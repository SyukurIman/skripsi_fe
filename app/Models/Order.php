<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_paket',
        'id_user',
        'invoice',
        'harga',
        'snap',
        'status_pembayaran',
        'tanggal_kadaluarsa'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $appends = ['kadaluarsa_formatted', 'harga_formatted','status_formatted','is_kadaluarsa'];
    public function user()
    {
        return $this->belongsTo(User::class,'id_user');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class,'id_paket');
    }

    public function sesi_user()
    {
        return $this->hasMany(Sesi_User::class,'id_order', "id");
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp' . number_format($this->harga, 0, ',', '.');
    }
    public function getKadaluarsaFormattedAttribute()
    {
        return tanggal($this->tanggal_kadaluarsa);
    }
    public function getStatusFormattedAttribute()
    {
        return statusPembayaran($this->status_pembayaran);
    }
    public function getIsKadaluarsaAttribute()
    {
        return kadaluarsa($this->tanggal_kadaluarsa);
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($order) {
            if ($order->isForceDeleting()) {
                $order->sesi_user()->forceDelete();
            } else {
                $order->sesi_user()->delete();
            }
        });

        static::restoring(function ($order) {
            $order->sesi_user()->withTrashed()->restore();
        });
    }
}
