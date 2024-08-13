<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, Uuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "sender_id", "receiver_id","number_key", "content"
    ];

    protected $appends = ['waktu'];

    protected $casts = [
        'id' => 'string'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }

    public function getWaktuAttribute()
    {
        return waktu($this->created_at);
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }

}
