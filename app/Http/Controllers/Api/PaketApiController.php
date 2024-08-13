<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori_Layanan;
use App\Models\Paket;
use Illuminate\Http\Request;

class PaketApiController extends Controller
{
    function get_paket(){
        try {
            // Ambil semua paket dengan relasi detail_paket dan kategori_layanan
            $data_paket = Paket::with(['detail_paket', 'kategori_layanan'])->get();

            // Buat array untuk menyimpan data paket yang sudah dikelompokkan berdasarkan kategori_layanan
            $grouped_data_paket = [];

            // Loop melalui setiap paket dan kelompokkan berdasarkan kategori_layanan
            foreach ($data_paket as $paket) {
                $kategori = $paket->kategori_layanan->nama; // Ambil nama kategori layanan
                $grouped_data_paket[$kategori][] = $paket;  // Kelompokkan paket berdasarkan kategori layanan
            }

            return response()->json([
                'status'        => 'Berhasil',
                'data_paket'    => $grouped_data_paket,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan' => $e->getMessage()
            ], 422);
        }
    }
}
