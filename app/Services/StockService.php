<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Apply a stock mutation to a product and record it.
     * $qty must be in the product's base unit, positive for additions and negative for reductions.
     */
    public function mutate(
        Product $product,
        float $qty,
        string $type,
        ?int $userId = null,
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): StockMutation {
        return DB::transaction(function () use ($product, $qty, $type, $userId, $note, $referenceType, $referenceId) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $stockBefore = $locked->current_stock;
            $stockAfter = $stockBefore + $qty;

            if ($stockAfter < 0) {
                throw new \RuntimeException("Stok tidak mencukupi untuk produk {$locked->name}.");
            }

            $locked->update(['current_stock' => $stockAfter]);

            $mutation = StockMutation::create([
                'product_id' => $locked->id,
                'user_id' => $userId,
                'type' => $type,
                'qty' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
            ]);

            $product->setAttribute('current_stock', $stockAfter);

            return $mutation;
        });
    }
}
