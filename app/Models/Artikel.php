<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'judul',
        'nama_penulis',
        'deskripsi',
        'link_gambar'
    ];

    protected $appends = ['tanggal_artikel','gambar'];

    protected $casts = [
        'id' => 'string'
    ];

    public function getTanggalArtikelAttribute()
    {
        return tanggal($this->created_at);
    }

    public function getGambarAttribute(){
        return  url('storage/image/artikel/' . $this->link_gambar);
    }

    public static function boot()
    {
        parent::boot();
        static::bootUuid();
    }
}
