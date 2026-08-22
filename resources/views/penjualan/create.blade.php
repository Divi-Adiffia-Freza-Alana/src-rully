@extends('adminlte::page')

@section('title', 'Penjualan - SI Inventory')

@section('content_header')
    <h1>Kasir</h1>
@stop

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @error('items')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pilih Produk</h3>
                </div>
                <div class="card-body">
                    <input type="text" id="search-produk" class="form-control mb-3" placeholder="Cari produk...">
                    <div class="row" id="product-grid">
                        @foreach ($products as $product)
                            <div class="col-md-4 mb-3 product-card" data-name="{{ strtolower($product->name) }}">
                                <div class="card h-100">
                                    <div class="card-body p-2 text-center">
                                        <p class="mb-1 font-weight-bold">{{ $product->name }}</p>
                                        <p class="text-muted mb-2" style="font-size: 12px;">
                                            Stok: {{ rtrim(rtrim(number_format($product->current_stock, 3, '.', ''), '0'), '.') }} {{ $product->baseUnit->symbol }}
                                        </p>
                                        <select class="form-control form-control-sm unit-select" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                                            @foreach ($product->units as $unit)
                                                <option value="{{ $unit->id }}"
                                                        data-price="{{ $unit->sell_price }}"
                                                        data-unit-name="{{ $unit->unit->name }}"
                                                        data-conversion="{{ $unit->conversion_to_base }}">
                                                    {{ $unit->unit->name }} - Rp {{ number_format($unit->sell_price, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-primary btn-sm btn-block mt-2 add-to-cart">+ Tambah</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Keranjang</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <tr id="cart-empty-row">
                                <td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="{{ route('penjualan.store') }}" method="POST" id="sale-form">
                        @csrf
                        <div id="items-container"></div>

                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total</strong>
                            <strong id="total-display">Rp 0</strong>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Bayar</label>
                            <input type="number" step="0.01" min="0" name="paid" id="paid-input" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Kembalian</strong>
                            <strong id="change-display">Rp 0</strong>
                        </div>
                        <button type="submit" class="btn btn-success btn-block" id="submit-sale" disabled>Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
(function () {
    var cart = [];

    var cartBody = document.getElementById('cart-body');
    var itemsContainer = document.getElementById('items-container');
    var totalDisplay = document.getElementById('total-display');
    var changeDisplay = document.getElementById('change-display');
    var paidInput = document.getElementById('paid-input');
    var submitBtn = document.getElementById('submit-sale');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function render() {
        cartBody.innerHTML = '';
        itemsContainer.innerHTML = '';

        if (cart.length === 0) {
            cartBody.innerHTML = '<tr id="cart-empty-row"><td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td></tr>';
        }

        var total = 0;

        cart.forEach(function (item, i) {
            var subtotal = item.qty * item.price;
            total += subtotal;

            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + item.productName + '<br><small class="text-muted">' + item.unitName + '</small></td>' +
                '<td><input type="number" step="0.001" min="0.001" value="' + item.qty + '" class="form-control form-control-sm qty-input" data-index="' + i + '" style="width: 80px"></td>' +
                '<td>' + formatRupiah(subtotal) + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-danger remove-item" data-index="' + i + '">&times;</button></td>';
            cartBody.appendChild(tr);

            ['product_id', 'product_unit_id', 'qty'].forEach(function (field) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'items[' + i + '][' + field + ']';
                hidden.value = field === 'qty' ? item.qty : item[field];
                itemsContainer.appendChild(hidden);
            });
        });

        totalDisplay.textContent = formatRupiah(total);
        updateChange(total);
        submitBtn.disabled = cart.length === 0;
    }

    function updateChange(total) {
        var paid = parseFloat(paidInput.value) || 0;
        var change = paid - total;
        changeDisplay.textContent = formatRupiah(change < 0 ? 0 : change);
        changeDisplay.className = change < 0 ? 'text-danger' : '';
    }

    function currentTotal() {
        return cart.reduce(function (sum, item) { return sum + item.qty * item.price; }, 0);
    }

    document.getElementById('product-grid').addEventListener('click', function (e) {
        if (!e.target.classList.contains('add-to-cart')) return;

        var card = e.target.closest('.product-card');
        var select = card.querySelector('.unit-select');
        var option = select.options[select.selectedIndex];

        cart.push({
            product_id: select.dataset.productId,
            productName: select.dataset.productName,
            product_unit_id: select.value,
            unitName: option.dataset.unitName,
            price: parseFloat(option.dataset.price),
            qty: 1,
        });

        render();
    });

    cartBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty-input')) {
            var idx = parseInt(e.target.dataset.index, 10);
            var val = parseFloat(e.target.value) || 0;
            cart[idx].qty = val > 0 ? val : 1;
            render();
        }
    });

    cartBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            var idx = parseInt(e.target.dataset.index, 10);
            cart.splice(idx, 1);
            render();
        }
    });

    paidInput.addEventListener('input', function () {
        updateChange(currentTotal());
    });

    document.getElementById('search-produk').addEventListener('input', function (e) {
        var q = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(function (card) {
            card.style.display = card.dataset.name.indexOf(q) !== -1 ? '' : 'none';
        });
    });

    document.getElementById('sale-form').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
        }
    });

    render();
})();
</script>
@endpush
