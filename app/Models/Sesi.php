<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sesi extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_dokter',
        'id_kategori_layanan',
        'jenis_pelayanan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai'
    ];

    protected $hidden = [
        'id_dokter',
        'id_kategori_layanan',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function kategori_layanan(): BelongsTo
    {
        return $this->belongsTo(Kategori_Layanan::class, 'id_kategori_layanan', 'id');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'id_dokter', 'id');
    }

    public function key_link()
    {
        return $this->hasMany(Key_Link::class,'id_sesi', "id");
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($sesi) {
            // if (!$sesi->isForceDeleting()) {
            //     foreach ($sesi->key_link as $key_link) {
            //         $key_link->delete();
            //     }
            // }

            if ($sesi->isForceDeleting()) {
                $sesi->key_link()->forceDelete();
            } else {
                $sesi->key_link()->delete();
            }
        });

        static::restoring(function ($sesi) {
            $sesi->key_link()->withTrashed()->restore();
        });
    }
}
