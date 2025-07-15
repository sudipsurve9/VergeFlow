<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(function($item) {
            return $item->total;
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock available');
        }

        $cartItem = $this->getCartItem($request->product_id);
        
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'session_id' => session()->getId()
            ]);
        }

        return back()->with('success', 'Product added to cart successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = $this->getCartItemById($id);
        
        if (!$cartItem) {
            return back()->with('error', 'Cart item not found');
        }

        if ($cartItem->product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock available');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated successfully');
    }

    public function remove($id)
    {
        $cartItem = $this->getCartItemById($id);
        
        if ($cartItem) {
            $cartItem->delete();
        }

        return back()->with('success', 'Item removed from cart');
    }

    public function clear()
    {
        $this->getCartItems()->each(function($item) {
            $item->delete();
        });

        return back()->with('success', 'Cart cleared successfully');
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::with('product')->where('user_id', Auth::id())->get();
        } else {
            return CartItem::with('product')->where('session_id', session()->getId())->get();
        }
    }

    private function getCartItem($productId)
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();
        } else {
            return CartItem::where('session_id', session()->getId())
                ->where('product_id', $productId)
                ->first();
        }
    }

    private function getCartItemById($id)
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();
        } else {
            return CartItem::where('session_id', session()->getId())
                ->where('id', $id)
                ->first();
        }
    }

    public function getCartCount()
    {
        $count = $this->getCartItems()->sum('quantity');
        return response()->json(['count' => $count]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = \App\Models\Coupon::where('code', $request->coupon_code)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        // Record coupon usage for the user
        \App\Models\CouponUsage::firstOrCreate([
            'user_id' => auth()->id(),
            'coupon_id' => $coupon->id,
        ], [
            'discount_amount' => 0
        ]);

        session(['applied_coupon' => $coupon->code]);
        return back()->with('success', 'Coupon applied successfully!');
    }
}
