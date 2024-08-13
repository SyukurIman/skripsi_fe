<?php

use App\Models\Notifikasi;

function rupiah($number)
{
    return "Rp " . number_format($number,0,',','.');
}

function tanggal($tanggal){
    // Set locale ke bahasa Indonesia
    \Carbon\Carbon::setLocale('id');
    // Format tanggal
    return \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
}

function status($status){
    if($status == '0'){
        return '<div class="badge rounded-pill bg-secondary">Menunggu Pembayaran</div>';
    }else if($status == '1'){
        return '<div class="badge rounded-pill bg-success">Sudah Terbayar</div>';
    }else if($status == '2'){
        return '<div class="badge rounded-pill bg-danger">Dibatalkan</div>';
    }else{
        return '<div class="badge rounded-pill bg-danger">Kadaluarsa</div>';
    }
}

function waktu($waktu){
     // Set locale ke bahasa Indonesia
     \Carbon\Carbon::setLocale('id');
     // Format tanggal
     return \Carbon\Carbon::parse($waktu)->translatedFormat('H:i');
}

function send_notif($target, $pesan){
    $notif = new Notifikasi();
    $notif->id_user = $target;
    $notif->pesan   = $pesan;
    $notif->status  = 'belum_dibaca';
    $notif->save();
}

function statusPembayaran($status){
    if($status == '0'){
        return 'Menunggu Pembayaran';
    }else if($status == '1'){
        return 'Sudah Terbayar';
    }else if($status == '2'){
        return 'Dibatalkan';
    }else{
        return 'Kadaluarsa';
    }
}

function kadaluarsa($tanggal){
        $kadaluarsa = $tanggal;
        $sekarang = new DateTime();
        $kadaluarsa_date = new DateTime($kadaluarsa);
        if ($sekarang == $kadaluarsa_date) {
            return  true;
        } else {
            return  false;
        }
}

