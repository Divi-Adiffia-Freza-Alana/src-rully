<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->cartItemsWithDetails($request);

        return view('customer.cart.index', ['items' => $cart, 'total' => $cart->sum('subtotal')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_unit_id' => ['required', 'exists:product_units,id'],
            'qty' => ['required', 'numeric', 'min:0.001'],
        ]);

        $productUnit = ProductUnit::with('product')->findOrFail($request->input('product_unit_id'));
        $key = $this->cartKey($request);
        $cart = session($key, []);

        $unitId = (string) $productUnit->id;
        $qty = (float) $request->input('qty');

        $cart[$unitId] = ($cart[$unitId] ?? 0) + $qty;

        session([$key => $cart]);

        return back()->with('success', "{$productUnit->product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request, int $productUnitId): RedirectResponse
    {
        $request->validate(['qty' => ['required', 'numeric', 'min:0.001']]);

        $key = $this->cartKey($request);
        $cart = session($key, []);
        $cart[(string) $productUnitId] = (float) $request->input('qty');

        session([$key => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Request $request, int $productUnitId): RedirectResponse
    {
        $key = $this->cartKey($request);
        $cart = session($key, []);
        unset($cart[(string) $productUnitId]);

        session([$key => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    private function cartKey(Request $request): string
    {
        return 'cart_customer_'.Auth::guard('customer')->id();
    }

    public function cartItemsWithDetails(Request $request)
    {
        $key = $this->cartKey($request);
        $cart = session($key, []);

        if (empty($cart)) {
            return collect();
        }

        $productUnits = ProductUnit::with(['product.baseUnit', 'unit'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function ($qty, $productUnitId) use ($productUnits) {
            $productUnit = $productUnits->get((int) $productUnitId);

            if (! $productUnit) {
                return null;
            }

            return (object) [
                'product_unit_id' => $productUnit->id,
                'product' => $productUnit->product,
                'unit' => $productUnit->unit,
                'qty' => $qty,
                'price' => $productUnit->sell_price,
                'subtotal' => $qty * $productUnit->sell_price,
            ];
        })->filter()->values();
    }
}
