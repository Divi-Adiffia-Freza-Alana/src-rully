<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs', 'symbol' => 'pcs'],
            ['name' => 'Karton', 'symbol' => 'ktn'],
            ['name' => 'Lusin', 'symbol' => 'lsn'],
            ['name' => 'Liter', 'symbol' => 'ltr'],
            ['name' => 'Mililiter', 'symbol' => 'ml'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Gram', 'symbol' => 'gr'],
            ['name' => 'Dus', 'symbol' => 'dus'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
