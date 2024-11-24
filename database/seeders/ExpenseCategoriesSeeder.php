<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('expense_categories')->insert([
            [
                'name' => 'Office Supplies',
                'description' => 'Expenses related to office supplies and equipment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Travel',
                'description' => 'Expenses for business travel including flights and accommodation.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
