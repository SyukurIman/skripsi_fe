<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detail_Paket extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_paket',
        'deskripsi_paket'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class,'id_paket');
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }

}
