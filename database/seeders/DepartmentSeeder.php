<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology Department',
                'code' => 'IT',
                'description' => 'Handles all IT-related operations and support',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources Department',
                'code' => 'HR',
                'description' => 'Manages employee relations and recruitment',
                'is_active' => true,
            ],
            [
                'name' => 'Finance Department',
                'code' => 'FIN',
                'description' => 'Handles financial planning and reporting',
                'is_active' => true,
            ],
            [
                'name' => 'Procurement Department',
                'code' => 'PROC',
                'description' => 'Manages purchasing and supplier relationships',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Affairs Department',
                'code' => 'ACAD',
                'description' => 'Oversees academic programs and policies',
                'is_active' => true,
            ],
            [
                'name' => 'Research and Development Department',
                'code' => 'R&D',
                'description' => 'Conducts research projects and innovations',
                'is_active' => true,
            ],
            [
                'name' => 'Student Affairs Department',
                'code' => 'SA',
                'description' => 'Provides student support services',
                'is_active' => true,
            ],
            [
                'name' => 'Administration Department',
                'code' => 'ADMIN',
                'description' => 'Handles general administrative operations',
                'is_active' => true,
            ],
        ];
        
        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
