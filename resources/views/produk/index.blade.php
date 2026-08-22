@extends('adminlte::page')

@section('title', 'Produk - SRC Rully')

@section('content_header')
    <h1>Produk</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Produk</h3>
            @can('produk.kelola')
                <a href="{{ route('produk.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
            @endcan
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Satuan Dasar</th>
                        <th>Harga Beli</th>
                        <th>Stok</th>
                        <th>Status</th>
                        @can('produk.kelola')
                            <th></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td>{{ $product->baseUnit->name }} ({{ $product->baseUnit->symbol }})</td>
                            <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td>
                                {{ rtrim(rtrim(number_format($product->current_stock, 3, '.', ''), '0'), '.') }}
                                @if ($product->isLowStock())
                                    <span class="badge badge-warning">Menipis</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            @can('produk.kelola')
                                <td class="text-nowrap">
                                    <a href="{{ route('produk.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('produk.destroy', $product) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>
@stop
