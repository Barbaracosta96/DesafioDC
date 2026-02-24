<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed database with roles
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);
    }

    public function test_admin_can_view_products_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'description' => 'Product description',
            'purchase_price' => 10.00,
            'sale_price' => 20.00,
            'stock_quantity' => 100,
            'min_stock' => 10,
            'category_id' => null, // Assuming optional for test simplicity
            'status' => 'active',
        ];

        $response = $this->actingAs($editor)->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['sku' => 'TEST-SKU-001']);
    }

    public function test_regular_user_cannot_delete_product(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user'); // User role typically has read-only access to products

        $product = Product::factory()->create();

        $response = $this->actingAs($user)->delete(route('products.destroy', $product));

        // Expect forbidden or restriction based on ACL configuration
        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
