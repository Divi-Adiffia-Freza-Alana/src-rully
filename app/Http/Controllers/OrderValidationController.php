<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderValidationController extends Controller
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function index(Request $request): View
    {
        $orders = Order::with('customer')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pesanan.index', compact('orders'));
    }

    public function show(Order $pesanan): View
    {
        $pesanan->load('items.product', 'items.unit', 'customer', 'validator');

        return view('pesanan.show', ['order' => $pesanan]);
    }

    public function confirmCod(Request $request, Order $pesanan): RedirectResponse
    {
        if ($pesanan->payment_method !== 'cod' || $pesanan->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat dikonfirmasi.');
        }

        $pesanan->update(['status' => 'menunggu_validasi']);

        return back()->with('success', "Pesanan COD {$pesanan->order_no} dikonfirmasi, menunggu validasi akhir.");
    }

    public function validate(Request $request, Order $pesanan): RedirectResponse
    {
        if ($pesanan->status !== 'menunggu_validasi') {
            return back()->with('error', 'Pesanan ini tidak lagi dapat divalidasi.');
        }

        if ($pesanan->payment_method === 'transfer' && ! $pesanan->payment_proof_path) {
            return back()->with('error', 'Konsumen belum mengunggah bukti transfer.');
        }

        $pesanan->update([
            'status' => 'diproses',
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        return back()->with('success', "Pesanan {$pesanan->order_no} berhasil divalidasi dan diproses.");
    }

    public function ship(Order $pesanan): RedirectResponse
    {
        if ($pesanan->status !== 'diproses') {
            return back()->with('error', 'Pesanan harus berstatus Diproses sebelum dikirim.');
        }

        $pesanan->update(['status' => 'dikirim']);

        return back()->with('success', "Pesanan {$pesanan->order_no} ditandai sedang dikirim.");
    }

    public function complete(Order $pesanan): RedirectResponse
    {
        if ($pesanan->status !== 'dikirim') {
            return back()->with('error', 'Pesanan harus berstatus Dikirim sebelum diselesaikan.');
        }

        $pesanan->update(['status' => 'selesai']);

        return back()->with('success', "Pesanan {$pesanan->order_no} ditandai selesai.");
    }

    public function cancel(Request $request, Order $pesanan): RedirectResponse
    {
        if (in_array($pesanan->status, ['selesai', 'dibatalkan'], true)) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        $request->validate(['cancel_reason' => ['required', 'string', 'max:255']]);

        foreach ($pesanan->items as $item) {
            $product = Product::find($item->product_id);

            if ($product) {
                $baseQty = $item->qty * $item->conversion_to_base;

                $this->stockService->mutate(
                    product: $product,
                    qty: $baseQty,
                    type: 'in',
                    userId: $request->user()->id,
                    note: "Pembatalan pesanan online #{$pesanan->order_no}",
                    referenceType: Order::class,
                    referenceId: $pesanan->id,
                );
            }
        }

        $pesanan->update([
            'status' => 'dibatalkan',
            'cancel_reason' => $request->input('cancel_reason'),
        ]);

        return back()->with('success', "Pesanan {$pesanan->order_no} dibatalkan, stok telah dikembalikan.");
    }
}
