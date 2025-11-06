<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::query()->upsert([
            ['id'=>1,'name'=>'admin','email'=>'admin@mail.com','designation'=>'admin','password'=>Hash::make('password')],
            ['id'=>2,'name'=>'dealing','email'=>'dealing@mail.com','designation'=>'dealing','password'=>Hash::make('password')],
            ['id'=>3,'name'=>'viewer','email'=>'viewer@mail.com','designation'=>'viewer','password'=>Hash::make('password')],
        ], ['id']);


        $admin=User::query()->find(1);
        $admin->assignRole('Admin');

        $manager=User::query()->find(2);
        $manager->assignRole('Dealing');

        $manager=User::query()->find(3);
        $manager->assignRole('Viewer');
    }
}
