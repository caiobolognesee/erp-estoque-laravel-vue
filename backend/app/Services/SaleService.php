<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * @param array{
     *   customer: string,
     *   items: array<int, array{product_id:int, quantity:int, unit_price:numeric}>
     * } $data
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'customer' => $data['customer'],
                'total' => 0,
                'profit' => 0,
                'status' => 'completed',
            ]);

            $total = 0.0;
            $profit = 0.0;

            foreach ($data['items'] as $item) {
                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $qty = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $costAtSale = (float) $product->avg_cost;

                if ((int) $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for product_id={$product->id}. Available={$product->stock}, requested={$qty}.",
                    ]);
                }

                // Update stock
                $product->decrement('stock', $qty);

                // Calculate
                $lineTotal = $qty * $unitPrice;
                $lineProfit = $qty * ($unitPrice - $costAtSale);

                $total += $lineTotal;
                $profit += $lineProfit;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_at_sale' => $costAtSale,
                ]);
            }

            $sale->update([
                'total' => $total,
                'profit' => $profit,
            ]);

            return $sale->load(['items']);
        });
    }
}
