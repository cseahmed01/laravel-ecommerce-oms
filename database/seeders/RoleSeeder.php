<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles for API guard if they don't exist
        if (!Role::where('name', 'admin')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'api']);
        }
        if (!Role::where('name', 'vendor')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'vendor', 'guard_name' => 'api']);
        }
        if (!Role::where('name', 'customer')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'customer', 'guard_name' => 'api']);
        }
    }
}