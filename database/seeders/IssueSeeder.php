<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\Issue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cells = Cell::all();



        for ($i = 1; $i <= 10; $i++) {
            Issue::create([
                'cell_id' => $cells->random()->id,
                'letter_addressee_main' => 'Officer ' . $i,
                'letter_addressee_copy_to' => json_encode(['Department ' . $i]),
                'subject' => 'Regarding issue number ' . $i,
                'letter_no' => 'ISS-' . Str::upper(Str::random(6)),
                'letter_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
