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

        // ✅ Letter Date (Excel serial number → real date)
        $letterDate = null;
        if (!empty($r['letter_date'])) {
            try {
                // Excel date serialization
                $letterDate = $this->parseDate($r['letter_date'] ?? null);

            } catch (\Exception $e) {}
        }

        // ✅ Issue Date → created_at
        $receivedAt = now();
        if (!empty($r['created_time'])) {
            try {
                $receivedAt = $this->parseDateTime($r['created_time'] ?? null) ?? now();
            } catch (\Exception $e) {}
        }





        // ✅ Create receipt
        Receipt::create([
            's_no'          => $r['sl'],
            'cell_id'       => $cellId,
            'subject'       => $r['subject'] ?? null,
            'letter_no'     => $r['letter_no'] ?? null,
            'letter_date'   => $letterDate,
            'received_from' => $r['received_from'] ?? null,
            'name_of_da'    => $r['name_of_da'] ?? null,
            'received_date' => $receivedAt,
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
        if (empty($value)) {
            return null;
        }

        // ✅ Case 0: If Excel gave a DateTime object, return Carbon instance
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        // ✅ Case 1: Remove stray characters
        $value = trim($value, " \t\n\r\0\x0B\"");

        // ✅ Case 2: Numeric Excel serial
        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((float)$value);
        }

        // ✅ Case 3: Normalize
        $value = preg_replace('/\s+/', ' ', str_replace(['-', '.'], '/', $value));

        // ✅ Case 4: Supported formats
        $formats = [
            'F d, Y h:i A',
            'F d, Y H:i',
            'm/d/Y H:i',
            'm/d/Y g:i A',
            'd/m/Y H:i',
            'd/m/Y g:i A',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'm/d/Y',
            'd/m/Y'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {}
        }

        // ✅ Final fallback
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }


}
