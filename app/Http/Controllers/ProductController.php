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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
