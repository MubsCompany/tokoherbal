<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    // function create
    public function createMedia(Request $request)
    {
        /**
         * Buat variabel $data yang berisi request berupa email, name, password
         */
        $data = $request->only(['name',  'url_path']);

        /**
         * validasi data dari user input
         */
        $validator = Validator::make(
            $data,
            [
                   'name' => 'required|string',
                   'url_path' => 'required|string',
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
        $media = new Media();
        $media->quantity = $request->name;
        $media->is_checkout = $request->url_path;
        $media->save();

        // tampilkan response berisi user dan token, dengan response status code 200 (OK/Sukses)
        return response()->json(compact(['media']), 200);
    }

    // function get
    public function getMedia($productId)
    {
        // https://laravel.com/docs/8.x/eloquent#retrieving-single-models
        $media = Media::find($productId);
        return response()->json(compact('media'), 200);
    }

    // function update
    public function updateMedia($mediaId, Request $request)
    {
        // cari cart yang akan diupdate, berdasarkan id
        $media = Media::find($mediaId);
        // jadikan object request menjadi array input
        $input = $request->all();
        // add validator untuk keamanan
        $validator = Validator::make(
            $input,
            [
                       'product_id' => 'nullable|integer',
                       'name' => 'nullable|string',
                       'url_path' => 'nullable|string',
                   ]
        );

        // jika validator gagal untuk validasi, tampilkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        if (isset($request->product_id)) {
            // modifikasi cart bagian quantity
            $media->product_id = $input['product_id'];
        }

        if (isset($request->name)) {
            // modifikasi cart bagian quantity
            $media->name = $input['name'];
        }

        if (isset($request->url_path)) {
            // modifikasi cart bagian is checkout
            $media->url_path = $input['url_path'];
        }

        // simpan user
        $media->save();

        //return user yang telah diupdate
        return response()->json(compact('media'), 200);
    }

    // function delete
    public function deleteMedia($mediaId)
    {
        // cari product berdasarkan id;
        $media = Media::find($mediaId);

        // jika gagal kodenya adalah false
        // selainnya itu sukses
        $resultCode = $media->delete();

        return response()->json(compact('product_id', 'name', 'url_path'), 200);
    }
}
