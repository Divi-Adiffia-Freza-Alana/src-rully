<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function penjualan(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now()->endOfDay();

        $sales = Sale::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = Sale::whereBetween('created_at', [$from, $to])
            ->selectRaw('COUNT(*) as total_transaksi, COALESCE(SUM(total), 0) as total_penjualan')
            ->first();

        return view('laporan.penjualan', [
            'sales' => $sales,
            'summary' => $summary,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);
    }
}
