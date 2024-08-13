<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Kategori_Layanan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public $data = [
        "parent" => "order",
    ];

    public function order(){
        $this->data['position'] = "Data order";
        $this->data['type'] = "index";
        $this->data['total_data'] = Order::count();
        return view('admin.'.$this->data['parent'].'.index', $this->data);
    }
    public function invoice($invoice){
        $this->data['position'] = "Data order";
        $this->data['type'] = "update";
        $this->data['kategori_layanan'] = Kategori_Layanan::all();
        $this->data['data_order'] = Order::where('invoice', $invoice)->with(['user','paket', 'paket.detail_paket'])->first();
        return view('admin.'.$this->data['parent'].'.index', $this->data);
    }

    public function update_invoice(Request $request, $invoice){
        $validator = Validator::make($request->all(), [
            'status_pembayaran'     => 'required|string',
            'tanggal_kadaluarsa'    => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors', $validator->errors());
        }

        try {
            $data_order = Order::where('invoice', $invoice)->with(['user','paket', 'paket.detail_paket'])->first();

            $data_order->status_pembayaran  = $request->status_pembayaran;
            $data_order->tanggal_kadaluarsa = $request->tanggal_kadaluarsa;
            $data_order->save();

            return redirect()->back()->with('status', 'Sukses menyimpan data order');
        } catch (\Throwable $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    public function table(){
        $data = Order::with(['user','paket'])->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .= '<a class="btn btn-warning ml-1" href="/order/invoice/'.$row->invoice.'" title="Ubah"><i class="fas fa-pencil-alt"></i></a> ';
                $btn .= '<button class="btn btn-danger btn-raised btn-xs" id="btn-hapus" title="Hapus"><i class="icon-trash"></i></button>';
                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('status_format', function($row){
                $btn = '';
                $btn .= '<div class="text-center">';
                $btn .= '<div class="btn-group btn-group-solid mx-5">';
                $btn .=  status($row->status_pembayaran);
                $btn .= '</div>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action','status_format'])->make(true);
    }


    function deleteform(Request $request){
        DB::beginTransaction();
        try{
            $order = Order::where('id', $request->id)->first();

            $order->delete();
            DB::commit();
            return response()->json(['title'=>'Success!','icon'=>'success','text'=>'Data Berhasil Dihapus!', 'ButtonColor'=>'#66BB6A', 'type'=>'success']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['title'=>'Error','icon'=>'error','text'=>$e->getMessage(), 'ButtonColor'=>'#EF5350', 'type'=>'error']);
        }
    }
}
