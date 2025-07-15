<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlists = Wishlist::with('product')->where('user_id', Auth::id())->get();
        return view('wishlist.index', compact('wishlists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);
        if ($request->ajax()) {
            return response()->json(['status' => 'added', 'wishlist_id' => $wishlist->id]);
        }
        return back()->with('success', 'Product added to wishlist!');
    }

    public function destroy($id, Request $request)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $wishlist->delete();
        if ($request->ajax()) {
            return response()->json(['status' => 'removed']);
        }
        return back()->with('success', 'Product removed from wishlist.');
    }
} 