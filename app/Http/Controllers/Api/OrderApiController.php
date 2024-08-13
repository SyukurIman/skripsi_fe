<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Paket;
use App\Services\Midtrans\CreateSnapTokenService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class OrderApiController extends Controller
{
    public function tes_midtrans($id){
        $order = Order::find($id);
        $snapToken = $order->snap;
            if(is_null($snapToken)){
                $midtrans = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapToken();

                $order->snap = $snapToken;
                $order->save();
            }
        return view('admin.midtrans.show', compact('order', 'snapToken'));
    }
    public function get_order_user(){
        $userAuth = auth()->guard('api')->user();
        $order = Order::with(['paket','user','paket.kategori_layanan'])
        ->where('id_user',$userAuth->id)
        ->select('id','id_paket','id_user','harga','status_pembayaran', 'invoice','tanggal_kadaluarsa','snap','created_at')
        ->orderBy('created_at', 'desc')
        ->get();
        return response()->json([
            'success' => true,
            'order' => $order
        ], 200);
    }

    public function get_order_detail($id){
        $order = Order::with(['paket','user','paket.kategori_layanan'])
                        ->where('id',$id)
                        ->select('id','id_paket','id_user','harga','status_pembayaran', 'invoice','tanggal_kadaluarsa','snap','created_at')
                        ->orderBy('created_at', 'desc')
                        ->first();
        return response()->json([
            'success' => true,
            'order' => $order
        ], 200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'id_paket' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['title' => 'Error', 'icon' => 'error', 'text' => 'Validasi gagal. ' . $validator->errors()->first(), 'ButtonColor' => '#EF5350', 'type' => 'error'], 400);
        }

        $number = Order::count() + 1; // Menghitung jumlah order dan menambah 1 untuk nomor urut berikutnya
        $paket = Paket::with(['kategori_layanan'])->where('id', $request->id_paket)->first();
        $tanggal_sekarang = Carbon::now();
        $tanggal_sekarang->addMonths($paket->kadaluarsa);

        // dd($tanggal_kadaluarsa);
        $huruf_awal = strtoupper(substr($paket->kategori_layanan->nama, 0, 1));
        $bulan = date('m');
        $tahun = date('Y');
        $userAuth = auth()->guard('api')->user();
        // $userAuth = $request->user;
        $invoice = $huruf_awal.'-'. $number . $bulan . $tahun;
        $harga = $paket->harga;

        // dd($invoice);
        DB::beginTransaction();
        try{
            $order = Order::create([
                    'id_paket' => $request->id_paket,
                    'id_user' => $userAuth->id,
                    'invoice' => $invoice,
                    'harga' => $harga,
                    'status_pembayaran' => '0',
                    'tanggal_kadaluarsa' => $tanggal_sekarang,
            ]);

            if ($order){
                $midtrans = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapToken();
                $order->snap = $snapToken;
                $order->save();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'data_order' => $order
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'text' => $e->getMessage(),
            ], 500);
        }
    }
}


