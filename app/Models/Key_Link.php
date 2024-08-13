<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Key_Link extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_sesi',
        'id_user',
        'id_dokter',
        'number_key'
    ];

    protected $hidden = [
        'id_user',
        'id_sesi',
        'id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class,'id_sesi');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class,'id_dokter');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'id_user');
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }
}
