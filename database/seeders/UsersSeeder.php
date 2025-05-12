<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@csu.edu.ph',
            'password' => Hash::make('password'),
            'department' => 'ICT',
            'position' => 'System Administrator',
        ]);
        $admin->assignRole('admin');
        
        // Create procurement officer
        $procurementOfficer = User::create([
            'name' => 'Procurement Officer',
            'email' => 'procurement@csu.edu.ph',
            'password' => Hash::make('password'),
            'department' => 'BAC Office',
            'position' => 'Procurement Officer',
        ]);
        $procurementOfficer->assignRole('procurement_officer');
        
        // Create budget approver
        $approver = User::create([
            'name' => 'Budget Approver',
            'email' => 'budget@csu.edu.ph',
            'password' => Hash::make('password'),
            'department' => 'Finance',
            'position' => 'Budget Officer',
        ]);
        $approver->assignRole('approver');
        
        // Create requestors
        $requestor1 = User::create([
            'name' => 'Faculty Requestor',
            'email' => 'faculty@csu.edu.ph',
            'password' => Hash::make('password'),
            'department' => 'Computer Science',
            'position' => 'Faculty',
        ]);
        $requestor1->assignRole('requestor');
        
        $requestor2 = User::create([
            'name' => 'Staff Requestor',
            'email' => 'staff@csu.edu.ph',
            'password' => Hash::make('password'),
            'department' => 'Registrar',
            'position' => 'Administrative Staff',
        ]);
        $requestor2->assignRole('requestor');
    }
}
