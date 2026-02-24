<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Aggregated stats — all calculated at DB level to avoid N+1
        $totalRevenue     = Sale::completed()->sum('total');
        $totalOrders      = Sale::completed()->count();
        $totalProducts    = Product::active()->count();
        $totalCustomers   = Customer::count();

        // Revenue for the last 7 days (for chart)
        $revenuePerDay = Sale::completed()
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Revenue per month for current year (online vs offline / for Figma chart)
        $revenuePerMonth = Sale::completed()
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Top 4 products by quantity sold
        $topProducts = Product::withSum(['saleItems as total_sold' => function ($q) {
            $q->whereHas('sale', fn ($s) => $s->completed());
        }], 'quantity')
            ->orderByDesc('total_sold')
            ->take(4)
            ->with('category')
            ->get(['id', 'name', 'sale_price', 'stock_quantity', 'category_id']);

        // Recent sales
        $recentSales = Sale::with('customer', 'user')
            ->latest()
            ->take(5)
            ->get(['id', 'customer_id', 'user_id', 'total', 'status', 'created_at']);

        // Low stock alerts
        $lowStockProducts = Product::lowStock()->active()->count();

        // Monthly comparison this month vs last month
        $thisMonthRevenue = Sale::completed()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $lastMonthRevenue = Sale::completed()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'totalRevenue'    => (float) $totalRevenue,
                'totalOrders'     => $totalOrders,
                'totalProducts'   => $totalProducts,
                'totalCustomers'  => $totalCustomers,
                'lowStockCount'   => $lowStockProducts,
                'thisMonthRevenue' => (float) $thisMonthRevenue,
                'lastMonthRevenue' => (float) $lastMonthRevenue,
            ],
            'revenuePerDay'   => $revenuePerDay,
            'revenuePerMonth' => $revenuePerMonth,
            'topProducts'     => $topProducts,
            'recentSales'     => $recentSales,
        ]);
    }
}
