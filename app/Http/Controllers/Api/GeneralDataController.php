<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;

class GeneralDataController extends Controller
{
    function get_data_dokter(){
        try {
            $data_mentor    = Dokter::where('jenis_dokter', 'Mentor')->with('user')->get();
            $data_psikolog  = Dokter::where('jenis_dokter', 'Psikolog')->with('user')->get();

            $data_mentor->setVisible(['spesalis', 'pengalaman', 'user']);
            foreach ($data_mentor as $key => $mentor) {
                $data_mentor[$key]->user->setVisible(['name', 'image_profile']);
            }

            $data_psikolog->setVisible(['spesalis', 'pengalaman', 'user']);
            foreach ($data_psikolog as $key => $mentor) {
                $data_psikolog[$key]->user->setVisible(['name', 'image_profile']);
            }

            return response()->json([
                'status'    => 'Berhasil',
                'mentor'    => $data_mentor,
                'psikolog'  => $data_psikolog  
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'Gagal',
                'pesan'     => $e->getMessage()
            ], 422);
        } 
    }

    function get_data_paket(){
        
    }
}
