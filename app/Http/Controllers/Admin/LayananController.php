<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Kategori_Layanan;
use App\Models\Key_Link;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LayananController extends Controller
{
    public $data = [
        "parent" => "Layanan",
    ];

    public function index(){
        $this->data['position'] = "Data Layanan";
        $this->data['total_data'] = Kategori_Layanan::count();
        return view('admin.layanan.index', $this->data);
    }

    public function get_all_data(){
        $data = Kategori_Layanan::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('link_sesi', function($row){
                return count($row->sesi).' Sesi';
            })
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .= '<a class="btn btn-warning ml-1" href="'.env('APP_URL').'admin/layanan/edit/'.$row->id.'"><i class="fas fa-pencil-alt"></i></a> ';
                $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                $btn .= '<a class="btn btn-success ml-1" href="'.env('APP_URL').'admin/sesi/'.$row->id.'"><i class="fas fa-book"></i></a> ';
                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('total_dokter', function($layanan){
                $data_dokter = Dokter::where('jenis_dokter', $layanan->akses)->count();
                return $data_dokter.' Dokter';
            })
            ->addColumn('total_pemakai', function($layanan){
                $data_sesi = Key_Link::whereHas('sesi', function($query) use ($layanan) {
                    $query->where('id_kategori_layanan', $layanan->id);
                })->count();
                return $data_sesi.' User';
            })
            ->addColumn('akses', function($layanan){
                return ucfirst($layanan->akses);
            })
            ->rawColumns(['action'])->make(true);
    }

    function from_create(){
        $this->data['position']     = "Form Create Layanan";
        return view("admin.layanan.index", $this->data);
    }

    function from_update($id){
        $this->data['position']     = "Form Edit Layanan";
        $this->data['data_layanan'] = Kategori_Layanan::find($id);

        return view("admin.layanan.index", $this->data);
    }

    function save_create(Request $request){
        $validator = Validator::make($request->all(), [
            'nama'   => 'required|string',
            'akses' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors', $validator->errors());
        }

        try {
            $data_sesi = Kategori_Layanan::create([
                'nama'   => $request->nama,
                'akses' => $request->akses
            ]);
            $data_sesi->save();

            return redirect()->back()->with('status', 'Sukses menyimpan data layanan');

        } catch (\Throwable $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    function save_update(Request $request, Kategori_Layanan $data){
        $validator = Validator::make($request->all(), [
            'nama'   => 'required|string',
            'akses' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors', $validator->errors());
        }

        try {
            $data->nama = $request->nama;
            $data->akses = $request->akses;
            $data->save();

            return redirect()->back()->with('status', 'Sukses menyimpan data layanan');
        } catch (\Throwable $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    function delete_user(Request $request){
        DB::beginTransaction();
        try{
            $data_layanan = Kategori_Layanan::where('id', $request->id)->first();
            if ($data_layanan) {
                Kategori_Layanan::where('id', $request->id)->delete();
            }

            DB::commit();
            return response()->json(['title'=>'Success!','icon'=>'success','text'=>'Data Berhasil Dihapus!', 'ButtonColor'=>'#66BB6A', 'type'=>'success']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['title'=>'Error','icon'=>'error','text'=>$e->getMessage(), 'ButtonColor'=>'#EF5350', 'type'=>'error']);
        }
    }

    function index_sesi($id_layanan){
        $this->data['position'] = "Data Sesi";
        $this->data['id_layanan'] = $id_layanan;
        $this->data['total_data'] = Sesi::where('id_kategori_layanan', $id_layanan)->with(['dokter'])->count();
        return view('admin.sesi.index', $this->data);
    }

    // Sesi
    function get_all_sesi($id_layanan){
        $data_sesi = Sesi::where('id_kategori_layanan', $id_layanan)->with(['dokter'])->get();
        return DataTables::of($data_sesi)
            ->addIndexColumn()
            ->addColumn('durasi', function($sesi){
                $start_time = Carbon::createFromFormat('H:i:s', $sesi->waktu_mulai);
                $end_time = Carbon::createFromFormat('H:i:s', $sesi->waktu_selesai);
                $duration   = $start_time->diffInMinutes($end_time);
                $durasi = $duration.' Menit';
                return $durasi;
            })
            ->addColumn('status_sesi', function($sesi){
                $date_now   = explode(' ', now());
                $time       = $date_now[1];
                if ($sesi->tanggal == $date_now[0]) {
                    if ($sesi->waktu_mulai <= $time && $sesi->waktu_selesai >= $time) {
                        $message = "Sesi Hari ini sedang berlangsung";
                    } else {
                        $message = ($sesi->waktu_mulai >= $time) ? 'Sesi Hari ini Belum Dimulai' : 'Sesi Hari ini Telah Selesai';
                    }
                } else {
                    $message = ($sesi->tanggal >= $date_now[0]) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';
                }

                $data_sesi = Key_Link::whereHas('sesi', function($query) use ($sesi) {
                    $query->where('id', $sesi->id);
                })->first();

                if (isset($data_sesi)) {
                    $message = $message.' | Di Pesan Oleh '. $data_sesi->user->name;
                } else {
                    $message = $message.' | Belum di Pesan';
                }
                return $message;
            })
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;
            })->rawColumns(['link_sesi', 'action'])->make(true);
    }
}
