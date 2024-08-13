<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paket extends Model
{
    use HasFactory, Uuid, SoftDeletes;


    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_kategori_layanan',
        'nama_paket',
        'harga',
        'harga_persesi',
        'max_sesi',
        'kadaluarsa',
        'rentang_pengalaman_min',
        'rentang_pengalaman_max',
        'fitur',
        'max_durasi'

    ];

    protected $appends = ['kadaluarsa_formatted', 'harga_formatted', 'detail_paket_count'];

    protected $casts = [
        'id' => 'string'
    ];

    public function detail_paket()
    {
        return $this->hasMany(Detail_Paket::class,'id_paket','id');
    }

    public function kategori_layanan()
    {
        return $this->belongsTo(Kategori_Layanan::class,'id_kategori_layanan');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'id_paket', "id");
    }

    public function getKadaluarsaFormattedAttribute()
    {
        return $this->kadaluarsa.' bulan';
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp' . number_format($this->harga, 0, ',', '.');
    }

    public function getDetailPaketCountAttribute()
    {
        return $this->detail_paket()->count();
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($paket) {
            if ($paket->isForceDeleting()) {
                $paket->detail_paket()->forceDelete();
                $paket->order()->forceDelete();
            } else {
                $paket->detail_paket()->delete();
                $paket->order()->delete();
            }
        });

        static::restoring(function ($paket) {
            $paket->detail_paket()->withTrashed()->restore();
            $paket->order()->withTrashed()->restore();
        });
    }
}
