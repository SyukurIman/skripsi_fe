<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public $data = [
        "parent" => "artikel",
    ];

    public function artikel(){
        $this->data['position'] = "Data Artikel";
        $this->data['type'] = "index";
        $this->data['total_data'] = Artikel::count();
        return view('admin.'.$this->data['parent'].'.index', $this->data);
    }

    public function table(){
        $data = Artikel::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .= '<a class="btn btn-warning ml-1" href="/artikel/update/'.$row->id.'" title="Ubah"><i class="fas fa-pencil-alt"></i></a> ';
                $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                $btn .= '</div>';    
                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function create(){
        $this->data['position']     = "Form Create Layanan";
        $this->data['type'] = "create";
        return view("admin.".$this->data['parent'].".index", $this->data);
    }

    function update($id){
        $this->data['position']     = "Form Edit Layanan";
        $this->data['type'] = "update";
        $this->data['data_artikel'] = Artikel::find($id);
        return view("admin.".$this->data['parent'].".index", $this->data);
    }

    function createform(Request $request){
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'judul'   => 'required|string',
            'deskripsi' => 'required|string',
            'link_gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $validator->errors(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }

        try {
            $gambar = time() . '.' .$request->link_gambar->extension();
            $request->link_gambar->storeAs('public/image/artikel', $gambar);
            $artikel = Artikel::create([
                'judul'   => $request->judul,
                'nama_penulis' => $request->nama_penulis,
                'deskripsi' => $request->deskripsi,
                //menaruh file ke storage
                'link_gambar' => $gambar,
            ]);
            $artikel->save();

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
            'judul'   => 'required|string',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => $validator->errors(), 'ButtonColor' => '#EF5350', 'type' => 'error']);
        }

        try {
            
            $artikel = Artikel::find($request->id);
            $artikel->judul = $request->judul;
            $artikel->nama_penulis = $request->nama_penulis;
            $artikel->deskripsi = $request->deskripsi;
            if ($request->hasFile('link_gambar')) {
                //delete gambar lama 
                if ($artikel->link_gambar) {
                    Storage::delete('public/image/artikel/' . $artikel->link_gambar);
                }
                $gambar = time() . '.' .$request->link_gambar->extension();
                $request->link_gambar->storeAs('public/image/artikel', $gambar);
                $artikel->link_gambar = $gambar;
            }
            $artikel->save();

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
            $artikel = Artikel::where('id', $request->id)->first();
            if ($artikel) {
                
                Storage::delete('public/image/artikel/' . $artikel->link_gambar);

                Artikel::where('id', $request->id)->delete();
            }
            DB::commit();
            return response()->json(['title'=>'Success!','icon'=>'success','text'=>'Data Berhasil Dihapus!', 'ButtonColor'=>'#66BB6A', 'type'=>'success']); 
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['title'=>'Error','icon'=>'error','text'=>$e->getMessage(), 'ButtonColor'=>'#EF5350', 'type'=>'error']); 
        } 
    }
}
