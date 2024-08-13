<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MidtransApiController extends Controller
{
    public function callback(Request $request)
    {
        // dd($request->all());
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
            // dd($hashed == $request->signature_key);
            if ($hashed == $request->signature_key) {
                $order = Order::where('invoice', $request->order_id)->first();
                if ($order) {
                    if ($request->transaction_status == 'capture') {
                        if ($request->payment_type == 'credit_card') {
                            if ($request->fraud_status == 'challenge') {
                                $order->update(['status_pembayaran' => '0']);
                            } else {
                                $order->update(['status_pembayaran' => '1']);
                                send_notif($order->id_user, 'Pembayaran anda telah berhasil !!');
                            }
                        }
                    }
                    else if($request->transaction_status == 'settlement') {
                        $order->update(['status_pembayaran' => '1']);
                        send_notif($order->id_user, 'Pembayaran anda telah berhasil !!');

                    } else if ($request->transaction_status == 'pending') {
                        $order->update(['status_pembayaran' => '0']);
                    } else if ($request->transaction_status == 'deny') {
                        $order->update(['status_pembayaran' => '4']);
                    } else if ($request->transaction_status == 'expire') {
                        $order->update(['status_pembayaran' => '3']);
                    } else if ($request->transaction_status == 'cancel') {
                        $order->update(['status_pembayaran' => '2']);
                    }

                    return response()
                        ->json([
                            'success' => true,
                            'message' => $order->invoice,
                        ]);
                } else {
                    return response()
                        ->json([
                            'success' => false,
                            'message' => 'Order not found',
                        ], 404);
                }
            } else {
                return response()
                    ->json([
                        'success' => false,
                        'message' => 'Invalid signature key',
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()
                ->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
        }
    }
}
