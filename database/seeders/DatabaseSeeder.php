<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        // Assign API roles to existing users
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $vendorUser = User::where('email', 'vendor@example.com')->first();
        if ($vendorUser && !$vendorUser->hasRole('vendor')) {
            $vendorUser->assignRole('vendor');
        }

        $customerUser = User::where('email', 'customer@example.com')->first();
        if ($customerUser && !$customerUser->hasRole('customer')) {
            $customerUser->assignRole('customer');
        }

        // Assign roles to vendor users created in ProductSeeder
        $vendorUsers = User::where('email', 'like', '%vendor%')->get();
        foreach ($vendorUsers as $user) {
            if (!$user->hasRole('vendor')) {
                $user->assignRole('vendor');
            }
        }

        // Assign roles to customer users created in OrderSeeder
        $customerUsers = User::where('email', 'like', '%customer%')->orWhere('email', 'like', '%@example.com%')->get();
        foreach ($customerUsers as $user) {
            if (!$user->hasRole('customer') && !$user->hasRole('admin') && !$user->hasRole('vendor')) {
                $user->assignRole('customer');
            }
        }
    }
}
