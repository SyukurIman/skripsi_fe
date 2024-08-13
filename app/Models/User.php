<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\UploadImage;
use App\Models\Traits\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, UploadImage, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status_role',
        'no_telpon',
        'image_profile'
    ];

    protected $hidden = [
        'status_role',
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'id' => 'string'
    ];

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }

    public function receiveMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id')->orderByDesc('id');
    }

    public function sendMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id')->orderByDesc('id');
    }

    public function key_link()
    {
        return $this->hasMany(Key_Link::class,'id_user', "id");
    }

    public function sesi_user()
    {
        return $this->hasMany(Sesi_User::class,'id_user', "id");
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class,'id_user', "id");
    }

    public function order()
    {
        return $this->hasMany(Order::class,'id_user', "id");
    }

    public function dokter()
    {
        return $this->hasMany(Dokter::class,'id_user', "id");
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();

        static::deleting(function ($user) {
            $isDokter = $user->status_role == 1;
            if ($user->isForceDeleting()) {
                $user->key_link()->forceDelete();
                $user->notifikasi()->forceDelete();
                $user->order()->forceDelete();
                $user->sesi_user()->forceDelete();

                if ($isDokter) {
                    $user->dokter()->forceDelete();
                }
            } else {
                $user->key_link()->delete();
                $user->notifikasi()->delete();
                $user->order()->delete();
                $user->sesi_user()->delete();
                if ($isDokter) {
                    $user->dokter()->delete();
                }
            }
        });

        static::restoring(function ($user) {
            $isDokter = $user->status_role == 1;
            $user->key_link()->withTrashed()->restore();
            $user->Notifikasi()->withTrashed()->restore();
            $user->order()->withTrashed()->restore();
            $user->sesi_user()->withTrashed()->restore();
            if ($isDokter) {
                $user->dokter()->withTrashed()->restore();;
            }
        });
    }
}
