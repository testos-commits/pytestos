<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Clear existing permissions to avoid duplicates
        Permission::truncate();

        // Additional custom permissions for specific business logic
        $customPermissions = [
            'access admin panel',
            'access talent messages',
            'change application status',
            'create audition',
            'create project',
            'delete audition',
            'delete project',
            'edit audition',
            'edit projects',
            'edit talent profile',
            'manage accounting',
            'manage director projects',
            'set audition budget',
            'shortlist applicant',
            'view accepted applicants',
            'view applicants',
            'view audition administrative details',
            'view profiles',
            'view referrals',
            'view talent description',
            'view talent full name',
            'view talent first name',
            'view talent phone details',
            'view talent previous commercials'
        ];

        // Create custom permissions
        foreach ($customPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
