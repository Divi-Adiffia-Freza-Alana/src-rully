<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Models\Order;
use App\Models\ProductUnit;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function create(): View
    {
        $customer = Auth::guard('customer')->user();
        $cart = app(CartController::class)->cartItemsWithDetails(request());

        return view('customer.checkout.create', [
            'items' => $cart,
            'total' => $cart->sum('subtotal'),
            'customer' => $customer,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $cartItems = app(CartController::class)->cartItemsWithDetails($request);

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Keranjang Anda kosong.');
        }

        $order = DB::transaction(function () use ($cartItems, $customer, $request) {
            $total = 0;
            $rows = [];

            foreach ($cartItems as $item) {
                $productUnit = ProductUnit::with('product')->findOrFail($item->product_unit_id);
                $baseQty = $item->qty * $productUnit->conversion_to_base;

                if ($productUnit->product->current_stock < $baseQty) {
                    throw new \RuntimeException("Stok {$productUnit->product->name} tidak mencukupi.");
                }

                $subtotal = $item->qty * $productUnit->sell_price;
                $total += $subtotal;

                $rows[] = [
                    'product' => $productUnit->product,
                    'unit_id' => $productUnit->unit_id,
                    'conversion_to_base' => $productUnit->conversion_to_base,
                    'qty' => $item->qty,
                    'price' => $productUnit->sell_price,
                    'subtotal' => $subtotal,
                ];
            }

            $order = $this->createOrderWithUniqueNumber(
                $customer->id,
                $request->validated('payment_method'),
                $total,
                $request->validated('shipping_address'),
                $request->validated('shipping_phone'),
            );

            foreach ($rows as $row) {
                $order->items()->create([
                    'product_id' => $row['product']->id,
                    'unit_id' => $row['unit_id'],
                    'conversion_to_base' => $row['conversion_to_base'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'subtotal' => $row['subtotal'],
                ]);

                $baseQty = $row['qty'] * $row['conversion_to_base'];

                $this->stockService->mutate(
                    product: $row['product'],
                    qty: -$baseQty,
                    type: 'sale',
                    note: "Pesanan online #{$order->order_no}",
                    referenceType: Order::class,
                    referenceId: $order->id,
                );
            }

            return $order;
        });

        session()->forget('cart_customer_'.$customer->id);

        return redirect()->route('customer.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    private function createOrderWithUniqueNumber(int $customerId, string $paymentMethod, float $total, string $address, string $phone): Order
    {
        $prefix = 'ORD-'.now()->format('Ymd').'-';
        $sequence = Order::whereDate('created_at', now())->count() + 1;

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Order::create([
                    'order_no' => $prefix.str_pad((string) ($sequence + $attempt), 4, '0', STR_PAD_LEFT),
                    'customer_id' => $customerId,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'total' => $total,
                    'shipping_address' => $address,
                    'shipping_phone' => $phone,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($attempt === 4) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Gagal membuat nomor pesanan.');
    }
}
