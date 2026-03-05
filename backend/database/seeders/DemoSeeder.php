<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $keyboard = Product::create([
            'name' => 'Keyboard',
            'sale_price' => 199.90,
            'avg_cost' => 0,
            'stock' => 0,
        ]);

        $mouse = Product::create([
            'name' => 'Mouse',
            'sale_price' => 99.90,
            'avg_cost' => 0,
            'stock' => 0,
        ]);

        $monitor = Product::create([
            'name' => 'Monitor',
            'sale_price' => 1200.00,
            'avg_cost' => 0,
            'stock' => 0,
        ]);

        $purchaseService = app(PurchaseService::class);

        $purchaseService->createPurchase([
            'supplier' => 'Tech Supplier Inc.',
            'items' => [
                [
                    'product_id' => $keyboard->id,
                    'quantity' => 20,
                    'unit_price' => 50,
                ],
                [
                    'product_id' => $mouse->id,
                    'quantity' => 30,
                    'unit_price' => 20,
                ],
                [
                    'product_id' => $monitor->id,
                    'quantity' => 10,
                    'unit_price' => 700,
                ],
            ],
        ]);

        $saleService = app(SaleService::class);

        $saleService->createSale([
            'customer' => 'Caio Bolognese',
            'items' => [
                [
                    'product_id' => $keyboard->id,
                    'quantity' => 2,
                    'unit_price' => 199.90,
                ],
                [
                    'product_id' => $mouse->id,
                    'quantity' => 3,
                    'unit_price' => 99.90,
                ],
            ],
        ]);
    }
}
