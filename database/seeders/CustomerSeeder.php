<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::updateOrCreate(
            ['email' => 'konsumen@si-inventory.test'],
            [
                'name' => 'Konsumen Demo',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh Alamat No. 1, Jakarta',
                'password' => 'password',
            ]
        );
    }
}
