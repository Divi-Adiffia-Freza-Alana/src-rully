@extends('adminlte::page')

@section('title', 'Nota Penjualan - SI Inventory')

@section('content_header')
    <h1>Nota Penjualan</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <strong>No. Invoice:</strong> {{ $sale->invoice_no }}<br>
                    <strong>Tanggal:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Kasir:</strong> {{ $sale->user->name }}
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->unit->name }}</td>
                            <td>{{ rtrim(rtrim(number_format($item->qty, 3, '.', ''), '0'), '.') }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total</th>
                        <th>Rp {{ number_format($sale->total, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Bayar</th>
                        <th>Rp {{ number_format($sale->paid, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Kembalian</th>
                        <th>Rp {{ number_format($sale->change, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>

            <a href="{{ route('penjualan.create') }}" class="btn btn-primary">Transaksi Baru</a>
            <button onclick="window.print()" class="btn btn-default">Cetak</button>
        </div>
    </div>
@stop
