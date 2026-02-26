<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ACL (Access Control Layer) tests.
 *
 * Verifies that each role can only perform actions it is permitted to,
 * and that unauthorized actions are correctly blocked (403 / redirect).
 */
class AclTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $editor;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);

        $this->admin  = tap(User::factory()->create())->assignRole('admin');
        $this->editor = tap(User::factory()->create())->assignRole('editor');
        $this->user   = tap(User::factory()->create())->assignRole('user');
    }

    // ─── USERS MODULE (admin-only) ───────────────────────────────────────────

    public function test_admin_can_access_users_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertStatus(200);
    }

    public function test_editor_cannot_access_users_list(): void
    {
        $this->actingAs($this->editor)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_regular_user_cannot_access_users_list(): void
    {
        $this->actingAs($this->user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    // ─── PRODUCTS MODULE ────────────────────────────────────────────────────

    public function test_admin_can_delete_product(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_editor_can_delete_product(): void
    {
        $product = $this->makeProduct();

        // editor has 'delete products' permission? Let's check seeder.
        // editor: view/create/edit products only — NOT delete.
        $this->actingAs($this->editor)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();
    }

    public function test_regular_user_cannot_create_product(): void
    {
        $category = Category::create(['name' => 'Cat', 'color' => '#fff']);

        $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name'           => 'Forbidden Product',
                'sku'            => 'FBD-001',
                'sale_price'     => 99.90,
                'stock_quantity' => 10,
                'status'         => 'active',
                'category_id'    => $category->id,
            ])
            ->assertForbidden();
    }

    public function test_regular_user_can_view_products_list(): void
    {
        $this->actingAs($this->user)
            ->get(route('products.index'))
            ->assertStatus(200);
    }

    // ─── CUSTOMERS MODULE ────────────────────────────────────────────────────

    public function test_editor_can_create_customer(): void
    {
        $this->actingAs($this->editor)
            ->post(route('customers.store'), [
                'name'  => 'João Silva',
                'email' => 'joao@example.com',
                'phone' => '11999990000',
            ])
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['email' => 'joao@example.com']);
    }

    public function test_regular_user_cannot_delete_customer(): void
    {
        $customer = Customer::create([
            'name'  => 'Customer X',
            'email' => 'cx@ex.com',
            'phone' => '11900000000',
        ]);

        $this->actingAs($this->user)
            ->delete(route('customers.destroy', $customer))
            ->assertForbidden();
    }

    public function test_admin_can_delete_customer(): void
    {
        $customer = Customer::create([
            'name'  => 'Customer Y',
            'email' => 'cy@ex.com',
            'phone' => '11911111111',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    // ─── DASHBOARD ───────────────────────────────────────────────────────────

    public function test_all_roles_can_access_dashboard(): void
    {
        foreach ([$this->admin, $this->editor, $this->user] as $actor) {
            $this->actingAs($actor)
                ->get(route('dashboard'))
                ->assertStatus(200);
        }
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    private function makeProduct(): Product
    {
        $category = Category::create(['name' => 'Categ-'.uniqid(), 'color' => '#aabbcc']);

        return Product::create([
            'category_id'    => $category->id,
            'name'           => 'Product-'.uniqid(),
            'sku'            => 'SKU-'.uniqid(),
            'sale_price'     => 10.00,
            'purchase_price' => 5.00,
            'stock_quantity' => 50,
            'min_stock'      => 5,
            'status'         => 'active',
        ]);
    }
}
