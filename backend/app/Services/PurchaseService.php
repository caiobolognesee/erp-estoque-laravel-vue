<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * @param array{
     *   supplier: string,
     *   items: array<int, array{product_id:int, quantity:int, unit_price:numeric}>
     * } $data
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'supplier' => $data['supplier'],
            ]);

            foreach ($data['items'] as $item) {
                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $inQty = (int) $item['quantity'];
                $inPrice = (float) $item['unit_price'];

                $oldStock = (int) $product->stock;
                $oldAvg = (float) $product->avg_cost;

                $newStock = $oldStock + $inQty;
                $newAvg = $newStock > 0
                    ? (($oldStock * $oldAvg) + ($inQty * $inPrice)) / $newStock
                    : 0;

                $product->update([
                    'stock' => $newStock,
                    'avg_cost' => $newAvg,
                ]);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $inQty,
                    'unit_price' => $inPrice,
                ]);
            }

            // optional: eager load items for response
            return $purchase->load(['items']);
        });
    }
}
