<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->saleService->createSale($request->validated());

        return response()->json([
            'id' => $sale->id,
            'customer' => $sale->customer,
            'total' => (float) $sale->total,
            'profit' => (float) $sale->profit,
            'created_at' => $sale->created_at,
            'items' => $sale->items->map(fn ($i) => [
                'product_id' => $i->product_id,
                'quantity' => $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'cost_at_sale' => (float) $i->cost_at_sale,
            ]),
        ], 201);
    }
}
