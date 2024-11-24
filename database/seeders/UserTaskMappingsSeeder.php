<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTaskMappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get(); // Fetch all users
        $tasks = DB::table('tasks')->get(); // Fetch tasks

        DB::table('user_task_mappings')->insert([
            [
                'task_id' => $tasks[0]->id, // Initial Planning
                'user_id' => $users[0]->id, // Admin
                'assigned_by' => $users[1]->id, // Normal User (Assigned by)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_id' => $tasks[1]->id, // Market Research
                'user_id' => $users[1]->id, // Normal User
                'assigned_by' => $users[0]->id, // Admin (Assigned by)
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
