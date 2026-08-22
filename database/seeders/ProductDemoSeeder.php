<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $minuman = Category::updateOrCreate(['name' => 'Minuman']);
        $sembako = Category::updateOrCreate(['name' => 'Sembako']);

        $pcs = Unit::where('symbol', 'pcs')->firstOrFail();
        $karton = Unit::where('symbol', 'ktn')->firstOrFail();
        $kg = Unit::where('symbol', 'kg')->firstOrFail();
        $gr = Unit::where('symbol', 'gr')->firstOrFail();

        $teh = Product::updateOrCreate(
            ['sku' => 'MNM-001'],
            [
                'category_id' => $minuman->id,
                'base_unit_id' => $pcs->id,
                'name' => 'Teh Botol 350ml',
                'purchase_price' => 3000,
                'current_stock' => 240,
                'min_stock' => 24,
                'is_active' => true,
            ]
        );
        $teh->units()->delete();
        $teh->units()->createMany([
            ['unit_id' => $pcs->id, 'conversion_to_base' => 1, 'sell_price' => 4000, 'is_base' => true],
            ['unit_id' => $karton->id, 'conversion_to_base' => 24, 'sell_price' => 90000, 'is_base' => false],
        ]);

        $beras = Product::updateOrCreate(
            ['sku' => 'SMB-001'],
            [
                'category_id' => $sembako->id,
                'base_unit_id' => $gr->id,
                'name' => 'Beras Premium',
                'purchase_price' => 12000,
                'current_stock' => 50000,
                'min_stock' => 5000,
                'is_active' => true,
            ]
        );
        $beras->units()->delete();
        $beras->units()->createMany([
            ['unit_id' => $gr->id, 'conversion_to_base' => 1, 'sell_price' => 15, 'is_base' => true],
            ['unit_id' => $kg->id, 'conversion_to_base' => 1000, 'sell_price' => 14000, 'is_base' => false],
        ]);
    }
}
