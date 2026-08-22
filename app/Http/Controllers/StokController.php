<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStokMasukRequest;
use App\Http\Requests\StoreStokOpnameRequest;
use App\Models\Product;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StokController extends Controller
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function index(): View
    {
        $products = Product::with('baseUnit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('stok.index', compact('products'));
    }

    public function riwayat(Product $product): View
    {
        $mutations = $product->stockMutations()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('stok.riwayat', compact('product', 'mutations'));
    }

    public function storeMasuk(StoreStokMasukRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));

        $this->stockService->mutate(
            product: $product,
            qty: (float) $request->validated('qty'),
            type: 'in',
            userId: $request->user()->id,
            note: $request->validated('note'),
        );

        return back()->with('success', "Stok masuk untuk {$product->name} berhasil dicatat.");
    }

    public function storeOpname(StoreStokOpnameRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));
        $selisih = (float) $request->validated('physical_stock') - (float) $product->current_stock;

        if ($selisih === 0.0) {
            return back()->with('success', "Stok {$product->name} sudah sesuai, tidak ada perubahan.");
        }

        $this->stockService->mutate(
            product: $product,
            qty: $selisih,
            type: 'adjustment',
            userId: $request->user()->id,
            note: $request->validated('note') ?? 'Penyesuaian stok opname',
        );

        return back()->with('success', "Stok {$product->name} berhasil disesuaikan.");
    }
}
