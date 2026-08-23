@extends('adminlte::page')

@section('title', 'Pesanan ' . $order->order_no . ' - SRC Rully')

@section('content_header')
    <h1>Pesanan {{ $order->order_no }}</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Item Pesanan</strong>
                    <span class="badge badge-{{ $order->statusColor() }} p-2">{{ $order->statusLabel() }}</span>
                </div>
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

            <div class="card mb-3">
                <div class="card-header"><strong>Konsumen &amp; Pengiriman</strong></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nama:</strong> {{ $order->customer->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $order->customer->email }}</p>
                    <p class="mb-1"><strong>Alamat:</strong> {{ $order->shipping_address }}</p>
                    <p class="mb-0"><strong>No. HP:</strong> {{ $order->shipping_phone }}</p>
                </div>
            </div>

            @if ($order->payment_method === 'transfer' && $order->payment_proof_path)
                <div class="card">
                    <div class="card-header"><strong>Bukti Transfer</strong></div>
                    <div class="card-body">
                        <img src="{{ Storage::url($order->payment_proof_path) }}" alt="Bukti transfer" class="img-fluid rounded border">
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><strong>Aksi</strong></div>
                <div class="card-body">
                    <p><strong>Metode:</strong> {{ $order->payment_method === 'transfer' ? 'Transfer Bank' : 'Bayar di Tempat (COD)' }}</p>

                    @can('pesanan.kelola')
                        @if ($order->payment_method === 'cod' && $order->status === 'pending')
                            <form action="{{ route('pesanan.confirm-cod', $order) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">Konfirmasi Pesanan COD</button>
                            </form>
                        @endif

                        @if ($order->status === 'menunggu_validasi')
                            @if ($order->payment_method === 'transfer' && ! $order->payment_proof_path)
                                <div class="alert alert-warning">Menunggu konsumen mengunggah bukti transfer.</div>
                            @else
                                <form action="{{ route('pesanan.validate', $order) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block">Validasi &amp; Proses Pesanan</button>
                                </form>
                            @endif
                        @endif

                        @if ($order->status === 'diproses')
                            <form action="{{ route('pesanan.ship', $order) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">Tandai Dikirim</button>
                            </form>
                        @endif

                        @if ($order->status === 'dikirim')
                            <form action="{{ route('pesanan.complete', $order) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">Tandai Selesai</button>
                            </form>
                        @endif

                        @if (! in_array($order->status, ['selesai', 'dibatalkan']))
                            <hr>
                            <form action="{{ route('pesanan.cancel', $order) }}" method="POST"
                                  onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                                @csrf
                                <div class="form-group">
                                    <label>Alasan Pembatalan</label>
                                    <input type="text" name="cancel_reason" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block">Batalkan Pesanan</button>
                            </form>
                        @endif
                    @endcan

                    @if ($order->status === 'selesai')
                        <div class="alert alert-success mb-0">Pesanan telah selesai.</div>
                    @endif

                    @if ($order->validated_at)
                        <p class="text-muted small mt-3 mb-0">
                            Divalidasi oleh {{ $order->validator?->name ?? '-' }} pada {{ $order->validated_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
