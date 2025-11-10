<?php

namespace App\Http\Controllers;

use App\Exports\ReceiptExport;
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
        abort_if(!$user->hasPermissionTo('view-receipt'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter  = $request->get('filter');
        $month   = $request->get('month');
        $year    = $request->get('year');

        // ==========================================
        // ✅ UNIVERSAL DATE NORMALIZATION
        // Accepts: 01/11/2025, 01.11.2025, 01-11-2025, 1/11/25, etc.
        // ==========================================
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
            'list' => Receipt::query()
                ->with(['cell'])

                // ==========================================
                // ✅ SEARCH LOGIC
                // ==========================================
                ->when($filter, function ($builder) use ($filter, $parsedDate) {
                    $builder->where(function ($q) use ($filter, $parsedDate) {

                        // Text-based search
                        $q->where('subject', 'LIKE', "%$filter%")
                            ->orWhere('s_no', 'LIKE', "%$filter%")
                            ->orWhere('letter_no', 'LIKE', "%$filter%")
                            ->orWhere('received_from', 'LIKE', "%$filter%");

                        // ✅ Date search (received_date)
                        if ($parsedDate) {
                            $q->orWhereDate('received_date', $parsedDate)
                                ->orWhereDate('letter_date', $parsedDate); // add if needed
                        }
                    });
                })

                // ==========================================
                // ✅ MONTH/YEAR FILTER (only when NOT searching)
                // ==========================================
                ->when(!$filter && $month && $year, function ($builder) use ($month, $year) {
                    return $builder->whereMonth('letter_date', $month)
                        ->whereYear('letter_date', $year);
                })

                ->orderBy('s_no', 'desc')
                ->paginate($perPage),
        ], 200);
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
        abort_if(!$user->hasPermissionTo('create-receipt'), 403, 'Access Denied');

        $validated = $this->validate($request, [
            'subject' => 'required|string|max:255',
            'letter_no' => 'required|string|max:255',
            'letter_date' => 'nullable|date',
            'received_from' => 'nullable|string',
            'cell_id'=>['nullable', Rule::exists('cells','id')],
            'name_of_da'=>'nullable|string',
        ]);

        $receipt = DB::transaction(function () use ($request) {

            // Lock table rows for sequence generation
            $nextSno = DB::table('receipts')
                ->lockForUpdate()
                ->max('s_no');

            $nextSno = $nextSno ? $nextSno + 1 : 1;

            // Create the receipt
            return Receipt::create([
                's_no' => $nextSno,
                'subject' => $request->subject,
                'letter_no' => $request->letter_no,
                'letter_date' => $request->letter_date ?: now(),
                'received_from' => $request->received_from,
                'cell_id' => $request->cell_id,
                'name_of_da' => $request->name_of_da,
                'received_date' => now(),
            ]);
        });

        return to_route('receipts.index');
    }

    public function edit(Request $request, Receipt $model)
    {

//        dd($model->load('cell'));
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

    public function export(Request $request){
        return inertia('Backend/Receipt/Export',[

        ]);
    }

    public function exportReceipt(Request $request)
    {

        $export = new ReceiptExport(
            year: $request->year,
            month: $request->month,
            all: $request->boolean('all')
        );
        return Excel::download($export, 'receipt.xlsx');

    }
}
