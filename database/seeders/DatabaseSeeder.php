<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProjectRolesSeeder::class,
            ApplicationRolesSeeder::class,
            UsersSeeder::class,
            BudgetsSeeder::class,
            ProjectsSeeder::class,
            UserProjectMappingsSeeder::class,
            TasksSeeder::class,
            ExpenseCategoriesSeeder::class,
            ExpensesSeeder::class,
            UserTaskMappingsSeeder::class,
        ]);
    }
}
