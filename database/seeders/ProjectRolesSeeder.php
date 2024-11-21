<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('project_roles')->insert([
            [
                'name' => 'Team Lead',
                'description' => 'The person responsible for leading the team and overseeing the project.',
            ],
            [
                'name' => 'Senior',
                'description' => 'A senior member with extensive experience in the project and technical areas.',
            ],
            [
                'name' => 'Junior',
                'description' => 'A junior member with basic experience, usually assisting senior members.',
            ],
            [
                'name' => 'Intern',
                'description' => 'A member still in the learning phase, typically with little to no experience.',
            ],
        ]);
    }
}
