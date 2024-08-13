<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori_Layanan extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nama',
        'akses'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $hidden = [
        'akses',
        'created_at',
        'updated_at'
    ];

    public function sesi(): HasMany
    {
        return $this->hasMany(Sesi::class, 'id_kategori_layanan', 'id');
    }

    public function paket(): HasMany
    {
        return $this->hasMany(Paket::class, 'id_kategori_layanan', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($kat) {
            if ($kat->isForceDeleting()) {
                $kat->sesi()->forceDelete();
            } else {
                $kat->sesi()->delete();
            }
        });

        static::restoring(function ($kat) {
            $kat->sesi()->withTrashed()->restore();
        });
    }
}
