<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budget = DB::table('budgets')->first(); // Fetch the first budget for seeding projects

        DB::table('projects')->insert([
            [
                'name' => 'Project Alpha',
                'description' => 'This is a project management tool web application.',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => 'Pending',
                'priority' => 'Low',
                'budget_id' => $budget->id, // Linking with a budget
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project Beta',
                'description' => 'This is a music composition project for pop music.',
                'start_date' => '2024-02-01',
                'end_date' => '2024-11-30',
                'status' => 'Ongoing',
                'priority' => 'Medium',
                'budget_id' => $budget->id, // Linking with the same budget
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
