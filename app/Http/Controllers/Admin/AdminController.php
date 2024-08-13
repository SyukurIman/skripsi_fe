<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Order;
use App\Models\Paket;
use App\Models\Sesi;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public $data = [
        "parent" => "Dashboard",
    ];
    public function home_admin(){
        $this->data['position'] = "Home";

        $this->data['total_user'] = User::where('status_role', 1)->count();
        $this->data['total_artikel'] = Artikel::count();
        $this->data['total_order'] = Order::where('status_pembayaran', 1)->count();
        $this->data['total_dokter'] = User::where('status_role', 2)->count();;

        $this->data['data_artikel'] = Artikel::orderBy('created_at', 'asc')->first();
        $this->data['data_paket']   = Paket::orderBy('created_at', 'asc')->with('detail_paket')->first();

        $this->data['data_order'] = Order::orderBy('created_at', 'asc');
        $this->data['data_sesi'] = Sesi::orderBy('created_at', 'asc');

        return view("admin.dashboard.index", $this->data);
    }
}
