<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Kategori_Layanan;
use App\Models\Key_Link;
use App\Models\Order;
use App\Models\Paket;
use App\Models\Sesi;
use App\Models\Sesi_User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SesiApiController extends Controller
{
    function get_layanan(){
        try {
            $user_id        = Auth::guard('api')->user()['id'];
            $data_dokter    = Dokter::where('id_user', $user_id)->first();

            if ($data_dokter->jenis_dokter == 'Mentor'){
                $akses = 'mentor';
                $data_layanan      = Kategori_Layanan::where('akses', $akses)->get();
            } else {
                $data_layanan      = Kategori_Layanan::all();
            }

            return response()->json([
                'status'        => 'Berhasil',
                'data_layanan'  => $data_layanan,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage()
            ], 422);
        }
    }
    function get_sesi_dokter(){
        try {
            $user_id        = Auth::guard('api')->user()['id'];
            $data_dokter    = Dokter::where('id_user', $user_id)->first();
            $data_sesi      = Sesi::where('id_dokter', $data_dokter->id)->with('kategori_layanan')->get();

            return response()->json([
                'status'    => 'Berhasil',
                'data_sesi' => $this->data_sesi_dokter($data_sesi),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan' => $e->getMessage()
            ], 422);
        }
    }

    public function data_sesi_dokter($data) {
        $data_new = $data->transform(function ($sesi) {
            $status_sesi = Key_Link::where('id_sesi', $sesi->id)->exists() ? 'Sesi Tidak Tersedia' : 'Sesi Masih Tersedia';

            return [
                'id'            => $sesi->id,
                'jenis_pelayanan' => $sesi->jenis_pelayanan,
                'tanggal'       => $sesi->tanggal,
                'waktu_mulai'   => $sesi->waktu_mulai,
                'waktu_selesai' => $sesi->waktu_selesai,
                'status_sesi'   => $status_sesi,
            ];
        });

        return $data_new->toArray();
    }

    function layanan_order(){
        $user_id    = Auth::guard('api')->user()['id'];
        $now       = now();

        $data_order = Order::where('id_user', $user_id)
            ->where('status_pembayaran', 1)
            ->with(['paket.kategori_layanan'])
            ->get();

        $filtered_orders = $data_order->filter(function($order) use ($now, $user_id) {
            $tanggal_kadaluarsa_paket     = Carbon::parse($order->tanggal_kadaluarsa);
            // $bulan_paket            = $order->paket->kadaluarsa;
            // $tanggal_kadaluarsa_paket = $tanggal_kadaluarsa->addMonths($bulan_paket);

            $cek_sesi   = Sesi_User::where('id_user', $user_id)->where('id_order', $order->id)->first();
            if (!isset($cek_sesi)) {
                $new_sesi   = new Sesi_User();
                $new_sesi->id_user       = $user_id;
                $new_sesi->id_order      = $order->id;
                $new_sesi->sesi_terpakai = 0;
                $new_sesi->batas_waktu   = $tanggal_kadaluarsa_paket;

                $new_sesi->save();
            }

            return $now->lt($tanggal_kadaluarsa_paket);
        });

        $data_layanan = $filtered_orders->groupBy('paket.fitur');

        $data_layanan = $data_layanan->map(function ($group) {
            $max_sesi = $group->sum('paket.max_sesi');

            $group_new = $group->each(function ($order) use ($max_sesi) {
                $order->paket->max_sesi = $max_sesi;
            });
            return $group_new->unique('paket.fitur');
        })->flatten();

        return response()->json([
            'status'        => 'Berhasil',
            'data_layanan'  => $this->convert_data_paket($data_layanan),
        ], 200);
    }

    public function convert_data_paket($data){
        $data_new = $data->transform(function ($order)   {
            return [
                'id_layanan'    => $order->paket->id_kategori_layanan,
                'kadarluarsa'   => $order->tanggal_kadaluarsa,
                'nama_layanan'  => $order->paket->kategori_layanan->nama,
                'jenis_fitur'   => $order->paket->fitur,
                'nama_paket'    => $order->paket->nama_paket,
                'sesi_tersedia' => $order->paket->max_sesi - Sesi_User::where('id_user', $order->id_user)->where('id_order', $order->id)->first()->sesi_terpakai
            ];
        });

        return $data_new->toArray();
    }


    function get_available_sesi(Request $request){
        $validator = Validator::make($request->all(), [
            'id_layanan' => 'required|string',
            'tanggal'    => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $user_id    = Auth::guard('api')->user()['id'];
            $now        = now();

            $data_sesi_fitur = Sesi_User::where('id_user', $user_id)->with(['order', 'order.paket'])->get();
            $filtered_fitur = $data_sesi_fitur->filter(function($sesi) use ($now) {
                $tanggal_kadaluarsa_paket     = Carbon::parse($sesi->order->tanggal_kadaluarsa);
                return $tanggal_kadaluarsa_paket >= $now;
            });

            $filtered_fitur = $filtered_fitur->filter(function($sesi) {
                return $sesi->order->paket->max_Sesi >= $sesi->sesi_terpakai;
            });
            $data_fitur = $filtered_fitur->pluck('order.paket.fitur')->flatten();

            $data_sesi  = Sesi::where('tanggal', $request->tanggal)->where('id_kategori_layanan', $request->id_layanan)->with(['dokter', 'dokter.user'])->get();
            $filtered_sesi = $data_sesi->filter(function($sesi) use ($data_fitur) {
                return $data_fitur->contains($sesi->jenis_pelayanan);
            });

            $excluded_sesi_ids = Key_Link::pluck('id_sesi');
            $filtered_sesi = $filtered_sesi->filter(function($sesi) use ($excluded_sesi_ids) {
                return !$excluded_sesi_ids->contains($sesi->id);
            });

            $data_layanan = $filtered_fitur->groupBy('order.paket.fitur');
            $data_layanan = $data_layanan->map(function ($group) {
                $max_sesi = $group->max('order.paket.max_durasi');

                $group_new = $group->each(function ($sesi_user) use ($max_sesi) {
                    $sesi_user->order->paket->max_durasi = $max_sesi;
                });
                return $group_new->unique('order.paket.fitur');
            })->flatten();

            $filtered_sesi = $filtered_sesi->filter(function($sesi) use($data_layanan) {
                $fitur = $sesi->jenis_pelayanan;
                $max_durasi = $data_layanan->where('order.paket.fitur', $fitur)->max('order.paket.max_durasi');

                $start_time = Carbon::createFromFormat('H:i:s', $sesi->waktu_mulai);
                $end_time = Carbon::createFromFormat('H:i:s', $sesi->waktu_selesai);
                $duration   = $start_time->diffInMinutes($end_time);
                $sesi->total_durasi = $duration.' Menit';

                return $duration <= $max_durasi;
            });

            $filtered_sesi = $filtered_sesi->filter(function($sesi)  {
                $date_now   = explode(' ', now());
                $time       = $date_now[1];

                if ($sesi->tanggal == $date_now[0]){
                    return $sesi->waktu_mulai >= $time;
                } else {
                    return $sesi->tanggal >= $date_now[0];
                }
            });

            return response()->json($this->data_sesi_user($filtered_sesi), 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage()
            ], 422);
        }
    }

    public function data_sesi_user($data){
        $data_new = $data->transform(function ($sesi) {
            return [
                'id'                => $sesi->id,
                'jenis_pelayanan'   => $sesi->jenis_pelayanan,
                'tanggal'       => $sesi->tanggal,
                'waktu_mulai'   => $sesi->waktu_mulai,
                'waktu_selesai' => $sesi->waktu_selesai,
                'durasi'        => $sesi->total_durasi,
                'nama_dokter'       => $sesi->dokter->user->name,
                "pengalaman_dokter" => $sesi->dokter->pengalaman,
                "jenis_dokter"      => $sesi->dokter->jenis_dokter
            ];
        });

        $data_sesi = [];
        foreach ($data_new as $sesi) {
            $data_sesi[] = $sesi;
        }

        return ['status'    => 'Berhasil',
                'data_sesi' => $data_sesi];
    }

    function use_sesi(Request $request){
        $validator = Validator::make($request->all(), [
            'id_sesi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $user_id    = Auth::guard('api')->user()['id'];
            $sesi       = Sesi::where('id', $request->id_sesi)->with('dokter')->first();
            $sesi_user = Sesi_User::where('id_user', $user_id)
                ->whereHas('order.paket', function($query) use ($sesi) {
                    $query->where('fitur', $sesi->jenis_pelayanan)
                        ->whereRaw('sesi_terpakai < max_sesi');
                })
                ->with(['order', 'order.paket'])
                ->first();

            $sesi_user->sesi_terpakai = $sesi_user->sesi_terpakai+1;
            $sesi_user->update();

            $METERED_DOMAIN     = env('METERED_DOMAIN');
            $METERED_SECRET_KEY = env('METERED_SECRET_KEY');

            if($sesi->jenis_pelayanan == "Chat"){
                //buat menggunakan uuid
                $roomName = Str::uuid();
            } else {
                $response = Http::post("https://{$METERED_DOMAIN}/api/v1/room?secretKey={$METERED_SECRET_KEY}", [
                    'autoJoin' => true,
                    'maxParticipants' => 2,
                    'ejectAfterElapsedTimeInSec' => $sesi_user->order->paket->max_durasi * 60,
                ]);
                $roomName = $response->json("roomName");
            }

            $key_link               = new Key_Link();
            $key_link->id_user      = $user_id;
            $key_link->id_sesi      = $sesi->id;
            $key_link->id_dokter    = $sesi->dokter->id;
            $key_link->number_key   = $roomName;

            $key_link->save();

            return response()->json([
                'status'    => 'Berhasil',
                'key_link'  => $key_link,
                'data_sesi' => $sesi
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage(),
                'baris' => $e->getLine()
            ], 422);
        }
    }

    function check_sesi(){
        try {
            $user_id    = Auth::guard('api')->user()['id'];
            $sesi       = Key_Link::where('id_user', $user_id)->with(['dokter', 'dokter.user','sesi'])->get();

            return response()->json( $this->data_check_sesi($sesi), 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage()
            ], 422);
        }
    }

    public function data_check_sesi($data){
        $data_new = $data->transform(function ($link) {
            $start_time = Carbon::createFromFormat('H:i:s', $link->sesi->waktu_mulai);
            $end_time   = Carbon::createFromFormat('H:i:s', $link->sesi->waktu_selesai);
            $duration   = $start_time->diffInMinutes($end_time);
            $duration   = $duration.' Menit';

            $jenis_pelayanan    = $link->sesi->jenis_pelayanan;
            $url_room           = $jenis_pelayanan == "Chat" ? env('APP_URL')."chat_ui/".$link->id : env('APP_URL').'meeting/'.$link->number_key;

            $date_now   = explode(' ', now());
            $time       = $date_now[1];
            if ($link->sesi->tanggal == $date_now[0]) {
                if ($link->sesi->waktu_mulai <= $time && $link->sesi->waktu_selesai >= $time) {
                    $message = "Sesi Hari ini sedang berlangsung";
                } else {
                    $message = ($link->sesi->waktu_mulai >= $time) ? 'Sesi Hari ini Belum Dimulai' : 'Sesi Hari ini Telah Selesai';
                }
            } else {
                $message = ($link->sesi->tanggal >= $date_now[0]) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';
            }

            return [
                'status_sesi'       => $message,
                'link_sesi'         => $url_room,
                'jenis_pelayanan'   => $jenis_pelayanan,
                'tanggal'       => $link->sesi->tanggal,
                'waktu_mulai'   => $link->sesi->waktu_mulai,
                'waktu_selesai' => $link->sesi->waktu_selesai,
                'durasi'        => $duration,
                'nama_dokter'       => $link->dokter->user->name,
                "pengalaman_dokter" => $link->dokter->pengalaman,
                "jenis_dokter"      => $link->dokter->jenis_dokter
            ];
        });

        $data_sesi = [];
        foreach ($data_new as $sesi) {
            $data_sesi[] = $sesi;
        }

        return ['status'    => 'Berhasil',
                'data_sesi' => $data_sesi];
    }

    function create_sesi(Request $request){
        $validator = Validator::make($request->all(), [
            'id_kategori_layanan'   => 'required|string',
            'jenis_pelayanan'       => 'required|string|max:255',
            'tanggal'               => 'required|date_format:Y-m-d',
            'waktu_mulai'           => 'required|date_format:Y-m-d H:i:s',
            'waktu_selesai'         => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $user_id        = Auth::guard('api')->user()['id'];
            $data_dokter    = Dokter::where('id_user', $user_id)->first();
            $data_layanan   = Kategori_Layanan::find($request->id_kategori_layanan);

            if ($data_layanan->akses == 'psikolog' && $data_dokter->jenis_dokter == 'Mentor') {
                return response()->json([
                    'status'    => 'Gagal',
                    'pesan'     => 'Anda tidak memiliki akses ke layanan tersebut !!'
                ], 422);
            }

            $data_sesi = Sesi::create([
                'jenis_pelayanan'   => $request->jenis_pelayanan,
                'tanggal'           => $request->tanggal,
                'waktu_mulai'       => $request->waktu_mulai,
                'waktu_selesai'    => $request->waktu_selesai,
                'id_dokter'         => $data_dokter->id,
                'id_kategori_layanan' => $request->id_kategori_layanan,
            ]);
            $data_sesi->save();

            return response()->json([
                'status'    => 'Berhasil',
                'data_sesi' => $data_sesi,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage()
            ], 422);
        }
    }

    function update_sesi(Request $request, Sesi $data_sesi){
        $validator = Validator::make($request->all(), [
            'id_kategori_layanan'   => 'required|string',
            'jenis_pelayanan'       => 'required|string|max:255',
            'tanggal'               => 'required|date_format:Y-m-d',
            'waktu_mulai'           => 'required|date_format:Y-m-d H:i:s',
            'waktu_selesai'         => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $user_id        = Auth::guard('api')->user()['id'];
            $data_dokter    = Dokter::where('id_user', $user_id)->first();
            $data_layanan   = Kategori_Layanan::find($request->id_kategori_layanan);

            if ($data_layanan->akses == 'psikolog' && $data_dokter->jenis_dokter == 'Mentor') {
                return response()->json([
                    'status'    => 'Gagal',
                    'pesan'     => 'Anda tidak memiliki akses ke layanan tersebut !!'
                ], 422);
            }

            $data_sesi->jenis_pelayanan = $request->jenis_pelayanan;
            $data_sesi->tanggal         = $request->tanggal;
            $data_sesi->waktu_mulai     = $request->waktu_mulai;
            $data_sesi->waktu_selesai   = $request->waktu_selesai;
            $data_sesi->id_dokter       = $data_dokter->id;
            $data_sesi->id_kategori_layanan = $request->id_kategori_layanan;
            $data_sesi->update();

            return response()->json([
                'status'    => 'Berhasil',
                'data_sesi' => $data_sesi,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage()
            ], 422);
        }
    }
}
