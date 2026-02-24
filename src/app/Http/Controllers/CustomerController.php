<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $customers = Customer::withCount('sales')
            ->withSum(['sales as total_purchased' => fn ($q) => $q->completed()], 'total')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters'   => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Form');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Customer $customer): Response
    {
        $totalSpent     = (float) $customer->sales()->where('status', 'completed')->sum('total');
        $totalSalesCount = $customer->sales()->count();
        $completedCount  = $customer->sales()->where('status', 'completed')->count();

        $customer->load(['sales' => fn ($q) => $q->latest()->take(10)->with('user')]);

        return Inertia::render('Customers/Show', [
            'customer'        => $customer,
            'totalSpent'      => $totalSpent,
            'totalSalesCount' => $totalSalesCount,
            'avgTicket'       => $completedCount > 0 ? round($totalSpent / $completedCount, 2) : 0,
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/Form', [
            'customer' => $customer,
        ]);
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}
