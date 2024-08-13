<?php

namespace App\Services\Midtrans;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Paket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function getSnapToken()
    {
        $user = auth()->guard('api')->user();

        $paket = Paket::where('id',$this->order->id_paket)->first();
        $amount = $this->order->harga;
        $amount = (int)$amount;
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->invoice,
                'gross_amount' => $amount,
            ],
            'item' => [
                'id' => $paket->id,
                'name' => $paket->nama_paket,
                'price' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }
}
