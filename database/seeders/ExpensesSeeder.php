<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenseCategories = DB::table('expense_categories')->get(); // Fetch expense categories
        $projects = DB::table('projects')->get(); // Fetch projects
        $tasks = DB::table('tasks')->get(); // Fetch tasks
        $budget = DB::table('budgets')->first(); // Fetch the first budget for seeding projects

        DB::table('expenses')->insert([
            [
                'name' => 'Office Supplies Purchase',
                'expense_category_id' => $expenseCategories[0]->id,
                'project_id' => $projects[0]->id, // Project Alpha
                'task_id' => $tasks[0]->id, // Initial Planning
                'amount' => 150.00,
                'description' => 'Office supplies for initial setup.',
                'date_incurred' => '2024-01-05',
                'created_at' => now(),
                'updated_at' => now(),
                'budget_id' => $budget->id,
            ],
            [
                'name' => 'Flight for Team Lead',
                'expense_category_id' => $expenseCategories[1]->id,
                'project_id' => $projects[1]->id, // Project Beta
                'task_id' => $tasks[1]->id, // Market Research
                'amount' => 350.00,
                'description' => 'Flight ticket for business trip.',
                'date_incurred' => '2024-02-15',
                'created_at' => now(),
                'updated_at' => now(),
                'budget_id' => $budget->id,
            ]
        ]);

    }
}
