<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApplicationRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('application_roles')->insert([
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access and control over the system.',
            ],
            [
                'name' => 'Project Manager',
                'description' => 'Responsible for managing projects, assigning tasks, and monitoring progress.',
            ],
            [
                'name' => 'Normal User',
                'description' => 'Regular user with limited access to tasks and projects.',
            ],
        ]);
    }
}
