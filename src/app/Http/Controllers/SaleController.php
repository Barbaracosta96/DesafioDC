<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index(Request $request): Response
    {
        $sales = Sale::with('customer', 'user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('customer', fn ($c) => $c->where('name', 'like', "%{$request->search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales/Index', [
            'sales'   => $sales,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Sales/Create', [
            'customers' => Customer::select('id', 'name', 'email')->orderBy('name')->get(),
            'products'  => Product::active()->with('category')->orderBy('name')->get([
                'id', 'name', 'sku', 'sale_price', 'stock_quantity', 'category_id',
            ]),
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = $this->saleService->create($request->validated(), $request->user()->id);

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Venda registrada com sucesso!');
    }

    public function show(Sale $sale): Response
    {
        $sale->load('items.product.category', 'customer', 'user');

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function updateStatus(Request $request, Sale $sale): RedirectResponse
    {
        $request->validate(['status' => ['required', 'in:pending,completed,cancelled']]);

        $this->saleService->updateStatus($sale, $request->status);

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}
