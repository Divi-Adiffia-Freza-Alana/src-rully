@extends('adminlte::page')

@section('title', 'Pesanan Online - SRC Rully')

@section('content_header')
    <h1>Pesanan Online</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" class="form-inline">
                <label class="mr-2">Filter Status</label>
                <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach (['pending' => 'Pending', 'menunggu_validasi' => 'Menunggu Validasi', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Konsumen</th>
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
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->payment_method === 'transfer' ? 'Transfer' : 'COD' }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                            <td><a href="{{ route('pesanan.show', $order) }}" class="btn btn-sm btn-info">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan online.</td>
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
