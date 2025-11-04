<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $cells = Cell::all();

        for ($i = 1; $i <= 10; $i++) {
            Receipt::create([
                'cell_id' => $cells->random()->id,
                'subject' => 'Letter received about project ' . $i,
                'letter_no' => 'REC-' . Str::upper(Str::random(6)),
                'letter_date' => Carbon::now()->subDays(rand(1, 30)),
                'received_from' => 'Department ' . $i,
            ]);
        }
    }
}
