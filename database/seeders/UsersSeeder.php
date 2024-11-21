<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $normalUserId = DB::table('users')->insertGetId([
            'name' => 'Jliew',
            'email' => 'jliew1114@gmail.com',
            'password' => Hash::make('123123'),
            'application_role_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminUserId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123123'),
            'application_role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
