<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Alamat;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    /**
     * Membuat fungsi create Alamat
     */
    public function createAlamat($id, Request $request)
    {
        // dapatkan semua input dari request user dan jadikan ke array
        $input = $request->all();

        // validasi input agar sesuai yg dibutuhkan database
        $validator = Validator::make(
            $input,
            [
                'alamat' => 'required|string',
                'provinsi' => 'required|string',
                'kota' => 'required|string',
                'kecamatan' => 'required|string',
                'desa' => 'required|string',
                'kodepos' => 'required|string',
            ]
        );

        // jika validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        // cari alamat berdasarkan user_id, jika tidak ada maka buat alamat
        $alamat = Alamat::firstOrCreate(['user_id' => $id]);
        $alamat->alamat = $input['alamat'];
        $alamat->provinsi = $input['provinsi'];
        $alamat->kota = $input['kota'];
        $alamat->kecamatan = $input['kecamatan'];
        $alamat->desa = $input['desa'];
        $alamat->kodepos = $input['kodepos'];
        $alamat->save();

        // update alamat_id pada user
        $user = User::find($id);
        $user->alamat_id = $alamat->id;
        $user->save();

        return response()->json(compact('alamat'), 200);
    }

    /**
     * fungsi untuk menampilkan alamat / read alamat
     * berdasarkan user id
     */
    public function getAlamat($id)
    {
        // cari alamat berdasarkan user_id
        $alamat = Alamat::firstWhere('user_id', $id);
        return response()->json(compact('alamat'), 200);
    }

    /**
     * fungsi hapus alamat / delete alamat
     * parameternya user id
     */
    public function deleteAlamat($id)
    {
        $alamat = Alamat::firstWhere('user_id', $id);
        if (isset($alamat)) {
            $isDeleteSuccess = $alamat->delete();
            return response()->json(compact(['is_delete_user', 'alamat']), 200);
        }
        return response()->json('', 404);
    }
}
