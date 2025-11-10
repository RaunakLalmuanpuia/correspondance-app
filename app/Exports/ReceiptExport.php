<?php

namespace App\Exports;

use App\Models\Issue;
use App\Models\Receipt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ReceiptExport implements FromView
{
    public function __construct(
        public ?int $year = null,
        public ?int $month = null,
        public bool $all = false
    ) {}

    public function view(): View
    {
        $query = Receipt::with('cell')->orderBy('s_no', 'desc');

        if (!$this->all && $this->year && $this->month) {
            $query->whereYear('received_date', $this->year)
                ->whereMonth('received_date', $this->month);
        }

        $receipts = $query->get();

        return view('exports.receipts', compact('receipts'));
    }
}
