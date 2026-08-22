<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function create(): View
    {
        $products = Product::with(['units.unit', 'baseUnit'])
            ->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('penjualan.create', compact('products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $items = $request->validated('items');

        $sale = DB::transaction(function () use ($items, $request) {
            $total = 0;
            $rows = [];

            foreach ($items as $item) {
                $productUnit = ProductUnit::with('product')->findOrFail($item['product_unit_id']);

                if ($productUnit->product_id !== (int) $item['product_id']) {
                    throw new \RuntimeException('Data satuan tidak sesuai dengan produk.');
                }

                $qty = (float) $item['qty'];
                $subtotal = $qty * (float) $productUnit->sell_price;
                $total += $subtotal;

                $rows[] = [
                    'product' => $productUnit->product,
                    'unit_id' => $productUnit->unit_id,
                    'conversion_to_base' => $productUnit->conversion_to_base,
                    'qty' => $qty,
                    'price' => $productUnit->sell_price,
                    'subtotal' => $subtotal,
                ];
            }

            $paid = (float) $request->validated('paid');

            if ($paid < $total) {
                throw new \RuntimeException('Jumlah bayar kurang dari total belanja.');
            }

            $sale = $this->createSaleWithUniqueInvoice($request->user()->id, $total, $paid);

            foreach ($rows as $row) {
                $saleItem = $sale->items()->create([
                    'product_id' => $row['product']->id,
                    'unit_id' => $row['unit_id'],
                    'conversion_to_base' => $row['conversion_to_base'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'subtotal' => $row['subtotal'],
                ]);

                $baseQty = $row['qty'] * $row['conversion_to_base'];

                $this->stockService->mutate(
                    product: $row['product'],
                    qty: -$baseQty,
                    type: 'sale',
                    userId: $request->user()->id,
                    referenceType: Sale::class,
                    referenceId: $sale->id,
                );
            }

            return $sale;
        });

        return redirect()->route('penjualan.nota', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function nota(Sale $penjualan): View
    {
        $penjualan->load(['items.product', 'items.unit', 'user']);

        return view('penjualan.nota', ['sale' => $penjualan]);
    }

    public function riwayat(): View
    {
        $sales = Sale::with('user')
            ->latest()
            ->paginate(20);

        return view('penjualan.riwayat', compact('sales'));
    }

    private function createSaleWithUniqueInvoice(int $userId, float $total, float $paid): Sale
    {
        $prefix = 'INV-'.now()->format('Ymd').'-';
        $sequence = Sale::whereDate('created_at', now())->count() + 1;

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Sale::create([
                    'invoice_no' => $prefix.str_pad((string) ($sequence + $attempt), 4, '0', STR_PAD_LEFT),
                    'user_id' => $userId,
                    'total' => $total,
                    'paid' => $paid,
                    'change' => $paid - $total,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($attempt === 4) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Gagal membuat nomor invoice.');
    }
}
