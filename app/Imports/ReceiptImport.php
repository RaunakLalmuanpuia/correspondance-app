<?php

namespace App\Imports;

use App\Models\Receipt;
use App\Models\Cell;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ReceiptImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {

        $r = $row->toArray();

        // ✅ Convert Designated Cell → cell_id
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


        // ✅ Parse Letter Date
        $letterDate = $this->parseDate($r['letter_date'] ?? null);

        // ✅ Parse Received Date → created_at
        $receivedAt = $this->parseDateTime($r['received_date'] ?? null) ?? now();



        // ✅ Create receipt
        Receipt::create([
            'cell_id'       => $cellId,
            'subject'       => $r['subject'] ?? null,
            'letter_no'     => $r['letter_no'] ?? null,
            'letter_date'   => $letterDate,
            'received_from' => $r['received_from'] ?? null,
            'name_of_da'    => $r['name_of_da'] ?? null,
            'created_at'    => $receivedAt,
            'updated_at'    => now(),
        ]);
    }

    /**
     * ✅ Universal parser – handles:
     * - Excel serial numbers
     * - d/m/Y H:i
     * - d/m/Y h:i A
     * - normal dates
     */
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
