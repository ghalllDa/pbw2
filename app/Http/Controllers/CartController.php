<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        //
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        $cartCount = $cartItems->sum('quantity');
        return view('cart.index', compact('cartItems', 'cartCount'));
    }

    public function checkout() 
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->total_price;
        });

        $order = Order::create([
            'order_id' => 'ORD' . rand(1000, 9999),
            'user_id' => Auth::id(),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        foreach ($cartItems as $item) {
            $order->orderDetails()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('cart.index')->with('success', 'Your order has been placed successfully!');
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ],

            [
                'quantity' => $request->input('quantity', 1), 
                'total_price' => Product::find($request->product_id)->price * $request->input('quantity', 1),
            ]
        );

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->total_price = $cartItem->product->price * $request->quantity;
        $cartItem->save();

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
    }

    public function destroy($id)
    {
        //
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();
        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }
}
