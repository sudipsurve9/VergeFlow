<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $multiTenantService = new MultiTenantService();
        
        // Demo clients data
        $demoClients = [
            [
                'name' => 'TechGear Pro',
                'company_name' => 'TechGear Pro Electronics',
                'contact_email' => 'admin@techgearpro.com',
                'contact_phone' => '+1-555-0101',
                'domain' => 'techgearpro.com',
                'subdomain' => 'techgear',
                'address' => '123 Tech Street, Silicon Valley, CA 94025',
                'primary_color' => '#2563eb',
                'secondary_color' => '#1e40af',
                'theme' => 'modern',
                'description' => 'Premium electronics and gadgets store',
                'categories' => [
                    ['name' => 'Smartphones', 'description' => 'Latest smartphones and accessories'],
                    ['name' => 'Laptops', 'description' => 'High-performance laptops and notebooks'],
                    ['name' => 'Gaming', 'description' => 'Gaming gear and accessories'],
                    ['name' => 'Audio', 'description' => 'Headphones, speakers, and audio equipment'],
                ],
                'products' => [
                    [
                        'name' => 'iPhone 15 Pro Max',
                        'description' => 'Latest iPhone with titanium design and A17 Pro chip',
                        'price' => 1199.99,
                        'sale_price' => 1099.99,
                        'sku' => 'IPH15PM-256',
                        'stock_quantity' => 50,
                        'category' => 'Smartphones',
                        'featured' => true,
                    ],
                    [
                        'name' => 'MacBook Pro 16"',
                        'description' => 'Powerful laptop with M3 Pro chip for professionals',
                        'price' => 2499.99,
                        'sale_price' => null,
                        'sku' => 'MBP16-M3P',
                        'stock_quantity' => 25,
                        'category' => 'Laptops',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Gaming Mechanical Keyboard',
                        'description' => 'RGB backlit mechanical keyboard for gaming',
                        'price' => 149.99,
                        'sale_price' => 129.99,
                        'sku' => 'GMK-RGB-001',
                        'stock_quantity' => 100,
                        'category' => 'Gaming',
                        'featured' => false,
                    ],
                    [
                        'name' => 'Wireless Noise-Canceling Headphones',
                        'description' => 'Premium wireless headphones with active noise cancellation',
                        'price' => 349.99,
                        'sale_price' => 299.99,
                        'sku' => 'WNC-HP-001',
                        'stock_quantity' => 75,
                        'category' => 'Audio',
                        'featured' => true,
                    ],
                ]
            ],
            [
                'name' => 'Fashion Forward',
                'company_name' => 'Fashion Forward Boutique',
                'contact_email' => 'admin@fashionforward.com',
                'contact_phone' => '+1-555-0202',
                'domain' => 'fashionforward.com',
                'subdomain' => 'fashion',
                'address' => '456 Fashion Ave, New York, NY 10001',
                'primary_color' => '#ec4899',
                'secondary_color' => '#db2777',
                'theme' => 'luxury',
                'description' => 'Trendy fashion and accessories boutique',
                'categories' => [
                    ['name' => 'Women\'s Clothing', 'description' => 'Stylish women\'s fashion'],
                    ['name' => 'Men\'s Clothing', 'description' => 'Modern men\'s apparel'],
                    ['name' => 'Accessories', 'description' => 'Fashion accessories and jewelry'],
                    ['name' => 'Shoes', 'description' => 'Designer shoes and footwear'],
                ],
                'products' => [
                    [
                        'name' => 'Designer Evening Dress',
                        'description' => 'Elegant evening dress perfect for special occasions',
                        'price' => 299.99,
                        'sale_price' => 249.99,
                        'sku' => 'DED-BLK-M',
                        'stock_quantity' => 20,
                        'category' => 'Women\'s Clothing',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Premium Leather Jacket',
                        'description' => 'Genuine leather jacket with modern styling',
                        'price' => 449.99,
                        'sale_price' => null,
                        'sku' => 'PLJ-BRN-L',
                        'stock_quantity' => 15,
                        'category' => 'Men\'s Clothing',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Gold Chain Necklace',
                        'description' => '18k gold plated chain necklace',
                        'price' => 89.99,
                        'sale_price' => 69.99,
                        'sku' => 'GCN-18K-001',
                        'stock_quantity' => 50,
                        'category' => 'Accessories',
                        'featured' => false,
                    ],
                    [
                        'name' => 'Designer High Heels',
                        'description' => 'Comfortable designer high heels for any occasion',
                        'price' => 199.99,
                        'sale_price' => 179.99,
                        'sku' => 'DHH-BLK-8',
                        'stock_quantity' => 30,
                        'category' => 'Shoes',
                        'featured' => true,
                    ],
                ]
            ],
            [
                'name' => 'Green Garden',
                'company_name' => 'Green Garden Supplies',
                'contact_email' => 'admin@greengarden.com',
                'contact_phone' => '+1-555-0303',
                'domain' => 'greengarden.com',
                'subdomain' => 'garden',
                'address' => '789 Garden Lane, Portland, OR 97201',
                'primary_color' => '#16a34a',
                'secondary_color' => '#15803d',
                'theme' => 'ecomarket',
                'description' => 'Organic gardening supplies and plants',
                'categories' => [
                    ['name' => 'Seeds', 'description' => 'Organic seeds for vegetables and flowers'],
                    ['name' => 'Tools', 'description' => 'High-quality gardening tools'],
                    ['name' => 'Fertilizers', 'description' => 'Organic fertilizers and soil amendments'],
                    ['name' => 'Plants', 'description' => 'Live plants and seedlings'],
                ],
                'products' => [
                    [
                        'name' => 'Organic Tomato Seeds',
                        'description' => 'Heirloom organic tomato seeds - variety pack',
                        'price' => 12.99,
                        'sale_price' => 9.99,
                        'sku' => 'OTS-VAR-001',
                        'stock_quantity' => 200,
                        'category' => 'Seeds',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Professional Pruning Shears',
                        'description' => 'High-carbon steel pruning shears with ergonomic grip',
                        'price' => 34.99,
                        'sale_price' => null,
                        'sku' => 'PPS-ERG-001',
                        'stock_quantity' => 75,
                        'category' => 'Tools',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Organic Compost Mix',
                        'description' => 'Premium organic compost for healthy soil',
                        'price' => 19.99,
                        'sale_price' => 16.99,
                        'sku' => 'OCM-20LB-001',
                        'stock_quantity' => 150,
                        'category' => 'Fertilizers',
                        'featured' => false,
                    ],
                    [
                        'name' => 'Herb Garden Starter Kit',
                        'description' => 'Complete kit with basil, oregano, and thyme plants',
                        'price' => 24.99,
                        'sale_price' => 19.99,
                        'sku' => 'HGS-KIT-001',
                        'stock_quantity' => 40,
                        'category' => 'Plants',
                        'featured' => true,
                    ],
                ]
            ]
        ];

        $this->command->info('Creating demo clients...');

        foreach ($demoClients as $clientData) {
            $this->command->info("Creating client: {$clientData['name']}");

            // Create client record in main database
            $client = Client::updateOrCreate(
                ['contact_email' => $clientData['contact_email']],
                [
                    'name' => $clientData['name'],
                    'company_name' => $clientData['company_name'],
                    'contact_phone' => $clientData['contact_phone'],
                    'domain' => $clientData['domain'],
                    'subdomain' => $clientData['subdomain'],
                    'address' => $clientData['address'],
                    'primary_color' => $clientData['primary_color'],
                    'secondary_color' => $clientData['secondary_color'],
                    'theme' => $clientData['theme'],
                    'status' => 'active',
                ]
            );

            // Create or verify client database
            $databaseCreated = $multiTenantService->createClientDatabase($client);
            
            if ($databaseCreated) {
                $this->command->info("✅ Client database created for {$client->name}");
                
                // Switch to client database
                $multiTenantService->switchToClientDatabase($client->id);
                
                // Create admin user in client database
                $adminUser = User::updateOrCreate(
                    ['email' => $clientData['contact_email']],
                    [
                        'name' => $client->name . ' Admin',
                        'email' => $clientData['contact_email'],
                        'password' => Hash::make('password123'),
                        'role' => 'admin',
                        'client_id' => $client->id,
                        'email_verified_at' => now(),
                    ]
                );

                // Create categories
                $categoryIds = [];
                foreach ($clientData['categories'] as $categoryData) {
                    $category = DB::table('categories')->updateOrInsert(
                        ['name' => $categoryData['name']],
                        [
                            'name' => $categoryData['name'],
                            'description' => $categoryData['description'],
                            'client_id' => $client->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    
                    $categoryRecord = DB::table('categories')->where('name', $categoryData['name'])->first();
                    if ($categoryRecord) {
                        $categoryIds[$categoryData['name']] = $categoryRecord->id;
                    }
                }

                // Create products
                foreach ($clientData['products'] as $productData) {
                    $categoryId = $categoryIds[$productData['category']] ?? null;
                    
                    DB::table('products')->updateOrInsert(
                        ['sku' => $productData['sku']],
                        [
                            'name' => $productData['name'],
                            'description' => $productData['description'],
                            'price' => $productData['price'],
                            'sale_price' => $productData['sale_price'],
                            'sku' => $productData['sku'],
                            'stock_quantity' => $productData['stock_quantity'],
                            'category_id' => $categoryId,
                            'client_id' => $client->id,
                            'featured' => $productData['featured'],
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                $this->command->info("✅ Seeded {$client->name} with categories and products");
            } else {
                $this->command->warn("⚠️  Database already exists for {$client->name}");
            }
        }

        // Switch back to main database
        $multiTenantService->switchToMainDatabase();
        
        $this->command->info('✅ Demo clients creation completed!');
        $this->command->info('');
        $this->command->info('Demo Clients Created:');
        $this->command->info('1. TechGear Pro (Electronics) - subdomain: techgear');
        $this->command->info('2. Fashion Forward (Fashion) - subdomain: fashion');
        $this->command->info('3. Green Garden (Gardening) - subdomain: garden');
        $this->command->info('');
        $this->command->info('Admin credentials for all clients:');
        $this->command->info('Email: admin@[clientdomain].com');
        $this->command->info('Password: password123');
    }
}
