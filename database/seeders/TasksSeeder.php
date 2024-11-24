<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = DB::table('projects')->first(); // Fetch the first project for seeding tasks

        DB::table('tasks')->insert([
            [
                'project_id' => $project->id, // Project Alpha
                'name' => 'Initial Planning',
                'description' => 'Planning and resource allocation for the project.',
                'due_date' => '2024-01-10',
                'status' => 'Pending',
                'priority' => 'High',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $project->id, // Project Alpha
                'name' => 'Market Research',
                'description' => 'Conducting research on market trends.',
                'due_date' => '2024-02-01',
                'status' => 'Ongoing',
                'priority' => 'Medium',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
