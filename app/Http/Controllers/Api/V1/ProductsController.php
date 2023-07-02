<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cart;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class ProductsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = Products::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully stored',
            'data' => $product,
        ]);
        }

    public function update(Request $request, $id) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        // Find the product by ID
        $product = Products::findOrFail($id);

        //update the product
        $product->name = $validatedData['name'];
        $product->price = $validatedData['price'];

        //save the updated product 
        $product->save();

       

        return response()->json([
            'status' => 'success', 
            'message' => 'updated successfully',
            'data' => $product,
        ]);
    }

    public function destroy($id)
    {
    // Find the product by ID
    $product = Products::findOrFail($id);

    // Delete the product
    $product->delete();

    return response()->json([
        'status' => 'success', 
        'message' => 'deleted successfully',
        'data' => $product,
    ]);
    }

public function addToCart(Request $request, $id) 
    {

        if(Auth::id()) 
        {
            $user = auth()->user();
            $product = Products::find($id);
            $cart = new Cart;

            $cart->name = $user->name;
            $cart->contact = $user->contact;
            $cart->address = $user->address;

            $cart->product_title = $product->name;
            $cart->price = $product->price;
            $cart->quantity = $request->quantity;

            $cart->save();

            return redirect()->back()->with('message', 'Product Added Succesfully');
        }
        else 
        {
            return redirect('login');
        }

    
    }
}
