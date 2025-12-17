<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Show form create product
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'required',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'price'       => 'required|numeric',
        ]);

        Product::create($request->all());

        return redirect()
            ->route('products.search')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Search product by radius (10 km)
     */
    public function search(Request $request)
    {
        $latitude  = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius    = 10; // km

        $products = Product::selectRaw("
                product.*, 
                (6371 * acos(
                    cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        return view('products.list-product', compact('products'));
    }
}
