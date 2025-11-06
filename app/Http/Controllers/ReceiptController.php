<?php

namespace App\Http\Controllers;

use App\Imports\ReceiptImport;
use App\Models\Cell;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ReceiptController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-receipt'),403,'Access Denied');


        return inertia('Backend/Receipt/Index',[
        ]);
    }

    public function jsonAll(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-receipt'),403,'Access Denied');



        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => Receipt::query()
                ->with(['cell'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }

    public function create(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-receipt'),403,'Access Denied');


        return inertia('Backend/Receipt/Create',[
            'designated_cells'=>Cell::query()->get(['id as value','name as label']),
        ]);
    }

    public function store(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-receipt'),403,'Access Denied');


        $validated=$this->validate($request, [

            'subject' => 'required|string|max:255',
            'letter_no' => 'required|string|unique:issues,letter_no|max:255',
            'letter_date' => 'nullable|date',
            'received_from' => 'nullable|string',
            'cell_id'=>['nullable',Rule::exists('cells','id')],
            'name_of_da'=>'nullable|string',

        ]);



        // ✅ Create issue record
        $receipt = Receipt::create([
            'subject' => $request->subject,
            'letter_no' => $request->letter_no,
            'letter_date' => $request->letter_date ? $request->letter_date  : now(),
            'received_from' => $request->received_from,
            'cell_id' => $request->cell_id,
            'name_of_da' => $request->name_of_da,
        ]);

        return to_route('receipts.index');
    }

    public function edit(Request $request, Receipt $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-receipt'),403,'Access Denied');


        return inertia('Backend/Receipt/Edit', [
            'data'=>$model->load('cell'),
            'designated_cells'=>Cell::query()->get(['id as value','name as label']),
        ]);
    }

    public function update(Request $request, Receipt $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-receipt'),403,'Access Denied');


        $validated=$this->validate($request, [
            'subject' => 'required|string|max:255',
            'letter_no' => 'required|string',
            'letter_date' => 'nullable|date',
            'received_from' => 'nullable|string',
            'cell_id'=>['nullable',Rule::exists('cells','id')],
            'name_of_da'=>'nullable|string',

        ]);
        DB::transaction(function () use ($model, $validated) {
            $model->update($validated);
        });

        return to_route('receipts.index');
    }

    public function destroy(Request $request, Receipt $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('delete-receipt'),403,'Access Denied');


        $model->delete();

        return to_route('receipts.index');
    }

    public function import(Request $request)
    {
//        $user = $request->user();
//        abort_if(!$user->hasPermissionTo('view-issue'),403,'Access Denied');
        return inertia('Backend/Receipt/Import',[
        ]);
    }

    public function importReceipt(Request $request)
    {

        $request->validate([
            'document' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ReceiptImport, $request->file('document'));

        return back()->with('success', 'Receipt imported successfully.');
    }

}
