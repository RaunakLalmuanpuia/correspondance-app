<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Imports\IssueImport;
use Maatwebsite\Excel\Facades\Excel;
class IssueController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-issue'),403,'Access Denied');
        return inertia('Backend/Issue/Index',[
        ]);
    }

    public function jsonAll(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-issue'),403,'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => Issue::query()
                ->with(['cell'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }

    public function create(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-issue'),403,'Access Denied');

        return inertia('Backend/Issue/Create',[
            'designated_cells'=>Cell::query()->get(['id as value','name as label']),
        ]);
    }



    public function store(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-issue'),403,'Access Denied');

        $validated=$this->validate($request, [
            'cell_id'=>['nullable',Rule::exists('cells','id')],
            'subject' => 'required|string|max:255',
            'letter_addressee_main' => 'nullable|string',
            'letter_addressee_copy_to' => 'nullable|array',
            'letter_no' => 'required|string|unique:issues,letter_no|max:255',
            'letter_date' => 'nullable|date',

        ]);



        // ✅ Create issue record
        $issue = Issue::create([
            'cell_id' => $request->cell_id,
            'subject' => $request->subject,
            'letter_addressee_main' => $request->letter_addressee_main,
            'letter_addressee_copy_to' => $request->letter_addressee_copy_to ? json_encode($request->letter_addressee_copy_to) : null,
            'letter_no' => $request->letter_no,
            'letter_date' => $request->letter_date ? $request->letter_date  : now(),
        ]);

        return to_route('issues.index');
    }

    public function edit(Request $request, Issue $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-issue'),403,'Access Denied');

        return inertia('Backend/Issue/Edit', [
            'data'=>$model->load('cell'),
            'designated_cells'=>Cell::query()->get(['id as value','name as label']),
        ]);
    }

    public function update(Request $request, Issue $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-issue'),403,'Access Denied');

        $validated=$this->validate($request, [
            'cell_id'=>['nullable',Rule::exists('cells','id')],
            'subject' => 'required|string|max:255',
            'letter_addressee_main' => 'nullable|string',
            'letter_addressee_copy_to' => 'nullable|array',
            'letter_no' => 'required',
            'letter_date' => 'nullable|date',

        ]);
        DB::transaction(function () use ($model, $validated) {
            $model->update($validated);
        });

        return to_route('issues.index');
    }

    public function destroy(Request $request, Issue $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('delete-issue'),403,'Access Denied');

        $model->delete();

        return to_route('issues.index');
    }


    public function import(Request $request)
    {
//        $user = $request->user();
//        abort_if(!$user->hasPermissionTo('view-issue'),403,'Access Denied');
        return inertia('Backend/Issue/Import',[
        ]);
    }

    public function importIssue(Request $request)
    {

        $request->validate([
            'document' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new IssueImport, $request->file('document'));

        return back()->with('success', 'Issues imported successfully.');
    }


}
