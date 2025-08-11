<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remove auth middleware to allow public access
    }

    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (!auth()->check()) {
            return view('welcome');
        }
        // Get categories without active scope since categories table doesn't have is_active column
        $categories = Category::withCount('products')->take(6)->get();
        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->orWhere('sale_price', '>', 0)
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }
}
