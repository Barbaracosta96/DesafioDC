<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Create a new sale with items, decrementing stock inside a DB transaction.
     *
     * @param  array  $data    Validated sale data
     * @param  int    $userId  The authenticated user ID
     */
    public function create(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            $subtotal = 0;

            // Validate and lock product rows for stock check
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Estoque insuficiente para o produto: {$product->name}. Disponível: {$product->stock_quantity}",
                    ]);
                }

                $subtotal += $product->sale_price * $item['quantity'];
            }

            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            $sale = Sale::create([
                'user_id'     => $userId,
                'customer_id' => $data['customer_id'] ?? null,
                'status'      => 'pending',
                'subtotal'    => $subtotal,
                'discount'    => $discount,
                'total'       => $total,
                'notes'       => $data['notes'] ?? null,
            ]);

            // Loop items again to create records and update stock
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);

                $sale->items()->create([
                    'product_id'     => $product->id,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $product->sale_price,
                    'total_price'    => $product->sale_price * $item['quantity'],
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            return $sale->load('items.product', 'customer', 'user');
        });
    }

    /**
     * Update sale status; if cancelled, restore stock.
     */
    public function updateStatus(Sale $sale, string $status): Sale
    {
        return DB::transaction(function () use ($sale, $status) {
            $previousStatus = $sale->status;

            // Revert stock if cancelling a non-cancelled sale
            if ($status === 'cancelled' && $previousStatus !== 'cancelled') {
                foreach ($sale->items as $item) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            $sale->update(['status' => $status]);

            return $sale->fresh('items.product', 'customer', 'user');
        });
    }
}
