<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Detail_Paket;
use App\Models\Kategori_Layanan;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PaketController extends Controller
{
    public $data = [
        "parent" => "paket",
    ];

    public function paket(){
        $this->data['position'] = "Data paket";
        $this->data['type'] = "index";
        $this->data['total_data'] = Paket::count();
        return view('admin.'.$this->data['parent'].'.index', $this->data);
    }

    public function table(){
        $data = Paket::with(['kategori_layanan'])->orderBy('id', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .= '<a class="btn btn-warning ml-1" href="/paket/update/'.$row->id.'" title="Ubah"><i class="fas fa-pencil-alt"></i></a> ';
                $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function create(){
        $this->data['position']     = "Form Create Paket";
        $this->data['type'] = "create";
        $this->data['kategori_layanan']= Kategori_Layanan::get();
        return view("admin.".$this->data['parent'].".index", $this->data);
    }

    function update($id){
        $this->data['position']     = "Form Edit Layanan";
        $this->data['type'] = "update";
        $this->data['data_paket'] = Paket::with(['kategori_layanan'])->where('id',$id)->first();
        $this->data['data_detail_paket'] = Detail_Paket::where('id_paket',$id)->get();
        $this->data['kategori_layanan']= Kategori_Layanan::get();
        return view("admin.".$this->data['parent'].".index", $this->data);
    }

    function createform(Request $request){
        DB::beginTransaction();
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'nama_paket'   => 'required|string',
            'max_sesi' => 'required|string',
            'kadaluarsa' => 'required|integer|between:1,12',
            'harga' => 'required',
            'harga_persesi' => 'required',
            'deskripsi_detail*' => 'requeired',
            'fitur' => 'required',
            'max_durasi' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $validator->errors(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }

        try {
            $harga = $request->harga;

            $harga = str_replace('.', '', $harga); // Menghapus titik
            $harga = str_replace('Rp ', '', $harga); // Menghapus "Rp "
            $harga_persesi = $request->harga_persesi;
            $harga_persesi = str_replace('.', '', $harga_persesi); // Menghapus titik
            $harga_persesi = str_replace('Rp ', '', $harga_persesi); // Menghapus "Rp "

            $paket = Paket::create([
                'nama_paket'   => $request->nama_paket,
                'id_kategori_layanan'=>$request->id_kategori_layanan,
                'max_sesi' => $request->max_sesi,
                'rentang_pengalaman_min' => $request->rentang_pengalaman_min,
                'rentang_pengalaman_max' => $request->rentang_pengalaman_max,
                'kadaluarsa' => $request->kadaluarsa,
                'harga_persesi' => $harga_persesi,
                'harga' => $harga,
                'fitur' => $request->fitur,
                'max_durasi' => $request->max_durasi
            ]);
            $paket->save();

            $data = $request->only(
                [
                    'deskripsi_paket',
                ]
            );

            if (isset($data['deskripsi_paket'])) {

                foreach ($data['deskripsi_paket'] as $key => $value) {
                    $detail_paket = new Detail_Paket();
                    $detail_paket->id_paket=$paket->id;
                    $detail_paket->deskripsi_paket=$data['deskripsi_paket'][$key];
                    $detail_paket->save();
                }
            }

            DB::commit();
            return response()->json(['title' => 'Success!', 'icon' => 'success', 'text' => 'Data Berhasil ditambahkan!', 'ButtonColor' => '#66BB6A', 'type' => 'success']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $e->getMessage(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }
    }

    function updateform(Request $request){
        // dd($request->all());
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'nama_paket'   => 'required|string',
            'max_sesi' => 'required|string',
            'kadaluarsa' => 'required|integer|between:1,12',
            'harga_persesi' => 'required',
            'harga' => 'required',
            'deskripsi_detail*' => 'requeired',
        ]);

        if ($validator->fails()) {
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $validator->errors(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }

        try {
            $harga = $request->harga;
            $harga = str_replace('.', '', $harga); // Menghapus titik
            $harga = str_replace('Rp ', '', $harga); // Menghapus "Rp "
            $harga_persesi = $request->harga_persesi;
            $harga_persesi = str_replace('.', '', $harga_persesi); // Menghapus titik
            $harga_persesi = str_replace('Rp ', '', $harga_persesi); // Menghapus "Rp "

            $paket = Paket::find($request->id);
            $paket->nama_paket = $request->nama_paket;
            $paket->id_kategori_layanan = $request->id_kategori_layanan;
            $paket->max_sesi = $request->max_sesi;
            $paket->kadaluarsa = $request->kadaluarsa;
            $paket->rentang_pengalaman_min = $request->rentang_pengalaman_min;
            $paket->rentang_pengalaman_max = $request->rentang_pengalaman_max;
            $paket->harga_persesi = $harga_persesi;
            $paket->harga = $harga;
            $paket->fitur = $request->fitur;
            $paket->max_durasi = $request->max_durasi;
            $paket->save();

            $data = $request->only(
                [
                    'deskripsi_paket',
                    'id_detail_paket'
                ]
            );

            if (isset($data['deskripsi_paket'])) {
                $exitingDetailPaket = Detail_Paket::where('id_paket', $request->id)->get();

                $existingDetailPaketIds = $exitingDetailPaket->pluck('id')->toArray();

                if ($exitingDetailPaket->isNotEmpty()) {
                    Detail_Paket::where('id_paket', $request->id)->delete();
                }

                foreach ($data['deskripsi_paket'] as $key => $value) {
                    $detail_paket = new Detail_Paket();

                    if (isset($data['id_detail_paket'][$key]) && in_array($data['id_detail_paket'][$key], $existingDetailPaketIds)) {
                        $detail_paket->id = $data['id_detail_paket'][$key];
                    }
                    $detail_paket->id_paket=$paket->id;
                    $detail_paket->deskripsi_paket=$data['deskripsi_paket'][$key];
                    $detail_paket->save();
                }
            }

            DB::commit();
            return response()->json(['title' => 'Success!', 'icon' => 'success', 'text' => 'Data Berhasil Diubah!', 'ButtonColor' => '#66BB6A', 'type' => 'success']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $e->getMessage(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }
    }

    function deleteform(Request $request){
        DB::beginTransaction();
        try{
            $paket = Paket::where('id', $request->id)->first();

            $paket->delete();
            DB::commit();
            return response()->json(['title'=>'Success!','icon'=>'success','text'=>'Data Berhasil Dihapus!', 'ButtonColor'=>'#66BB6A', 'type'=>'success']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['title'=>'Error','icon'=>'error','text'=>$e->getMessage(), 'ButtonColor'=>'#EF5350', 'type'=>'error']);
        }
    }
}
