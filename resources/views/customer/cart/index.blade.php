@extends('layouts.customer')

@section('title', 'Keranjang - SRC Rully')

@section('content')
    <h4 class="mb-4">Keranjang Belanja</h4>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->unit->name }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="width: 140px;">
                                <form action="{{ route('customer.cart.update', $item->product_unit_id) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="qty" value="{{ $item->qty }}" min="0.001" step="0.001" class="form-control form-control-sm mr-1">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('customer.cart.destroy', $item->product_unit_id) }}" method="POST"
                                      onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">&times;</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Keranjang Anda kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($items->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th colspan="2">Rp {{ number_format($total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('customer.catalog') }}" class="btn btn-default">&larr; Lanjut Belanja</a>
        @if ($items->isNotEmpty())
            <a href="{{ route('customer.checkout') }}" class="btn btn-success">Checkout &rarr;</a>
        @endif
    </div>
@stop
