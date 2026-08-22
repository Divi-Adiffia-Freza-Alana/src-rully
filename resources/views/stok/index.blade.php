@extends('adminlte::page')

@section('title', 'Stok - SRC Rully')

@section('content_header')
    <h1>Stok</h1>
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
            <h3 class="card-title">Stok Produk</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama</th>
                        <th>Satuan Dasar</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Minimum</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->baseUnit->name }} ({{ $product->baseUnit->symbol }})</td>
                            <td>{{ rtrim(rtrim(number_format($product->current_stock, 3, '.', ''), '0'), '.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($product->min_stock, 3, '.', ''), '0'), '.') }}</td>
                            <td>
                                @if ($product->isLowStock())
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('stok.riwayat', $product) }}" class="btn btn-sm btn-info">Riwayat</a>
                                @can('stok.kelola')
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                            data-target="#modal-masuk-{{ $product->id }}">Stok Masuk</button>
                                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal"
                                            data-target="#modal-opname-{{ $product->id }}">Opname</button>
                                @endcan
                            </td>
                        </tr>

                        @can('stok.kelola')
                            <div class="modal fade" id="modal-masuk-{{ $product->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('stok.masuk') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Stok Masuk - {{ $product->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Jumlah ({{ $product->baseUnit->symbol }})</label>
                                                    <input type="number" step="0.001" min="0.001" name="qty" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Catatan (opsional)</label>
                                                    <input type="text" name="note" class="form-control">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-opname-{{ $product->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('stok.opname') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Stok Opname - {{ $product->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted">Stok sistem saat ini: {{ rtrim(rtrim(number_format($product->current_stock, 3, '.', ''), '0'), '.') }} {{ $product->baseUnit->symbol }}</p>
                                                <div class="form-group">
                                                    <label>Stok Fisik Hasil Hitung ({{ $product->baseUnit->symbol }})</label>
                                                    <input type="number" step="0.001" min="0" name="physical_stock" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Catatan (opsional)</label>
                                                    <input type="text" name="note" class="form-control">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada produk aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
