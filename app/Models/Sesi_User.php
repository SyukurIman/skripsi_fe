<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sesi_User extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_user',
        'id_order',
        'sesi_terpakai',
        'batas_waktu'
    ];

    protected $hidden = [
        'id',
        'id_user',
        'id_order',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }

}
