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

            // ✅ Normalize parentheses: "Technical(Rural)" → "Technical (Rural)"
            $normalized = preg_replace('/\(/', ' (', trim($r['designated_cell']));

            // ✅ Lowercase and safe matching
            $normalized = strtolower($normalized);

            $cell = Cell::whereRaw('LOWER(name) LIKE ?', ["%{$normalized}%"])->first();

            if ($cell) {
                $cellId = $cell->id;
            }
        }


        // ✅ Address Main
        $addressMain = $r['addressmain'] ?? null;

        $copyTo = $r['addresscopy_to'] ?? null;

//        // ✅ Address Copy to (split by comma)
//        $copyTo = null;
//        if (!empty($r['addresscopy_to'])) {
//            $arr = preg_split("/,|\r\n|\n|\r/", $r['addresscopy_to']);
//            $arr = array_filter(array_map('trim', $arr));
//            $copyTo = json_encode($arr);
//        }

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
                $createdAt = $this->parseDate($r['issue_date'] ?? null) ?? now();
            } catch (\Exception $e) {}
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

        $value = trim($value);

        // ✅ Case 1: Excel numeric serial
        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)
                ->addDays((int) $value)
                ->format('Y-m-d');
        }

        // ✅ Case 2: Parse date string with known formats
        $formats = [
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // ✅ Final fallback
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
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
