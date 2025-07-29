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

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('is_active', true)->count(),
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
        ];

        $recent_clients = Client::latest()->take(5)->get();
        $recent_orders = Order::with('user')->latest()->take(5)->get();

        return view('super_admin.dashboard', compact('stats', 'recent_clients', 'recent_orders'));
    }

    public function clients()
    {
        $clients = Client::withCount(['users', 'products', 'orders'])->latest()->paginate(15);
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

        $client = Client::create($request->all());

        // Create database for the new client
        $databaseService = new DatabaseService();
        $databaseCreated = $databaseService->createClientDatabase($client);

        // Create admin user for this client
        $adminUser = User::create([
            'name' => $request->company_name . ' Admin',
            'email' => 'admin@' . ($request->subdomain ? $request->subdomain . '.' : '') . 'vergeflow.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'client_id' => $client->id,
        ]);

        $message = 'Client created successfully. Admin credentials: ' . $adminUser->email . ' / password123';
        if (!$databaseCreated) {
            $message .= ' (Warning: Database creation failed)';
        }

        return redirect()->route('super_admin.clients.index')
            ->with('success', $message);
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
        // Delete all related data (only from tables that have client_id column)
        $client->users()->delete();
        $client->products()->delete();
        $client->categories()->delete();
        $client->orders()->delete();
        $client->customers()->delete();
        $client->coupons()->delete();
        
        // Note: The following tables are global and don't have client_id columns:
        // - settings (global application settings)
        // - banners (global banners)
        // - pages (global pages)

        $client->delete();

        return redirect()->route('super_admin.clients.index')
            ->with('success', 'Client deleted successfully.');
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
