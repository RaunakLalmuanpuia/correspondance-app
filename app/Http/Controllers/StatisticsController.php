<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\Issue;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /* ---------------------------------------------------------
     * ✅ 1. STAT CARD DATA
     * --------------------------------------------------------- */
    public function statCards(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        return response()->json(
            $this->getStatCardData($month, $year)
        );
    }

    private function getStatCardData($month = null, $year = null)
    {
        $issues = Issue::query();
        $receipts = Receipt::query();

        if ($month && $year) {
            $issues->whereYear('letter_date', $year)
                ->whereMonth('letter_date', $month);

            $receipts->whereYear('letter_date', $year)
                ->whereMonth('letter_date', $month);
        }

        $totalIssues = $issues->count();
        $totalReceipts = $receipts->count();

        return [
            'cells'          => Cell::count(),
            'issues'         => $totalIssues,
            'receipts'       => $totalReceipts,
            'correspondence' => $totalIssues + $totalReceipts,
        ];
    }

    /* ---------------------------------------------------------
     * ✅ 2. BAR CHART DATA
     * --------------------------------------------------------- */
    public function barChart(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        return response()->json(
            $this->getBarChartData($month, $year)
        );
    }

    private function getBarChartData($month = null, $year = null)
    {
        // Get cells
        $cells = Cell::select('id', 'name')->get();

        // Add a virtual cell for NULL
        $cells->push((object)[
            'id' => null,
            'name' => 'N/A'
        ]);

        // Issues
        $issuesQuery = Issue::query();
        if ($month && $year) {
            $issuesQuery->whereYear('letter_date', $year)
                ->whereMonth('letter_date', $month);
        }

        $issuesByCell = $issuesQuery
            ->select('cell_id', DB::raw("COUNT(*) as count"))
            ->groupBy('cell_id')
            ->pluck('count', 'cell_id');

        // Receipts
        $receiptsQuery = Receipt::query();
        if ($month && $year) {
            $receiptsQuery->whereYear('letter_date', $year)
                ->whereMonth('letter_date', $month);
        }

        $receiptsByCell = $receiptsQuery
            ->select('cell_id', DB::raw("COUNT(*) as count"))
            ->groupBy('cell_id')
            ->pluck('count', 'cell_id');

        return $cells->map(function ($cell) use ($issuesByCell, $receiptsByCell) {
            $cid = $cell->id; // could be null

            return [
                'name'     => $cell->name,
                'issues'   => $issuesByCell[$cid] ?? 0,
                'receipts' => $receiptsByCell[$cid] ?? 0,
            ];
        });
    }


    /* ---------------------------------------------------------
     * ✅ 3. TIMELINE DATA (NO FILTER REQUESTED)
     * --------------------------------------------------------- */
    public function timeline()
    {
        return response()->json(
            $this->getTimelineData()
        );
    }

    private function getTimelineData()
    {
        $issues = Issue::select(
            DB::raw("DATE_FORMAT(letter_date, '%Y-%m') as ym"),
            DB::raw("COUNT(*) as count")
        )
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('count', 'ym');

        $receipts = Receipt::select(
            DB::raw("DATE_FORMAT(letter_date, '%Y-%m') as ym"),
            DB::raw("COUNT(*) as count")
        )
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('count', 'ym');

        $allMonths = collect($issues->keys())
            ->merge($receipts->keys())
            ->unique()
            ->sort()
            ->values();

        return $allMonths->map(function ($ym) use ($issues, $receipts) {
            return [
                'month'    => $ym,
                'issues'   => $issues[$ym] ?? 0,
                'receipts' => $receipts[$ym] ?? 0,
                'total'    => ($issues[$ym] ?? 0) + ($receipts[$ym] ?? 0),
            ];
        });
    }
}
