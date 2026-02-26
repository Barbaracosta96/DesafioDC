<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $editor;
    private User $regularUser;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');

        $category = Category::create(['name' => 'Test Category', 'color' => '#000']);

        $this->product = Product::create([
            'category_id'    => $category->id,
            'name'           => 'Test Item',
            'sku'            => 'TEST-001',
            'sale_price'     => 50.00,
            'purchase_price' => 20.00,
            'stock_quantity' => 100,
            'min_stock'      => 5,
            'status'         => 'active',
        ]);
    }

    // ─── CREATE SALE ────────────────────────────────────────────────────────

    public function test_admin_can_create_a_sale(): void
    {
        $payload = [
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 3],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('sales.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', ['user_id' => $this->admin->id]);
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $this->product->id,
            'quantity'   => 3,
        ]);
    }

    public function test_stock_decrements_after_sale_is_created(): void
    {
        $initialStock = $this->product->stock_quantity;

        $this->actingAs($this->admin)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 10],
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'id'             => $this->product->id,
            'stock_quantity' => $initialStock - 10,
        ]);
    }

    public function test_sale_creation_fails_when_stock_is_insufficient(): void
    {
        $response = $this->actingAs($this->admin)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 9999],
            ],
        ]);

        // Validation exception — stock check fails in SaleService
        $response->assertSessionHasErrors('items');

        // Stock must remain unchanged
        $this->assertDatabaseHas('products', [
            'id'             => $this->product->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_no_sale_record_created_when_sale_fails(): void
    {
        $this->actingAs($this->admin)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 9999],
            ],
        ]);

        $this->assertDatabaseCount('sales', 0);
    }

    // ─── UPDATE STATUS ──────────────────────────────────────────────────────

    public function test_admin_can_update_sale_status(): void
    {
        $sale = Sale::create([
            'user_id'   => $this->admin->id,
            'status'    => 'pending',
            'subtotal'  => 150.00,
            'discount'  => 0,
            'total'     => 150.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('sales.update-status', $sale), ['status' => 'completed']);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'status' => 'completed']);
    }

    public function test_cancelling_sale_restores_stock(): void
    {
        // Create the sale (decrements stock to 90)
        $this->actingAs($this->admin)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 10],
            ],
        ]);

        $sale = Sale::first();
        $stockAfterSale = $this->product->fresh()->stock_quantity; // should be 90

        // Cancel the sale
        $this->actingAs($this->admin)
            ->patch(route('sales.update-status', $sale), ['status' => 'cancelled']);

        // Stock should be back to original
        $this->assertDatabaseHas('products', [
            'id'             => $this->product->id,
            'stock_quantity' => $stockAfterSale + 10,
        ]);
    }

    public function test_regular_user_cannot_cancel_sale(): void
    {
        $sale = Sale::create([
            'user_id'  => $this->admin->id,
            'status'   => 'pending',
            'subtotal' => 50.00,
            'discount' => 0,
            'total'    => 50.00,
        ]);

        // 'user' role does not have 'cancel sales' permission → 403
        $this->actingAs($this->regularUser)
            ->patch(route('sales.update-status', $sale), ['status' => 'cancelled'])
            ->assertForbidden();
    }

    // ─── LISTING ────────────────────────────────────────────────────────────

    public function test_admin_can_view_sales_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('sales.index'))
            ->assertStatus(200);
    }

    public function test_regular_user_can_view_sales_list(): void
    {
        $this->actingAs($this->regularUser)
            ->get(route('sales.index'))
            ->assertStatus(200);
    }
}
