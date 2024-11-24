<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserProjectMappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get(); // Fetch all users for project mapping
        $projects = DB::table('projects')->get(); // Fetch all projects
        $projectRoles = DB::table('project_roles')->get(); // Fetch all project roles

        DB::table('user_project_mappings')->insert([
            [
                'user_id' => $users[0]->id, // Admin
                'project_id' => $projects[0]->id, // Project Alpha
                'project_role_id' => $projectRoles[0]->id, // Team Lead
                'remark' => 'Project manager overseeing initial development.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[1]->id, // Normal User
                'project_id' => $projects[1]->id, // Project Beta
                'project_role_id' => $projectRoles[2]->id, // Junior
                'remark' => 'Assisting with minor tasks and support.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
