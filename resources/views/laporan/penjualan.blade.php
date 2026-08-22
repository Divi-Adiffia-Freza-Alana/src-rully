@extends('adminlte::page')

@section('title', 'Laporan Penjualan - SRC Rully')

@section('content_header')
    <h1>Laporan Penjualan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-inline mb-3">
                <label class="mr-2">Dari</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control mr-3">
                <label class="mr-2">Sampai</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control mr-3">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-receipt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Transaksi</span>
                            <span class="info-box-number">{{ $summary->total_transaksi }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Penjualan</span>
                            <span class="info-box-number">Rp {{ number_format($summary->total_penjualan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('penjualan.nota', $sale) }}" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada transaksi pada rentang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sales->links() }}
        </div>
    </div>
@stop
