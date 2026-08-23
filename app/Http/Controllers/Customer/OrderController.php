<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UploadProofRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Auth::guard('customer')->user()
            ->orders()
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorizeOwnership($order);

        $order->load('items.product', 'items.unit');

        return view('customer.orders.show', compact('order'));
    }

    public function uploadProof(UploadProofRequest $request, Order $order): RedirectResponse
    {
        $this->authorizeOwnership($order);

        if ($order->payment_method !== 'transfer' || $order->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat diunggah bukti transfernya.');
        }

        $path = $request->file('proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof_path' => $path,
            'status' => 'menunggu_validasi',
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah, menunggu validasi kasir.');
    }

    private function authorizeOwnership(Order $order): void
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
    }
}
