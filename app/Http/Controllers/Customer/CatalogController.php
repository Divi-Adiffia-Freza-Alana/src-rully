<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with(['units.unit', 'baseUnit', 'category'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('customer.catalog.index', compact('products'));
    }
}
