<?php

use App\Http\Controllers\UserController;
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
//3. Tambah product baru (CREATE)
Route::post('/products/tambah',[ProductController::class, 'store']);
//4. Baca semua product (READ) berdasarkan id
Route::get('/products/lihat/{id}', [ProductController::class, 'show']);
//5. Update produk (UPDATE)
Route::put('/products/edit/{id}', [ProductController::class, 'edit']);
//6. Delete produk (DELETE)
Route::delete('/products/hapus/{id}', [ProductController::class, 'delete']);

//route ambil semua data user
//method : GET
Route::get('/users', [UserController::class, 'index']);
//route cari user berdasarkan id
//method GET
Route::get('/user/find', [UserController::class, 'show']);
// route cari user berdasarkan kemiripan nama atau email
// method: GET
Route::get('/user/search', [UserController::class, 'search']);
//registrasi user
//parameter name, email, phone, password
//password harus di hash sebelum disimpan ke tabel
Route::post('/register', [UserController::class, 'store']);

//Ubah Data User
//parameter nama, surel, telp, sandi
//method 'PUT' atau 'PATCH'
//data user yang akan diubah di cari berdasarkan id yang dikirim
//pada contoh ini, id akan langsung di asosiasikan ke model User
Route::put('/user/edit/{user}', [UserController::class, 'update']);

//Hapus Data User
//method 'DELETE'
//request dilakukan dengan menyertakan id user yang akan dihapus
Route:: delete('/user/delete', [UserController::class, 'destroy']);

//    return $request->user();
//})->middleware('auth:sanctum');
