<?php

namespace App\Http\Controllers;

use App\Exports\IssuesExport;
use App\Models\Cell;
use App\Models\Issue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Imports\IssueImport;
use Maatwebsite\Excel\Facades\Excel;
use const Exception;

class IssueController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-issue'), 403, 'Access Denied');
        return inertia('Backend/Issue/Index', [
        ]);
    }

    public function jsonAll(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-issue'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        $month = $request->get('month');
        $year = $request->get('year');

        $parsedDate = null;

        if ($filter) {
            // Normalize separators to hyphens
            $normalized = str_replace(['.', '/'], '-', $filter);

            // Try parsing using Carbon
            try {
                $parsedDate = \Carbon\Carbon::parse($normalized)->format('Y-m-d');
            } catch (\Exception $e) {
                $parsedDate = null;
            }
        }
        return response()->json([
            'list' => Issue::query()
                ->with(['cell'])

                ->when($filter, function ($builder) use ($filter, $parsedDate) {
                    $builder->where(function ($q) use ($filter, $parsedDate) {

                        // Text-based search
                        $q->where('subject', 'LIKE', "%$filter%")
                            ->orWhere('s_no', 'LIKE', "%$filter%")
                            ->orWhere('letter_no', 'LIKE', "%$filter%")
                            ->orWhere('letter_addressee_main', 'LIKE', "%$filter%")
                            ->orWhere('letter_addressee_copy_to', 'LIKE', "%$filter%");

                        // ✅ Date search (received_date)
                        if ($parsedDate) {
                            $q->orWhereDate('letter_date', $parsedDate)
                                ->orWhereDate('issue_date', $parsedDate); // add if needed
                        }
                    });
                })
                // ✅ Only apply month/year filter when NOT searching
                ->when(!$filter && $month && $year, function ($builder) use ($month, $year) {
                    return $builder->whereMonth('issue_date', $month)
                        ->whereYear('issue_date', $year);
                })
                ->orderBy('s_no', 'desc')
                ->paginate($perPage),
        ], 200);
    }

    public function create(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-issue'), 403, 'Access Denied');

        return inertia('Backend/Issue/Create', [
            'designated_cells' => Cell::query()->get(['id as value', 'name as label']),
        ]);
    }


    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-issue'), 403, 'Access Denied');

        $validated = $this->validate($request, [
            'cell_id' => ['nullable', Rule::exists('cells', 'id')],
            'subject' => 'required|string|max:255',
            'letter_addressee_main' => 'nullable|string',
            'letter_addressee_copy_to' => 'nullable|string',
            'letter_no' => 'required|string|unique:issues,letter_no|max:255',
            'letter_date' => 'nullable|date',
        ]);

        $issue = DB::transaction(function () use ($request) {

            // Lock table to ensure concurrency-safe increment
            $nextSno = DB::table('issues')
                ->lockForUpdate()                // Important for concurrency
                ->max('s_no');

            $nextSno = $nextSno ? $nextSno + 1 : 1;

            return Issue::create([
                's_no' => $nextSno,
                'cell_id' => $request->cell_id,
                'subject' => $request->subject,
                'letter_addressee_main' => $request->letter_addressee_main,
                'letter_addressee_copy_to' => $request->letter_addressee_copy_to,
                'letter_no' => $request->letter_no,
                'letter_date' => $request->letter_date,
                'issue_date' => now(),
            ]);
        });

        return to_route('issues.index');
    }

    public function edit(Request $request, Issue $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-issue'), 403, 'Access Denied');

        return inertia('Backend/Issue/Edit', [
            'data' => $model->load('cell'),
            'designated_cells' => Cell::query()->get(['id as value', 'name as label']),
        ]);
    }

    public function update(Request $request, Issue $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('edit-issue'), 403, 'Access Denied');

        $validated = $this->validate($request, [
            'cell_id' => ['nullable', Rule::exists('cells', 'id')],
            'subject' => 'required|string|max:255',
            'letter_addressee_main' => 'nullable|string',
            'letter_addressee_copy_to' => 'nullable|string',
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
        abort_if(!$user->hasPermissionTo('delete-issue'), 403, 'Access Denied');

        $model->delete();

        return to_route('issues.index');
    }


    public function import(Request $request)
    {
//        $user = $request->user();
//        abort_if(!$user->hasPermissionTo('view-issue'),403,'Access Denied');
        return inertia('Backend/Issue/Import', [
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

    public function export(Request $request)
    {
        return inertia('Backend/Issue/Export', [

        ]);
    }

    public function exportIssue(Request $request)
    {

        $export = new IssuesExport(
            year: $request->year,
            month: $request->month,
            all: $request->boolean('all')
        );
        return Excel::download($export, 'issue.xlsx');

    }


}
