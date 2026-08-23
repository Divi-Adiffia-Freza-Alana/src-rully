@extends('layouts.customer')

@section('title', 'Katalog Produk - SRC Rully')

@section('content')
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari produk...">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card product-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">{{ $product->category?->name ?? 'Umum' }}</p>

                        @auth('customer')
                            <form action="{{ route('customer.cart.store') }}" method="POST" class="mt-auto">
                                @csrf
                                <div class="form-group mb-2">
                                    <select name="product_unit_id" class="form-control form-control-sm">
                                        @foreach ($product->units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->unit->name }} - Rp {{ number_format($unit->sell_price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="number" name="qty" value="1" min="0.001" step="0.001" class="form-control form-control-sm">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </form>
                        @else
                            <p class="mb-2">
                                Mulai dari <strong>Rp {{ number_format($product->units->min('sell_price'), 0, ',', '.') }}</strong>
                            </p>
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary btn-sm btn-block mt-auto">
                                Masuk untuk Membeli
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted py-5">Belum ada produk tersedia.</p>
            </div>
        @endforelse
    </div>

    {{ $products->links() }}
@stop
