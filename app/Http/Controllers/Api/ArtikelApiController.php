<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;

class ArtikelApiController extends Controller
{
    //buat api artikel
    function get_artikel(){
        try {
            $data_artikel      = Artikel::select('id','judul','nama_penulis','deskripsi','link_gambar')->get();

            return response()->json([
                'status'        => 'Berhasil',
                'data_artikel'  => $data_artikel,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan' => $e->getMessage()
            ], 422);
        }
    }
    function get_detail_artikel($id){
        try {
            $data_artikel      = Artikel::select('id','judul','nama_penulis','deskripsi','link_gambar')->where('id',$id)->first();

            return response()->json([
                'status'        => 'Berhasil',
                'data_artikel'  => $data_artikel,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan' => $e->getMessage()
            ], 422);
        }
    }
}
