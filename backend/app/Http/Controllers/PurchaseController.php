<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService
    ) {}

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->createPurchase($request->validated());

        return response()->json([
            'id' => $purchase->id,
            'supplier' => $purchase->supplier,
            'created_at' => $purchase->created_at,
            'items' => $purchase->items->map(fn ($i) => [
                'product_id' => $i->product_id,
                'quantity' => $i->quantity,
                'unit_price' => (float) $i->unit_price,
            ]),
        ], 201);
    }
}
