<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        // Get all roles
        $superAdminRole = Role::where('name', 'super admin')->first();
        $countryAdminRole = Role::where('name', 'country admin')->first();
        $partnerRole = Role::where('name', 'partner')->first();
        $directorRole = Role::where('name', 'director')->first();
        $superDirectorRole = Role::where('name', 'super director')->first();

        // Create roles if they don't exist
        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'super admin', 'guard_name' => 'web']);
        }
        if (!$countryAdminRole) {
            $countryAdminRole = Role::create(['name' => 'country admin', 'guard_name' => 'web']);
        }
        if (!$partnerRole) {
            $partnerRole = Role::create(['name' => 'partner', 'guard_name' => 'web']);
        }
        if (!$directorRole) {
            $directorRole = Role::create(['name' => 'director', 'guard_name' => 'web']);
        }
        if (!$superDirectorRole) {
            $superDirectorRole = Role::create(['name' => 'super director', 'guard_name' => 'web']);
        }

        // Clear existing role permissions
        $superAdminRole->permissions()->detach();
        $countryAdminRole->permissions()->detach();
        $partnerRole->permissions()->detach();
        $directorRole->permissions()->detach();
        $superDirectorRole->permissions()->detach();

        // SUPER ADMIN PERMISSIONS (is_admin && !isCountryAdmin())
        $superAdminPermissions = [
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
            'view talent phone details',
            'view talent previous commercials'
        ];

        // COUNTRY ADMIN PERMISSIONS (is_admin && isCountryAdmin())
        $countryAdminPermissions = [
            'access admin panel',
            'access talent messages',
            'change application status',
            'create audition',
            'create project',
            'delete audition',
            'delete project',
            'edit audition',
            'edit projects',
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
            'view talent phone details',
            'view talent previous commercials',
        ];

        // PARTNER PERMISSIONS (is_partner)
        $partnerPermissions = [
            'view profiles',
            'view talent phone details',
            'view talent full name',
            'change application status',
            'view audition administrative details',
            'access talent messages',
            'view applicants',
            'view accepted applicants',
            'view talent previous commercials',
            'manage accounting',
            'edit projects',
            'access admin panel',
            'manage director projects',
            'view talent description',
            'shortlist applicant',
            'delete project',
            'delete audition',
            'edit audition',
            'create audition',
            'set audition budget',
            'view referrals',
            'create project',
            'view talent first name',

        ];

        // DIRECTOR PERMISSIONS (is_director)
        $directorPermissions = [
            'access admin panel'
        ];

        // SUPER DIRECTOR PERMISSIONS (enhanced director)
        $superDirectorPermissions = [
            'access talent messages',
            'view applicants',
            'view accepted applicants',
            'access admin panel',
            'shortlist applicant',
            'create audition',
            'create project',
        ];

        // Assign permissions to roles
        $superAdminRole->givePermissionTo($superAdminPermissions);
        $countryAdminRole->givePermissionTo($countryAdminPermissions);
        $partnerRole->givePermissionTo($partnerPermissions);
        $directorRole->givePermissionTo($directorPermissions);
        $superDirectorRole->givePermissionTo($superDirectorPermissions);

        // Assign roles to existing users based on their flags
        $this->assignRolesToExistingUsers();

        Schema::enableForeignKeyConstraints();
    }

    private function assignRolesToExistingUsers()
    {
        // Sync roles for existing users based on their boolean flags
        $users = User::all();

        foreach ($users as $user) {
            // Clear existing roles
            $user->roles()->detach();

            // Assign roles based on flags
            if ($user->is_admin) {
                if ($user->isCountryAdmin()) {
                    $user->assignRole('country admin');
                } else {
                    $user->assignRole('super admin');
                }
            }

            if ($user->is_partner) {
                $user->assignRole('partner');
            }

            if ($user->is_director) {
                // You can add logic here to determine if they should be super_director
                // For now, assigning regular director role
                $user->assignRole('director');
            }
        }
    }
}
