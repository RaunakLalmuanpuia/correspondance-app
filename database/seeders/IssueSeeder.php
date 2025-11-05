<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\Issue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cells = \App\Models\Cell::all();

        for ($i = 1; $i <= 10; $i++) {
            // Generate multiple random departments (between 2 and 4)
            $copyTo = [];
            $count = rand(1, 4);

            for ($j = 1; $j <= $count; $j++) {
                $copyTo[] = 'Department ' . rand(1, 20);
            }

            Issue::create([
                'cell_id' => $cells->random()->id,
                'letter_addressee_main' => 'Officer ' . $i,
                'letter_addressee_copy_to' => json_encode($copyTo),
                'subject' => 'Regarding issue number ' . $i,
                'letter_no' => 'ISS-' . Str::upper(Str::random(6)),
                'letter_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
