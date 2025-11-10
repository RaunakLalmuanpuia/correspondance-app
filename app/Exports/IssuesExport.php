<?php

namespace App\Exports;

use App\Models\Issue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class IssuesExport implements FromView
{
    public function __construct(
        public ?int $year = null,
        public ?int $month = null,
        public bool $all = false
    ) {}

    public function view(): View
    {
        $query = Issue::with('cell')->orderBy('s_no', 'desc');

        if (!$this->all && $this->year && $this->month) {
            $query->whereYear('issue_date', $this->year)
                ->whereMonth('issue_date', $this->month);
        }

        $issues = $query->get();

        return view('exports.issues', compact('issues'));
    }
}
