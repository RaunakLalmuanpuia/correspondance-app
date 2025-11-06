<?php

namespace App\Imports;

use App\Models\Issue;
use App\Models\Cell;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IssueImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $r = $row->toArray();

        // ✅ Get cell id
        $cellId = null;
        if (!empty($r['designated_cell'])) {
            $cell = Cell::where('name', 'LIKE', '%' . trim($r['designated_cell']) . '%')->first();
            if ($cell) $cellId = $cell->id;
        }

        // ✅ Address Main
        $addressMain = $r['addressmain'] ?? null;

        // ✅ Address Copy to (split by comma)
        $copyTo = null;
        if (!empty($r['addresscopy_to'])) {
            $arr = preg_split("/,|\r\n|\n|\r/", $r['addresscopy_to']);
            $arr = array_filter(array_map('trim', $arr));
            $copyTo = json_encode($arr);
        }

        // ✅ Letter Date (Excel serial number → real date)
        $letterDate = null;
        if (!empty($r['letter_date'])) {
            try {
                // Excel date serialization
                $letterDate = $this->parseDate($r['letter_date'] ?? null);

            } catch (\Exception $e) {}
        }

        // ✅ Issue Date → created_at
        $createdAt = now();
        if (!empty($r['issue_date'])) {
            try {
                $createdAt = $this->parseDateTime($r['issue_date'] ?? null) ?? now();
            } catch (\Exception $e) {}
        }

        // ✅ Skip duplicate letter_no
        if (!empty($r['letter_no']) && Issue::where('letter_no', $r['letter_no'])->exists()) {
            return;
        }

        // ✅ Create Issue
        Issue::create([
            'cell_id'                   => $cellId,
            'subject'                   => $r['subject'] ?? null,
            'letter_addressee_main'     => $addressMain,
            'letter_addressee_copy_to'  => $copyTo,
            'letter_no'                 => $r['letter_no'] ?? null,
            'letter_date'               => $letterDate,
            'created_at'                => $createdAt,
            'updated_at'                => now()
        ]);
    }

    private function parseDate($value)
    {
        if (empty(trim($value))) {
            return null;
        }

        // ✅ Case 1: Excel numeric serial
        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->format('Y-m-d');
        }

        // ✅ Case 2: Normal date string ("25/8/2025", "25-08-2025", etc.)
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {}

        return null;
    }
    private function parseDateTime($value)
    {
        if (empty(trim($value))) {
            return null;
        }

        $value = trim($value);

        // ✅ CASE 1: Excel serial number (int or float)
        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)
                ->addDays((float)$value);
        }

        // ✅ CASE 2: Date + Time (AM/PM or 24-hr format)
        $formats = [
            'd/m/Y g:i A',
            'd/m/Y g:iA',
            'd/m/Y h:i A',
            'd/m/Y h:iA',
            'd/m/Y H:i',
            'd-m-Y g:i A',
            'd-m-Y H:i',
            'd/m/Y',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {}
        }

        // ✅ Last fallback
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

}
