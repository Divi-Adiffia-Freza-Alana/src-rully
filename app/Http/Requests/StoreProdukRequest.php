<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('produk')?->id;

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],

            'units' => ['required', 'array', 'min:1'],
            'units.*.unit_id' => ['required', 'distinct', 'exists:units,id'],
            'units.*.conversion_to_base' => ['required', 'numeric', 'min:0.001'],
            'units.*.sell_price' => ['required', 'numeric', 'min:0'],
            'units.*.is_base' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $units = $this->input('units', []);
            $baseCount = collect($units)->filter(fn ($u) => filter_var($u['is_base'] ?? false, FILTER_VALIDATE_BOOLEAN))->count();

            if ($baseCount !== 1) {
                $validator->errors()->add('units', 'Harus ada tepat satu satuan dasar (is_base) yang dipilih.');
            }
        });
    }
}
