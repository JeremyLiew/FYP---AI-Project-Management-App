<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BudgetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('budgets')->insert([
            [
                'name' => 'General Budget',
                'total_budget' => 500000.00,
                'remaining_amount' => 300000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing Budget',
                'total_budget' => 100000.00,
                'remaining_amount' => 40000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
