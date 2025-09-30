<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationData;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Tampilkan semua data Produk.
     * Beserta pemiliknya (user)
     */
    public function index()
    {
        //untuk memanggil relasi terkait, sebutkan
        //nama method relasi yang ada di model tersebut.
        //gunakan method with() untuk menyertakan relasi tabel
        //pada data yang dipanggil.
        $products = Product::query()
            ->where('is_available', true) //hanya produk tersedia
            ->with('user')                       //sertakan pemiliknya
            ->get();                                        //eksekusi query
        //format respon ada status (Sukses/Gagal) dan data
        return response()->json([
            'status' => 'Sukses',
            'data'  => $products
        ]);
    }

    /**
     * Cari produk berdasarkan 'name'
     * dan ikutkan relasinya
     */
    public function search(Request $req)
    {
        //validasi minimal 3 huruf untuk pencarian
        try{
            //validasi minimal 3 huruf untuk pencarian
            $validated = $req->validate([
                'teks' => 'required|min:3',
            ], [
                //pesan error custom
                'teks.required' => ':Attribute jangan dikosongkan lah!',
                'teks.min'      => 'Ini :attribute kurang dari :min bos!',
            ], [
                //custom attribute
                'teks'  => 'huruf'
            ]);

            //proses pencarian produk berdasarkan
            $products = Product::query()
                    ->where('name', 'like', '%'.$req->teks.'%')
                    ->with('user')
                    ->get();
            return response()->json([
                'pesan' => 'Sukses!',
                'data' => $products,
            ]);

        } catch (ValidationException $ex) {
            return response()->json([
                'pesan' => 'Gagal!',
                'data' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $req)
    {
         try{
        // validasi data
    $validate = $req->validate([
        //params -> rules
        'nama'      => 'required|min:3|max:255',
        'deskripsi' => 'required|min:10',
        'harga'     => 'required|numeric|min:0',
        'stok'      => 'required|integer|min:0',
        'user_id'   => 'required|exists:users,id',
    ]);
    // tambahkan data user baru
    $new_user = Product::query()->create([
        'name'          => $req->nama,
        'description'   => $req->deskripsi,
        'price'         => $req->harga,
        'stock'         => $req->stok,
        'user_id'       => $req->user_id,
        'is_available'  => true,
    ]);
    // return
    return response()->json($new_user);
    } catch (ValidationException $e){
    return $e->validator->errors();
    }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Request $req)
    {
        $product = Product::query()
                    ->where('id', $req->id)
                    ->with('user')
                    ->first();

        //jika produk tidak ditemukan
        if (!$product) {
            return response()->json([
                'pesan' => 'Gagal! Produk tidak ditemukan.',
                'data' => null
            ]);
        }

        return response()->json([
            'pesan' => 'Sukses!',
            'data' => $product,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $req)
    {
        try {
        $product = Product::find($req->id);
        
        if (!$product) {
            return response()->json([
                'pesan' => 'Gagal! Produk tidak ditemukan.'
            ]);
        }

        $data = $req->all();

        $product->update([
            'name' => $data['nama'] ?? $product->name,
            'description' => $data['deskripsi'] ?? $product->description,
            'price' => $data['harga'] ?? $product->price,
            'stock' => $data['stok'] ?? $product->stock,
            'is_available' => $data['tersedia'] ?? $product->is_available,
        ]);
        return response()->json([
            'pesan' => 'Sukses! Produk berhasil diubah.',
            'data' => $product->load('user'),
        ]);

    } catch (ValidationException $ex) {
        return response()->json([
            'pesan' => 'Gagal!',
            'data' => $ex->validator->errors(),
        ]);
    }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function delete(Request $req)
    {
        $product = Product::find($req->id);
    //respon jika user tidak ditemukan
    if (! $product)
        return response()->json([
    'pesan' => 'Gagal! User tidak ditemukan.'
        ]);

    //hapus data user jika ada
    $product->delete();
    return response()->json([
    'pesan' => 'Sukses! User berhasil dihapus.'
        ]);
    }
}
