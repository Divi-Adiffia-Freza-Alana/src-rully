@extends('layouts.customer')

@section('title', 'Checkout - SRC Rully')

@section('content')
    <h4 class="mb-4">Checkout</h4>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header"><strong>Ringkasan Pesanan</strong></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($items as $item)
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
                                <th class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.checkout.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Alamat Pengiriman</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', $customer->address) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>No. HP untuk Pengiriman</label>
                            <input type="text" name="shipping_phone" class="form-control" value="{{ old('shipping_phone', $customer->phone) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pm-transfer" name="payment_method" value="transfer" class="custom-control-input" checked>
                                <label class="custom-control-label" for="pm-transfer">Transfer Bank</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pm-cod" name="payment_method" value="cod" class="custom-control-input">
                                <label class="custom-control-label" for="pm-cod">Bayar di Tempat (COD)</label>
                            </div>
                        </div>
                        <div class="alert alert-info small">
                            <strong>Transfer Bank:</strong> setelah pesanan dibuat, Anda perlu mengunggah bukti transfer di halaman detail pesanan.<br>
                            <strong>COD:</strong> pembayaran dilakukan tunai saat barang diterima.
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Buat Pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
