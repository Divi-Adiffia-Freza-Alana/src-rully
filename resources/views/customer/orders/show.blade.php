@extends('layouts.customer')

@section('title', 'Detail Pesanan ' . $order->order_no . ' - SRC Rully')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Pesanan {{ $order->order_no }}</h4>
        <span class="badge badge-{{ $order->statusColor() }} p-2">{{ $order->statusLabel() }}</span>
    </div>

    @if ($order->status === 'dibatalkan' && $order->cancel_reason)
        <div class="alert alert-danger">
            <strong>Pesanan dibatalkan.</strong> Alasan: {{ $order->cancel_reason }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header"><strong>Item Pesanan</strong></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }} <span class="text-muted">({{ $item->unit->name }})</span></td>
                                    <td class="text-center">{{ rtrim(rtrim(number_format($item->qty, 3, '.', ''), '0'), '.') }}x</td>
                                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Info Pengiriman</strong></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Alamat:</strong> {{ $order->shipping_address }}</p>
                    <p class="mb-0"><strong>No. HP:</strong> {{ $order->shipping_phone }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><strong>Pembayaran</strong></div>
                <div class="card-body">
                    <p><strong>Metode:</strong> {{ $order->payment_method === 'transfer' ? 'Transfer Bank' : 'Bayar di Tempat (COD)' }}</p>

                    @if ($order->payment_method === 'transfer')
                        @if ($order->payment_proof_path)
                            <p class="mb-2"><strong>Bukti Transfer:</strong></p>
                            <img src="{{ Storage::url($order->payment_proof_path) }}" alt="Bukti transfer" class="img-fluid rounded border mb-2">
                        @endif

                        @if ($order->status === 'pending')
                            <form action="{{ route('customer.orders.upload-proof', $order) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label>Unggah Bukti Transfer</label>
                                    <input type="file" name="proof" accept="image/*" class="form-control-file" required>
                                    @error('proof') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Unggah Bukti</button>
                            </form>
                        @endif
                    @else
                        <p class="text-muted small mb-0">Siapkan pembayaran tunai saat kurir mengantarkan pesanan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
