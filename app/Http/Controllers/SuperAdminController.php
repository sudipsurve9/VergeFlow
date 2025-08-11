<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Hash;
use App\Services\MultiTenantService;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function dashboard()
    {
        // Ensure we're using main database for global data
        $multiTenantService = app(\App\Services\MultiTenantService::class);
        $multiTenantService->switchToMainDatabase();
        
        // Get global stats from main database
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'total_users' => User::count(),
            'total_products' => 0,
            'total_orders' => 0,
            'total_customers' => 0,
        ];

        // Aggregate stats from all client databases
        $clients = Client::all();
        foreach ($clients as $client) {
            try {
                $multiTenantService->switchToClientDatabase($client->id);
                
                // Check if tables exist before querying
                if (\Schema::hasTable('products')) {
                    $stats['total_products'] += \DB::table('products')->count();
                }
                if (\Schema::hasTable('orders')) {
                    $stats['total_orders'] += \DB::table('orders')->count();
                }
                if (\Schema::hasTable('customers')) {
                    $stats['total_customers'] += \DB::table('customers')->count();
                }
            } catch (\Exception $e) {
                // Skip if client database doesn't exist or has issues
                \Log::warning("Could not connect to client {$client->id} database: " . $e->getMessage());
            }
        }
        
        // Switch back to main database
        $multiTenantService->switchToMainDatabase();
        
        $recent_clients = Client::latest()->take(5)->get();
        $recent_orders = collect(); // Empty collection since orders are in client DBs

        return view('super_admin.dashboard', compact('stats', 'recent_clients', 'recent_orders'));
    }

    public function clients()
    {
        // Ensure we're using main database for client records
        $multiTenantService = app(\App\Services\MultiTenantService::class);
        $multiTenantService->switchToMainDatabase();
        
        // Get clients without attempting to count related tables from main DB
        $clients = Client::latest()->paginate(15);
        
        // Add counts from client databases for each client
        foreach ($clients as $client) {
            try {
                $multiTenantService->switchToClientDatabase($client->id);
                
                // Get counts from client database
                $client->users_count = \Schema::hasTable('users') ? \DB::table('users')->count() : 0;
                $client->products_count = \Schema::hasTable('products') ? \DB::table('products')->count() : 0;
                $client->orders_count = \Schema::hasTable('orders') ? \DB::table('orders')->count() : 0;
                $client->customers_count = \Schema::hasTable('customers') ? \DB::table('customers')->count() : 0;
                
            } catch (\Exception $e) {
                // If client database doesn't exist or has issues, set counts to 0
                $client->users_count = 0;
                $client->products_count = 0;
                $client->orders_count = 0;
                $client->customers_count = 0;
                \Log::warning("Could not get counts for client {$client->id}: " . $e->getMessage());
            }
        }
        
        // Switch back to main database
        $multiTenantService->switchToMainDatabase();
        
        return view('super_admin.clients.index', compact('clients'));
    }

    public function createClient()
    {
        return view('super_admin.clients.create');
    }

    public function storeClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:clients,contact_email',
            'contact_phone' => 'nullable|string|max:20',
            'domain' => 'nullable|string|unique:clients,domain',
            'subdomain' => 'nullable|string|unique:clients,subdomain',
            'address' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'theme' => 'nullable|string',
        ]);

        // Create client record in main database
        $client = Client::create($request->all());

        // Create dedicated database for the new client
        $multiTenantService = new MultiTenantService();
        $databaseCreated = $multiTenantService->createClientDatabase($client);

        if ($databaseCreated) {
            // Switch to client database to create admin user
            $multiTenantService->switchToClientDatabase($client->id);
            
            // Create admin user in client database
            $adminUser = User::create([
                'name' => $request->company_name . ' Admin',
                'email' => 'admin@' . ($request->subdomain ? $request->subdomain . '.' : '') . 'vergeflow.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'client_id' => $client->id,
            ]);
            
            // Switch back to main database
            $multiTenantService->switchToMainDatabase();
            
            $message = 'Client and database created successfully! Admin credentials: ' . $adminUser->email . ' / password123';
        } else {
            $message = 'Client created but database creation failed. Please check logs and try again.';
        }

        return redirect()->route('super_admin.clients.index')
            ->with($databaseCreated ? 'success' : 'warning', $message);
    }

    public function editClient(Client $client)
    {
        return view('super_admin.clients.edit', compact('client'));
    }

    public function updateClient(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:clients,contact_email,' . $client->id,
            'contact_phone' => 'nullable|string|max:20',
            'domain' => 'nullable|string|unique:clients,domain,' . $client->id,
            'subdomain' => 'nullable|string|unique:clients,subdomain,' . $client->id,
            'address' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'theme' => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('super_admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function deleteClient(Client $client)
    {
        $multiTenantService = new MultiTenantService();
        
        // Delete the entire client database (this removes all client data)
        $databaseDeleted = $multiTenantService->deleteClientDatabase($client->id);
        
        if ($databaseDeleted) {
            // Delete the client record from main database
            $client->delete();
            $message = 'Client and all associated data deleted successfully.';
            $type = 'success';
        } else {
            $message = 'Failed to delete client database. Please check logs and try again.';
            $type = 'error';
        }

        return redirect()->route('super_admin.clients.index')
            ->with($type, $message);
    }

    public function users()
    {
        $users = User::with('client')->latest()->paginate(15);
        return view('super_admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $clients = Client::all();
        return view('super_admin.users.create', compact('clients'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,admin,super_admin',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'client_id' => $request->client_id,
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function systemSettings()
    {
        return view('super_admin.settings');
    }

    public function updateSystemSettings(Request $request)
    {
        // Update system-wide settings
        return redirect()->route('super_admin.settings')
            ->with('success', 'System settings updated successfully.');
    }

    public function templates()
    {
        $current = \App\Models\Setting::where('key', 'site_layout')->value('value') ?? 'layouts.app';
        return view('super_admin.templates.index', compact('current'));
    }

    public function updateTemplate(Request $request)
    {
        $request->validate([
            'layout' => 'required|string',
        ]);
        
        \App\Models\Setting::updateOrCreate(
            ['key' => 'site_layout'],
            [
                'value' => $request->layout,
                'label' => 'Site Layout',
            ]
        );
        
        return redirect()->back()->with('success', 'Site template updated successfully!');
    }
}
