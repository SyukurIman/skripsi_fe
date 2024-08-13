<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class dummy_token extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'token',
        'number_key'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }
}
