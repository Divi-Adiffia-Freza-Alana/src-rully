@php
    $existingUnits = old('units', ($produk ?? null)?->units->map(fn ($u) => [
        'unit_id' => $u->unit_id,
        'conversion_to_base' => $u->conversion_to_base,
        'sell_price' => $u->sell_price,
        'is_base' => $u->is_base,
    ])->all() ?? [
        ['unit_id' => '', 'conversion_to_base' => 1, 'sell_price' => '', 'is_base' => true],
    ]);
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="sku">SKU / Kode Produk</label>
            <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror"
                   value="{{ old('sku', $produk->sku ?? '') }}" required>
            @error('sku') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $produk->name ?? '') }}" required>
            @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                <option value="">-- Tanpa kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $produk->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="purchase_price">Harga Beli</label>
            <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price"
                   class="form-control @error('purchase_price') is-invalid @enderror"
                   value="{{ old('purchase_price', $produk->purchase_price ?? '') }}" required>
            @error('purchase_price') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="min_stock">Stok Minimum (dalam satuan dasar)</label>
            <input type="number" step="0.001" min="0" name="min_stock" id="min_stock"
                   class="form-control @error('min_stock') is-invalid @enderror"
                   value="{{ old('min_stock', $produk->min_stock ?? 0) }}" required>
            @error('min_stock') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
               @checked(old('is_active', $produk->is_active ?? true))>
        <label class="custom-control-label" for="is_active">Produk Aktif</label>
    </div>
</div>

<hr>

<h5>Satuan Jual</h5>
<p class="text-muted">Tentukan satuan-satuan yang bisa dipakai untuk menjual produk ini. Tandai satu sebagai <strong>satuan dasar</strong> (dipakai untuk mencatat stok, konversi = 1).</p>

@error('units') <div class="alert alert-danger">{{ $message }}</div> @enderror

<table class="table table-bordered" id="units-table">
    <thead>
        <tr>
            <th style="width: 25%">Satuan</th>
            <th style="width: 25%">Konversi ke Satuan Dasar</th>
            <th style="width: 25%">Harga Jual</th>
            <th style="width: 15%">Satuan Dasar?</th>
            <th style="width: 10%"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($existingUnits as $i => $row)
            <tr>
                <td>
                    <select name="units[{{ $i }}][unit_id]" class="form-control" required>
                        <option value="">-- pilih --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected($row['unit_id'] == $unit->id)>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.001" min="0.001" name="units[{{ $i }}][conversion_to_base]"
                           class="form-control" value="{{ $row['conversion_to_base'] }}" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="units[{{ $i }}][sell_price]"
                           class="form-control" value="{{ $row['sell_price'] }}" required>
                </td>
                <td class="text-center">
                    <input type="radio" name="units_base_index" value="{{ $i }}" @checked($row['is_base']) style="transform: scale(1.5)">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-unit-row">&times;</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<button type="button" class="btn btn-secondary btn-sm mb-3" id="add-unit-row">+ Tambah Satuan</button>

<div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('produk.index') }}" class="btn btn-default">Batal</a>
</div>

@push('js')
<script>
(function () {
    var unitOptions = @json($units->map(fn ($u) => ['id' => $u->id, 'label' => $u->name.' ('.$u->symbol.')']));
    var table = document.getElementById('units-table').querySelector('tbody');
    var index = {{ count($existingUnits) }};

    document.getElementById('add-unit-row').addEventListener('click', function () {
        var tr = document.createElement('tr');
        var options = '<option value="">-- pilih --</option>';
        unitOptions.forEach(function (u) {
            options += '<option value="' + u.id + '">' + u.label + '</option>';
        });

        tr.innerHTML =
            '<td><select name="units[' + index + '][unit_id]" class="form-control" required>' + options + '</select></td>' +
            '<td><input type="number" step="0.001" min="0.001" name="units[' + index + '][conversion_to_base]" class="form-control" value="1" required></td>' +
            '<td><input type="number" step="0.01" min="0" name="units[' + index + '][sell_price]" class="form-control" required></td>' +
            '<td class="text-center"><input type="radio" name="units_base_index" value="' + index + '" style="transform: scale(1.5)"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-unit-row">&times;</button></td>';

        table.appendChild(tr);
        index++;
    });

    table.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-unit-row')) {
            if (table.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });

    document.querySelector('form').addEventListener('submit', function () {
        var checked = table.querySelector('input[name="units_base_index"]:checked');
        table.querySelectorAll('tr').forEach(function (tr, i) {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            var isBase = checked && checked.closest('tr') === tr;
            var nameMatch = tr.querySelector('select[name^="units["]').name.match(/units\[(\d+)\]/);
            hidden.name = 'units[' + nameMatch[1] + '][is_base]';
            hidden.value = isBase ? '1' : '0';
            tr.appendChild(hidden);
        });
    });
})();
</script>
@endpush
