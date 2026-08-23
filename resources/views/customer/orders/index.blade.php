@extends('layouts.customer')

@section('title', 'Pesanan Saya - SRC Rully')

@section('content')
    <h4 class="mb-4">Pesanan Saya</h4>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->order_no }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $order->payment_method === 'transfer' ? 'Transfer Bank' : 'COD' }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                            <td><a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-info">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Anda belum memiliki pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
@stop
