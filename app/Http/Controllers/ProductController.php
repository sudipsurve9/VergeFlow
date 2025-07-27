<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $clientId = auth()->check() ? auth()->user()->client_id : 
            (\App\Models\Client::where('name', 'Vault64')->value('id'));
        $query = Product::with('category')->active()->where('client_id', $clientId);
        
        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->sort) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            }
        }
        
        $products = $query->paginate(12);
        $categories = Category::active()->where('client_id', $clientId)->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();
            
        // Track recently viewed
        $this->trackRecentlyViewed($product);
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take(4)
            ->get();
            
        // Get reviews with pagination
        $reviews = $product->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        // Check if user can review this product
        $canReview = false;
        $hasReviewed = false;
        if (auth()->check()) {
            $hasReviewed = \App\Models\ProductReview::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
                
            $canReview = !$hasReviewed && \App\Models\Order::where('user_id', auth()->id())
                ->whereHas('items', function($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->where('status', 'delivered')
                ->exists();
        }
        
        // Get recently viewed products
        $recentlyViewed = $this->getRecentlyViewed($product->id);
            
        return view('products.show', compact(
            'product', 
            'relatedProducts', 
            'reviews', 
            'canReview', 
            'hasReviewed',
            'recentlyViewed'
        ));
    }

    // Admin methods
    public function adminIndex()
    {
        $this->middleware('admin');
        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $this->middleware('admin');
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->middleware('admin');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        Product::create($data);
        
        return redirect()->route('admin.products')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $this->middleware('admin');
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $this->middleware('admin');
        
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);
        
        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->middleware('admin');
        $product = Product::findOrFail($id);
        $product->delete();
        
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);
        $importer = new \App\Imports\ProductsImport();
        $importer->import($request->file('file'));
        return back()->with('success', 'Products imported successfully!');
    }

    /**
     * Track recently viewed products
     */
    private function trackRecentlyViewed($product)
    {
        if (auth()->check()) {
            // For logged-in users
            \App\Models\RecentlyViewed::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $product->id
                ],
                [
                    'viewed_at' => now()
                ]
            );
            
            // Keep only last 20 viewed products
            $recentCount = \App\Models\RecentlyViewed::where('user_id', auth()->id())->count();
            if ($recentCount > 20) {
                $oldestViewed = \App\Models\RecentlyViewed::where('user_id', auth()->id())
                    ->orderBy('viewed_at', 'asc')
                    ->take($recentCount - 20)
                    ->get();
                \App\Models\RecentlyViewed::destroy($oldestViewed->pluck('id'));
            }
        } else {
            // For guest users (session-based)
            \App\Models\RecentlyViewed::updateOrCreate(
                [
                    'session_id' => session()->getId(),
                    'product_id' => $product->id
                ],
                [
                    'viewed_at' => now()
                ]
            );
            
            // Keep only last 10 viewed products for guests
            $recentCount = \App\Models\RecentlyViewed::where('session_id', session()->getId())->count();
            if ($recentCount > 10) {
                $oldestViewed = \App\Models\RecentlyViewed::where('session_id', session()->getId())
                    ->orderBy('viewed_at', 'asc')
                    ->take($recentCount - 10)
                    ->get();
                \App\Models\RecentlyViewed::destroy($oldestViewed->pluck('id'));
            }
        }
    }

    /**
     * Get recently viewed products
     */
    private function getRecentlyViewed($excludeProductId = null)
    {
        $query = \App\Models\RecentlyViewed::with('product')
            ->recent(8);
            
        if (auth()->check()) {
            $query->forUser(auth()->id());
        } else {
            $query->forSession(session()->getId());
        }
        
        if ($excludeProductId) {
            $query->where('product_id', '!=', $excludeProductId);
        }
        
        return $query->get()->pluck('product')->filter();
    }
}
