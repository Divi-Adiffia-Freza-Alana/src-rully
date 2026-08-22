<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::where('slug', 'owner')->firstOrFail();
        $kasirRole = Role::where('slug', 'kasir')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'owner@si-inventory.test'],
            [
                'role_id' => $ownerRole->id,
                'name' => 'Owner',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@si-inventory.test'],
            [
                'role_id' => $kasirRole->id,
                'name' => 'Kasir',
                'password' => 'password',
                'is_active' => true,
            ]
        );
    }
}
