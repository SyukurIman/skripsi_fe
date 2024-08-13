<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_user',
        'spesalis',
        'pengalaman',
        'jenis_dokter'
    ];

    protected $hidden = [
        'id_user',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'id_dokter', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($dokter) {
            foreach ($dokter->sesi as $sesi) {
                $sesi->delete();
            }

            if ($dokter->isForceDeleting()) {
                $dokter->sesi()->forceDelete();
            } else {
                $dokter->sesi()->delete();
            }
        });

        static::restoring(function ($dokter) {
            foreach ($dokter->sesi->withTrashed() as $sesi) {
                $sesi->restore();
            }
            $dokter->sesi()->withTrashed()->restore();
        });
    }
}
