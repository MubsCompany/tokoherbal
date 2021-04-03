<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * fungsi untuk mengatur register user / Create User
     * parameter : request yang berisi semua input dari user
    */
    public function registerUser(Request $request)
    {
        /**
         * Buat variabel $data yang berisi request berupa email, name, password
         */
        $data = $request->only(['email', 'name', 'password']);

        /**
         * validasi data dari user input
         */
        $validator = Validator::make(
            $data,
            [
                'email' => 'required|string|email|unique:App\Models\User,email',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8'
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
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        /**
         * Generate token untuk user yang telah dibuat
         */
        $token = $user->createToken('api_token')->plainTextToken;

        // tampilkan response berisi user dan token, dengan response status code 200 (OK/Sukses)
        return response()->json(compact(['user', 'token']), 200);
    }

    /**
     * Fungsi untuk login user dan mendapatkan token baru
     */
    public function loginUser(Request $request)
    {
        // ambil data email dan password saja
        $data = $request->only(['email', 'password']);
        // validasi data
        $validator = Validator::make(
            $data,
            [
                'email' => 'required|string|email',
                'password' => 'required|string|min:8'
            ]
        );

        // jika validator gagal untuk validasi, tampilkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        // Auth attempt untuk mengecek apakah data (email dan password) sesuai
        if (Auth::attempt($data)) {
            $user = User::where('email', $data['email'])->first();
            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json(compact(['user', 'token'], 200));
        }
    }

    /**
     * Fungsi untuk logout / menghapus token dari database
     */
    public function logoutUser(Request $request)
    {
        // expectJson artinya request meminta return berupa JSON
        if ($request->expectsJson()) {
            // cek https://laravel.com/docs/8.x/sanctum#revoking-tokens
            $isTokenDeleted = $request->user()->currentAccessToken()->delete();
            return response()->json(compact('isTokenDeleted'), 200);
        }
    }

    /**
     * fungsi untuk mendapatkan data user / Read User
     * parameternya adalah id dari user yang telah dibuat
    */
    public function getUser($id)
    {
        // https://laravel.com/docs/8.x/eloquent#retrieving-single-models
        $user = User::find($id);
        $user->alamat;
        return response()->json(compact('user'), 200);
    }

    /**
     * fungsi untuk mengubah data user / Update User
     * parameternya adalah id dari user yang akan diubah dan request berisi input user
     */
    public function updateUser($id, Request $request)
    {
        // cari user yang akan diupdate, berdasarkan id
        $user = User::find($id);
        // jadikan object request menjadi array input
        $input = $request->all();
        // add validator untuk keamanan
        $validator = Validator::make(
            $input,
            [
                'name' => 'nullable|string|max:255',
                'photo' => 'nullable|file|image|max:2048',
                'phone_number' => 'nullable|string|max:15',
            ]
        );

        // jika validator gagal untuk validasi, tampilkan error
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(compact('errors'), 401);
        }

        if (isset($request->name)) {
            // modifikasi user bagian nama
            $user->name = $input['name'];
        }
        if (isset($request->phone_number)) {
            // modifikasi user bagian phone_number
            $user->phone_number = $input['phone_number'];
        }

        // cek apakah file yang diupload itu ada
        // diketahui dari adanya response dengan key tertentu semisal photo
        if ($request->has('photo')) {
            if (isset($user->profile_photo_path) || !empty($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $urlPath = $request->file('photo')->store('images/'. $id, 'public');
            $user->profile_photo_path = $urlPath;
        }
        // simpan user
        $user->save();

        //return user yang telah diupdate
        return response()->json(compact('user'), 200);
    }

    /**
     * fungsi untuk menghapus user / Delete User
     * parameternya adalah id dari user
     */
    public function deleteUser($id)
    {
        // cari user berdasarkan id;
        $user = User::find($id);

        // jika gagal kodenya adalah false
        // selainnya itu sukses
        $resultCode = $user->delete();

        // selain menghapus user dari database, kita perlu jg menghapus gambar user tersebut
        if (isset($user->profile_photo_path) || !empty($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        return response()->json(compact('resultCode', 'user'), 200);
    }

    /**
     * fungsi untuk mengambil semua user
     */
    public function getAllUsers(Request $request)
    {
        // cek jika ada query
        $queryName = $request->name;
        $queryEmail = $request->email;
        $users = User::query();
        if (isset($queryName)) {
            $users = $users->where('name', 'like', '%' . $queryName . '%');
        }
        if (isset($queryEmail)) {
            $users = $users->where('email', 'like', '%' . $queryEmail . '%');
        }
        /** simplePaginate untuk membuat pagination alias membatasi berapa banyak output data dalam satu request */
        $users = $users->simplePaginate(5);
        // $users = User::where('name', 'like', '%' . $queryName . '%')->where('email', 'like', '%' . $queryEmail . '%')->simplePaginate(5);

        return response()->json(compact('users'), 200);
    }
}
