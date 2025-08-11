<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDashboardController extends Controller
{
    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        $this->multiTenantService = $multiTenantService;
    }

    /**
     * Display the main analytics dashboard
     */
    public function index()
    {
        // Ensure we're using main database for global analytics
        $this->multiTenantService->switchToMainDatabase();

        $analytics = [
            'overview' => $this->getOverviewStats(),
            'clients' => $this->getClientStats(),
            'revenue' => $this->getRevenueStats(),
            'performance' => $this->getPerformanceStats(),
            'growth' => $this->getGrowthStats(),
        ];

        return view('admin.analytics.dashboard', compact('analytics'));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $totalRevenue = $this->getTotalRevenue();
        $totalOrders = $this->getTotalOrders();
        $totalProducts = $this->getTotalProducts();
        $totalUsers = $this->getTotalUsers();

        return [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'growth_rate' => $this->calculateGrowthRate(),
        ];
    }

    /**
     * Get client-specific statistics
     */
    private function getClientStats()
    {
        $clients = Client::with(['users' => function($query) {
            $query->select('client_id', DB::raw('count(*) as user_count'));
        }])->get();

        $clientStats = [];

        foreach ($clients as $client) {
            // Switch to client database
            $this->multiTenantService->switchToClientDatabase($client->id);

            $stats = [
                'id' => $client->id,
                'name' => $client->name,
                'company_name' => $client->company_name,
                'domain' => $client->domain,
                'subdomain' => $client->subdomain,
                'theme' => $client->theme,
                'status' => $client->status,
                'created_at' => $client->created_at,
                'products_count' => $this->getClientProductCount(),
                'orders_count' => $this->getClientOrderCount(),
                'revenue' => $this->getClientRevenue(),
                'users_count' => $this->getClientUserCount(),
                'categories_count' => $this->getClientCategoryCount(),
                'reviews_count' => $this->getClientReviewCount(),
                'avg_order_value' => $this->getClientAvgOrderValue(),
                'conversion_rate' => $this->getClientConversionRate(),
                'top_products' => $this->getClientTopProducts(),
                'recent_orders' => $this->getClientRecentOrders(),
            ];

            $clientStats[] = $stats;
        }

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();

        return $clientStats;
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStats()
    {
        $clients = Client::all();
        $monthlyRevenue = [];
        $clientRevenue = [];

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);

            // Monthly revenue for this client
            $monthly = DB::table('orders')
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            foreach ($monthly as $month) {
                $key = $month->year . '-' . str_pad($month->month, 2, '0', STR_PAD_LEFT);
                if (!isset($monthlyRevenue[$key])) {
                    $monthlyRevenue[$key] = 0;
                }
                $monthlyRevenue[$key] += $month->revenue ?? 0;
            }

            $clientRevenue[] = [
                'client_name' => $client->name,
                'revenue' => $this->getClientRevenue(),
            ];
        }

        $this->multiTenantService->switchToMainDatabase();

        return [
            'monthly' => $monthlyRevenue,
            'by_client' => $clientRevenue,
            'total' => array_sum($monthlyRevenue),
        ];
    }

    /**
     * Get performance statistics
     */
    private function getPerformanceStats()
    {
        $clients = Client::all();
        $performance = [];

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);

            $performance[] = [
                'client_name' => $client->name,
                'products' => $this->getClientProductCount(),
                'orders' => $this->getClientOrderCount(),
                'revenue' => $this->getClientRevenue(),
                'users' => $this->getClientUserCount(),
                'conversion_rate' => $this->getClientConversionRate(),
                'avg_order_value' => $this->getClientAvgOrderValue(),
                'reviews' => $this->getClientReviewCount(),
                'avg_rating' => $this->getClientAvgRating(),
            ];
        }

        $this->multiTenantService->switchToMainDatabase();

        return $performance;
    }

    /**
     * Get growth statistics
     */
    private function getGrowthStats()
    {
        $clients = Client::all();
        $growth = [];

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);

            $currentMonth = $this->getClientRevenueForPeriod(Carbon::now()->startOfMonth(), Carbon::now());
            $lastMonth = $this->getClientRevenueForPeriod(
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            );

            $revenueGrowth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

            $currentUsers = $this->getClientUserCountForPeriod(Carbon::now()->startOfMonth(), Carbon::now());
            $lastMonthUsers = $this->getClientUserCountForPeriod(
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            );

            $userGrowth = $lastMonthUsers > 0 ? (($currentUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;

            $growth[] = [
                'client_name' => $client->name,
                'revenue_growth' => round($revenueGrowth, 2),
                'user_growth' => round($userGrowth, 2),
                'current_revenue' => $currentMonth,
                'last_revenue' => $lastMonth,
                'current_users' => $currentUsers,
                'last_users' => $lastMonthUsers,
            ];
        }

        $this->multiTenantService->switchToMainDatabase();

        return $growth;
    }

    // Helper methods for client-specific data
    private function getClientProductCount()
    {
        try {
            return DB::table('products')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientOrderCount()
    {
        try {
            return DB::table('orders')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientRevenue()
    {
        try {
            return DB::table('orders')
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientUserCount()
    {
        try {
            return DB::table('users')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientCategoryCount()
    {
        try {
            return DB::table('categories')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientReviewCount()
    {
        try {
            return DB::table('product_reviews')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientAvgOrderValue()
    {
        try {
            return DB::table('orders')
                ->where('status', 'completed')
                ->avg('total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientConversionRate()
    {
        try {
            $totalUsers = DB::table('users')->count();
            $usersWithOrders = DB::table('orders')->distinct('user_id')->count();
            return $totalUsers > 0 ? round(($usersWithOrders / $totalUsers) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientAvgRating()
    {
        try {
            return DB::table('product_reviews')->avg('rating') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientTopProducts()
    {
        try {
            return DB::table('products')
                ->select('name', 'price', 'stock_quantity')
                ->orderBy('stock_quantity', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getClientRecentOrders()
    {
        try {
            return DB::table('orders')
                ->select('id', 'total_amount', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getClientRevenueForPeriod($start, $end)
    {
        try {
            return DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getClientUserCountForPeriod($start, $end)
    {
        try {
            return DB::table('users')
                ->whereBetween('created_at', [$start, $end])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Global helper methods
    private function getTotalRevenue()
    {
        $clients = Client::all();
        $totalRevenue = 0;

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);
            $totalRevenue += $this->getClientRevenue();
        }

        $this->multiTenantService->switchToMainDatabase();
        return $totalRevenue;
    }

    private function getTotalOrders()
    {
        $clients = Client::all();
        $totalOrders = 0;

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);
            $totalOrders += $this->getClientOrderCount();
        }

        $this->multiTenantService->switchToMainDatabase();
        return $totalOrders;
    }

    private function getTotalProducts()
    {
        $clients = Client::all();
        $totalProducts = 0;

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);
            $totalProducts += $this->getClientProductCount();
        }

        $this->multiTenantService->switchToMainDatabase();
        return $totalProducts;
    }

    private function getTotalUsers()
    {
        $clients = Client::all();
        $totalUsers = 0;

        foreach ($clients as $client) {
            $this->multiTenantService->switchToClientDatabase($client->id);
            $totalUsers += $this->getClientUserCount();
        }

        $this->multiTenantService->switchToMainDatabase();
        return $totalUsers;
    }

    private function calculateGrowthRate()
    {
        // Calculate overall platform growth rate
        $currentMonthClients = Client::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonthClients = Client::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        return $lastMonthClients > 0 ? round((($currentMonthClients - $lastMonthClients) / $lastMonthClients) * 100, 2) : 0;
    }

    /**
     * Export analytics data to CSV
     */
    public function exportCsv()
    {
        $analytics = $this->getClientStats();
        
        $filename = 'vergeflow_analytics_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($analytics) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Client Name', 'Company', 'Domain', 'Theme', 'Status',
                'Products', 'Orders', 'Revenue', 'Users', 'Categories',
                'Reviews', 'Avg Order Value', 'Conversion Rate', 'Created At'
            ]);

            // CSV data
            foreach ($analytics as $client) {
                fputcsv($file, [
                    $client['name'],
                    $client['company_name'],
                    $client['domain'],
                    $client['theme'],
                    $client['status'],
                    $client['products_count'],
                    $client['orders_count'],
                    number_format($client['revenue'], 2),
                    $client['users_count'],
                    $client['categories_count'],
                    $client['reviews_count'],
                    number_format($client['avg_order_value'], 2),
                    $client['conversion_rate'] . '%',
                    $client['created_at']->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
