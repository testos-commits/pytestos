<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();

        Role::create(['name' => 'country admin']);
        Role::create(['name' => 'partner']);
        Role::create(['name' => 'director']);
        Role::create(['name' => 'super director']);
        Role::create(['name' => 'super admin']);
        Schema::enableForeignKeyConstraints();
    }
}
