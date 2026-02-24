<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(Request $request): Response
    {
        $products = Product::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%"))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products'   => $products,
            'categories' => Category::select('id', 'name')->get(),
            'filters'    => $request->only(['search', 'category', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Form', [
            'categories' => Category::select('id', 'name', 'color')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product): Response
    {
        $product->load('category');

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Form', [
            'product'    => $product->load('category'),
            'categories' => Category::select('id', 'name', 'color')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}
