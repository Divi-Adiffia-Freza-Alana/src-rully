<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProduk = Product::where('is_active', true)->count();
        $stokMenipis = Product::where('is_active', true)->whereColumn('current_stock', '<=', 'min_stock')->count();

        $todaySales = Sale::whereDate('created_at', now())
            ->select([
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('COALESCE(SUM(total), 0) as total_penjualan'),
            ])
            ->first();

        return view('dashboard', [
            'totalProduk' => $totalProduk,
            'stokMenipis' => $stokMenipis,
            'penjualanHariIni' => $todaySales->total_penjualan,
            'transaksiHariIni' => $todaySales->total_transaksi,
        ]);
    }
}
