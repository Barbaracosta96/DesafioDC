<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@dabang.app'],
            ['name' => 'Admin Dabang', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        $editor = User::firstOrCreate(
            ['email' => 'editor@dabang.app'],
            ['name' => 'Editor User', 'password' => Hash::make('password')]
        );
        $editor->assignRole('editor');

        $user = User::firstOrCreate(
            ['email' => 'user@dabang.app'],
            ['name' => 'Regular User', 'password' => Hash::make('password')]
        );
        $user->assignRole('user');

        // Categories
        $categories = [
            ['name' => 'Eletrônicos',     'color' => '#7C3AED'],
            ['name' => 'Casa & Decoração', 'color' => '#EC4899'],
            ['name' => 'Moda',             'color' => '#F59E0B'],
            ['name' => 'Beleza',           'color' => '#10B981'],
            ['name' => 'Esporte',          'color' => '#3B82F6'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $cats = Category::all()->keyBy('name');

        // Products
        $products = [
            ['category_id' => $cats['Eletrônicos']->id,      'name' => 'Home Decor Range',            'sku' => 'HDEC-001', 'sale_price' => 299.90,  'purchase_price' => 150.00, 'stock_quantity' => 45, 'min_stock' => 10],
            ['category_id' => $cats['Moda']->id,             'name' => 'Disney Princess Pink Bag III','sku' => 'DPB-003',  'sale_price' => 189.90,  'purchase_price' => 80.00,  'stock_quantity' => 62, 'min_stock' => 15],
            ['category_id' => $cats['Casa & Decoração']->id, 'name' => 'Bathroom Essentials',          'sku' => 'BATH-001', 'sale_price' => 99.90,   'purchase_price' => 45.00,  'stock_quantity' => 8,  'min_stock' => 10],
            ['category_id' => $cats['Eletrônicos']->id,      'name' => 'Apple Smartwatches',           'sku' => 'APL-SW1',  'sale_price' => 1299.00, 'purchase_price' => 900.00, 'stock_quantity' => 3,  'min_stock' => 5],
            ['category_id' => $cats['Beleza']->id,           'name' => 'Perfume Collection',           'sku' => 'PERF-001', 'sale_price' => 349.90,  'purchase_price' => 180.00, 'stock_quantity' => 30, 'min_stock' => 8],
            ['category_id' => $cats['Esporte']->id,          'name' => 'Running Shoes Pro',            'sku' => 'SHOE-001', 'sale_price' => 459.90,  'purchase_price' => 200.00, 'stock_quantity' => 20, 'min_stock' => 5],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }

        // Customers
        $customersData = [
            ['name' => 'Maria Silva',    'email' => 'maria@email.com',   'phone' => '11 99999-0001', 'city' => 'São Paulo',     'state' => 'SP'],
            ['name' => 'João Oliveira',  'email' => 'joao@email.com',    'phone' => '21 98888-0002', 'city' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Ana Costa',      'email' => 'ana@email.com',     'phone' => '31 97777-0003', 'city' => 'Belo Horizonte', 'state' => 'MG'],
            ['name' => 'Carlos Menezes', 'email' => 'carlos@email.com',  'phone' => '41 96666-0004', 'city' => 'Curitiba',       'state' => 'PR'],
            ['name' => 'Patrícia Lima',  'email' => 'patricia@email.com','phone' => '51 95555-0005', 'city' => 'Porto Alegre',   'state' => 'RS'],
        ];

        foreach ($customersData as $cust) {
            Customer::firstOrCreate(['email' => $cust['email']], $cust);
        }

        // Generate demo sales for the last 3 months
        $customers = Customer::all();
        $prods     = Product::all();

        $months = [
            now()->subMonths(2)->startOfMonth(),
            now()->subMonths(1)->startOfMonth(),
            now()->startOfMonth(),
        ];

        foreach ($months as $monthStart) {
            for ($i = 0; $i < 8; $i++) {
                $customer = $customers->random();
                $product  = $prods->random();
                $qty      = rand(1, 3);
                $total    = $product->sale_price * $qty;

                $sale = Sale::create([
                    'user_id'     => $admin->id,
                    'customer_id' => $customer->id,
                    'status'      => 'completed',
                    'subtotal'    => $total,
                    'discount'    => 0,
                    'total'       => $total,
                    'created_at'  => $monthStart->copy()->addDays(rand(0, 25)),
                ]);

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $product->id,
                    'quantity'    => $qty,
                    'unit_price'  => $product->sale_price,
                    'total_price' => $total,
                ]);
            }
        }
    }
}
