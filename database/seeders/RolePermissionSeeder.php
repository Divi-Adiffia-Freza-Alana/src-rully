<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'produk.lihat' => 'Produk',
            'produk.kelola' => 'Produk',
            'stok.lihat' => 'Stok',
            'stok.kelola' => 'Stok',
            'penjualan.lihat' => 'Penjualan',
            'penjualan.kelola' => 'Penjualan',
            'laporan.lihat' => 'Laporan',
            'karyawan.lihat' => 'Karyawan',
            'karyawan.kelola' => 'Karyawan',
            'role.lihat' => 'Role',
            'role.kelola' => 'Role',
        ];

        foreach ($permissions as $slug => $group) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => $slug, 'group' => $group]
            );
        }

        $owner = Role::updateOrCreate(
            ['slug' => 'owner'],
            ['name' => 'Owner', 'is_system' => true]
        );
        $owner->permissions()->sync(Permission::pluck('id'));

        $kasir = Role::updateOrCreate(
            ['slug' => 'kasir'],
            ['name' => 'Kasir', 'is_system' => true]
        );
        $kasir->permissions()->sync(
            Permission::whereIn('slug', [
                'produk.lihat',
                'stok.lihat',
                'penjualan.lihat',
                'penjualan.kelola',
            ])->pluck('id')
        );
    }
}
