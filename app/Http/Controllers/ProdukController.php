<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdukRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'baseUnit'])
            ->orderBy('name')
            ->paginate(15);

        return view('produk.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('produk.create', compact('categories', 'units'));
    }

    public function store(StoreProdukRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $product = Product::create([
                ...$request->safe()->only(['category_id', 'sku', 'name', 'purchase_price', 'min_stock']),
                'base_unit_id' => $this->baseUnitId($request->validated('units')),
                'is_active' => $request->boolean('is_active', true),
                'current_stock' => 0,
            ]);

            $product->units()->createMany($this->normalizeUnits($request->validated('units')));
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $produk): View
    {
        $produk->load('units');
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('produk.edit', ['produk' => $produk, 'categories' => $categories, 'units' => $units]);
    }

    public function update(StoreProdukRequest $request, Product $produk): RedirectResponse
    {
        DB::transaction(function () use ($request, $produk) {
            $produk->update([
                ...$request->safe()->only(['category_id', 'sku', 'name', 'purchase_price', 'min_stock']),
                'base_unit_id' => $this->baseUnitId($request->validated('units')),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $produk->units()->delete();
            $produk->units()->createMany($this->normalizeUnits($request->validated('units')));
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $produk): RedirectResponse
    {
        if ($produk->stockMutations()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat stok/transaksi.');
        }

        $produk->units()->delete();
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function baseUnitId(array $units): int
    {
        $base = collect($units)->first(fn ($u) => filter_var($u['is_base'] ?? false, FILTER_VALIDATE_BOOLEAN));

        return (int) $base['unit_id'];
    }

    private function normalizeUnits(array $units): array
    {
        return collect($units)->map(fn ($u) => [
            'unit_id' => $u['unit_id'],
            'conversion_to_base' => filter_var($u['is_base'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : $u['conversion_to_base'],
            'sell_price' => $u['sell_price'],
            'is_base' => filter_var($u['is_base'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ])->all();
    }
}
