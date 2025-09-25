<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

//default endpoint API: http://api-5sia1.test/api

//Product Controller
//menampung semua logika dan perintah yang diarahkan
//dari endpoint url di sini (api.php).
use App\Http\Controllers\ProductController;

/**
 * API Resource untuk model Product
 */
//1. Ambil semua data Produk beserta pemiliknya (user)
//action url = [NamaController::class, 'method']
Route::get('/products/semuanya', [ProductController::class, 'index']);
//2. Cari produk tersedia berdasarkan nama
Route::get('/products/cari', [ProductController::class, 'search']);

//route ambil semua data user
//method : GET
Route::get('/users', function() {
    //panggil semua data user dan simpan dalam variabel users
    //method wiht() digunakan untuk mengikutsertakan relasi
    //relasi yang disebutkan sesuai dengan nama method pada model
    $users = User::query()->with('products')->get();
    //convert ke dalam format JSON
    $json_users = json_encode($users);
    // berikan data (response) json ke aplikasi yang meminta (request)
    return $json_users;
});

//route cari user berdasarkan id
//method GET
Route::get('/user/find', function(Request $request) {
    //cari user
    //$user = User::find($request->id);
    $user = User::query()
                ->where('id', $request->id)
                ->with('products')
                ->get();
    return json_encode($user);
});

// route cari user berdasarkan kemiripan nama atau email
// method: GET
Route::get('/user/search', function(Request $request) {
    $users = User::where('name', 'like', '%'.$request->nama.'%')
            ->orWhere('email', 'like', '%'.$request->nama.'%')
            ->get();
    // SELECT * FROM users WHERE name OR email LIKE '%ahmad%';
    return json_encode($users);
});

//registrasi user
//parameter name, email, phone, password
//password harus di hash sebelum disimpan ke tabel
Route::post('/register', function(Request $request){
    try{
        // validasi data
    $validate = $request->validate([
        //params -> rules
        'nama'      => 'required|max:255',
        'surel'     => 'required|email|unique:users,email',
        'telp'      => 'required|unique:users,phone',
        'sandi'     => 'required|min:6',
    ]);
    // tambahkan data user baru
    $new_user = User::query()->create([
        'name' => $request->nama,
        'email'=> $request->surel,
        'phone' => $request->telp,
        'password'=> Hash::make($request->sandi)
    ]);
    // return
    return response()->json($new_user);
    } catch (ValidationException $e){
    return $e->validator->errors();
    }
});

//Ubah Data User
//parameter nama, surel, telp, sandi
//method 'PUT' atau 'PATCH'
//data user yang akan diubah di cari berdasarkan id yang dikirim
//pada contoh ini, id akan langsung di asosiasikan ke model User
Route::put('/user/edit/{user}', function (Request $r, User $user) {

    //validasi ubah data
    try{
        $validate = $r->validate([
        'nama'   => 'max:255',
        'surel'  => 'email|unique:users,email,'.$user->id,
        'telp'   => 'unique:users,phone,'.$user->id,
        'sandi'  => 'min:6',
    ]);

    //----------cara yang sederhana-----------
    

    //----------cara yang kompleks------------
    //salin data yang diterima ke variabel baru
    $data = $r->all();
    //jika ada data password pada array $data
    if (array_key_exists('sandi', $data)) {
        //replace isi 'sandi' dengan hasil Hash 'sandi'
        $data['sandi'] = Hash::make($data['sandi']);
    }
    //ubah data user
    $user->update([
        'name' => $data['nama'] ?? $user->name,
        'email' => $data['surel'] ?? $user->email,
        'phone' => $data['telp'] ?? $user->phone,
        'password' => $data['sandi'] ?? $user->password
    ]);
    //kembalikan data user yang sudah diubah beserta pesan sukses 
    return response()->json([
        'pesan' => 'Sukses diubah!', 'user' => $user,
    ]);

    } catch (ValidationException $e){
    return $e->validator->errors();
    }
});

//Hapus Data User
//method 'DELETE'
//request dilakukan dengan menyertakan id user yang akan dihapus
Route:: delete('/user/delete', function (Request $r){
    //temukan user berdasarkan id yang dikirim
    $user = User::find($r->id);
    //respon jika user tidak ditemukan
    if (! $user)
        return response()->json([
    'pesan' => 'Gagal! User tidak ditemukan.'
        ]);

    //hapus data user jika ada
    $user->delete();
    return response()->json([
    'pesan' => 'Sukses! User berhasil dihapus.'
        ]);
});

//    return $request->user();
//})->middleware('auth:sanctum');
