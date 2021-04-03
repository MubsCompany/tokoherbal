<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // function create
    public function createProduct(Request $request)
    {
        /**
         * Buat variabel $data yang berisi request berupa email, name, password
         */
        $data = $request->only(['productName',  'price', 'manufacture', 'description', 'quantity']);

        /**
         * validasi data dari user input
         */
        $validator = Validator::make(
            $data,
            [
                'productName' => 'required|string',
                'price' => 'required|string',
                'manufacture' => 'required|string',
                'description' => 'required|string',
                'quantity' => 'required|integer'
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
        $product = new Product();
        $product->productName = $request->productName;
        $product->price = $request->price;
        $product->manufacture = $request->manufacture;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->save();

        // tampilkan response berisi user dan token, dengan response status code 200 (OK/Sukses)
        return response()->json(compact(['product']), 200);
    }

    // function get
    public function getProduct($productId)
    {
        // https://laravel.com/docs/8.x/eloquent#retrieving-single-models
        $product = Product::find($productId);
        return response()->json(compact('product'), 200);
    }

    // function update
    public function updateProduct($productId, Request $request)
    {
        // cari user yang akan diupdate, berdasarkan id
        $product = Product::find($productId);
        // jadikan object request menjadi array input
        $input = $request->all();
        // add validator untuk keamanan
        $validator = Validator::make(
            $input,
            [
                'productName' => 'nullable|string',
                'price' => 'nullable|string',
                'manufacture' => 'nullable|string',
                'description' => 'nullable|string',
                'quantity' => 'nullable|string'
            ]
        );

        // jika validator gagal untuk validasi, tampilkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        if (isset($request->productName)) {
            // modifikasi product bagian productName
            $product->productName = $input['productName'];
        }

        if (isset($request->price)) {
            // modifikasi product bagian price
            $product->price = $input['price'];
        }

        if (isset($request->description)) {
            // modifikasi product bagian description
            $product->description = $input['description'];
        }

        if (isset($request->manufacture)) {
            // modifikasi product bagian price
            $product->manufacture = $input['manufacture'];
        }

        if (isset($request->quantity)) {
            // modifikasi product bagian price
            $product->quantity = $input['quantity'];
        }

        // simpan user
        $product->save();

        //return user yang telah diupdate
        return response()->json(compact('user'), 200);
    }

    public function deleteProduct($productId)
    {
        // cari product berdasarkan id;
        $product = Product::find($productId);

        // jika gagal kodenya adalah false
        // selainnya itu sukses
        $resultCode = $product->delete();

        return response()->json(compact('resultCode', 'product'), 200);
    }

    public function getAllProduct(Request $request)
    {
        // cek jika ada query
        $queryProductName = $request->productName;
        $queryManufacture = $request->manufacture;
        $product = Product::query();
        if (isset($queryProductName)) {
            $product = $product->where('name', 'like', '%' . $queryProductName . '%');
        }
        if (isset($queryEmail)) {
            $product = $product->where('manufacture', 'like', '%' . $queryManufacture . '%');
        }
        /** simplePaginate untuk membuat pagination alias membatasi berapa banyak output data dalam satu request */
        $product = $product->simplePaginate(5);
        // $users = User::where('name', 'like', '%' . $queryName . '%')->where('email', 'like', '%' . $queryEmail . '%')->simplePaginate(5);

        return response()->json(compact('product'), 200);
    }
}
