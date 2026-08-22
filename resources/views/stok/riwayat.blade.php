@extends('adminlte::page')

@section('title', 'Riwayat Stok - SRC Rully')

@section('content_header')
    <h1>Riwayat Stok: {{ $product->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('stok.index') }}" class="btn btn-sm btn-default">&larr; Kembali</a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Stok Sebelum</th>
                        <th>Stok Sesudah</th>
                        <th>Catatan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mutations as $mutation)
                        <tr>
                            <td>{{ $mutation->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @switch($mutation->type)
                                    @case('in') <span class="badge badge-success">Stok Masuk</span> @break
                                    @case('out') <span class="badge badge-danger">Stok Keluar</span> @break
                                    @case('sale') <span class="badge badge-primary">Penjualan</span> @break
                                    @case('adjustment') <span class="badge badge-warning">Penyesuaian</span> @break
                                @endswitch
                            </td>
                            <td class="{{ $mutation->qty >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $mutation->qty >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($mutation->qty, 3, '.', ''), '0'), '.') }}
                            </td>
                            <td>{{ rtrim(rtrim(number_format($mutation->stock_before, 3, '.', ''), '0'), '.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($mutation->stock_after, 3, '.', ''), '0'), '.') }}</td>
                            <td>{{ $mutation->note ?? '-' }}</td>
                            <td>{{ $mutation->user?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $mutations->links() }}
        </div>
    </div>
@stop
