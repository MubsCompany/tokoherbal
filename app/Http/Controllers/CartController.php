<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // function create
    public function createCart(Request $request)
    {
        /**
         * Buat variabel $data yang berisi request berupa email, name, password
         */
        $data = $request->only(['quantity',  'is_checkout']);

        /**
         * validasi data dari user input
         */
        $validator = Validator::make(
            $data,
            [
                'quantity' => 'required|integer',
                'is_checkout' => 'required|string',
            ]
        );
        // jika validator gagal untuk memvalidasi, maka munculkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        /**
         * Buat User sesuai $data tersebut
         */
        $cart = new Cart();
        $cart->quantity = $request->quantity;
        $cart->is_checkout = $request->is_checkout;
        $cart->save();

        // tampilkan response berisi user dan token, dengan response status code 200 (OK/Sukses)
        return response()->json(compact(['cart']), 200);
    }

    // function get
    public function getCart($cartId)
    {
        // https://laravel.com/docs/8.x/eloquent#retrieving-single-models
        $cart = Cart::find($cartId);
        return response()->json(compact('cart'), 200);
    }

    // function update
    public function updateCart($cartId, Request $request)
    {
        // cari cart yang akan diupdate, berdasarkan id
        $cart = Cart::find($cartId);
        // jadikan object request menjadi array input
        $input = $request->all();
        // add validator untuk keamanan
        $validator = Validator::make(
            $input,
            [
                   'quantity' => 'nullable|integer',
                   'is_checkout' => 'nullable|string',
               ]
        );

        // jika validator gagal untuk validasi, tampilkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        if (isset($request->quantity)) {
            // modifikasi cart bagian quantity
            $cart->quantity = $input['quantity'];
        }

        if (isset($request->is_checkout)) {
            // modifikasi cart bagian is checkout
            $cart->is_checkout = $input['is_checkout'];
        }

        // simpan user
        $cart->save();

        //return user yang telah diupdate
        return response()->json(compact('cart'), 200);
    }

    // function delete
    public function deleteCart($cartId)
    {
        // cari product berdasarkan id;
        $cart = Cart::find($cartId);

        // jika gagal kodenya adalah false
        // selainnya itu sukses
        $resultCode = $cart->delete();

        return response()->json(compact('resultCode', 'cart'), 200);
    }

    // function get all dan get sebagian
    // public function getAllCart(Request $request)
    // {
    //     // cek jika ada query
    //     $queryProductName = $request->productName;
    //     $queryManufacture = $request->manufacture;
    //     $product = Product::query();
    //     if (isset($queryProductName)) {
    //         $product = $product->where('name', 'like', '%' . $queryProductName . '%');
    //     }
    //     if (isset($queryEmail)) {
    //         $product = $product->where('manufacture', 'like', '%' . $queryManufacture . '%');
    //     }
    //     /** simplePaginate untuk membuat pagination alias membatasi berapa banyak output data dalam satu request */
    //     $product = $product->simplePaginate(5);
    //     // $users = User::where('name', 'like', '%' . $queryName . '%')->where('email', 'like', '%' . $queryEmail . '%')->simplePaginate(5);

    //     return response()->json(compact('cart'), 200);
    // }
}
