<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $cells = [
            ['name' => 'Accounts', 'param1' => null, 'param2' => null],
            ['name' => 'DIC', 'param1' => null, 'param2' => null],
            ['name' => 'Establishment', 'param1' => null, 'param2' => null],
            ['name' => 'Technical (Monitoring)', 'param1' => null, 'param2' => null],
            ['name' => 'Technical (Urban)', 'param1' => null, 'param2' => null],
            ['name' => 'Technical (Rural)', 'param1' => null, 'param2' => null],
        ];

        DB::table('cells')->insert($cells);
    }
}
