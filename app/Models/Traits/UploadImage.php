<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

/**
 * 
 * Trait ini digunakan untuk menghandle proses upload gambar. Gambar yang diupload otomatis akan 
 * diresize menjadi 3 ukuran: lg, md, sm.
 * 
 * Dan gambar juga akan otomatis dikonversi menjadi 
 * next gen format webp (safari masih partial support saat trait ini ditulis: https://caniuse.com/webp)
 * 
 * Kolom di db harus bernama "image" agar bisa memanfaatkan $appends
 */

trait UploadImage
{

  /**
   * Upload gambar.
   * 
   * @param object $file file dari request file
   * @param String $directory lokasi direktori tujuan diupload
   * @param String $fileName nama file tanpa ekstensi
   */
  public function uploadImage($file, $directory = 'public/ugc', $fileName = null)
  {
    # hapus ekstensi file dari $fileName (kalau ada)
    $fileName = Str::slug(uniqid() . '-' . pathinfo($fileName ?? $file->getClientOriginalName())['filename']);

    if (!Storage::exists($directory)) {
      Storage::makeDirectory($directory);
    }

    $extension = $file->getClientOriginalExtension();
    $fileNameWithExtension = "{$fileName}.{$extension}";
    $path = $file->storeAs($directory, $fileNameWithExtension);
    
    return (object) [
      'path' => $path,
      // 'url' => Storage::url($path)
    ];
  }

  /**
   * Delete 3 versi image (lg, md, sm)
   */
  public function deleteImage($filePath = null)
    {
        try {
            Storage::delete($filePath);
            return true;
        } catch (\Throwable $th) {
            // Log::error($th);
            return false;
        }
    }

    public function getFileAttribute($filePath)
    {
        return (object) [
            'path' => $filePath,
            'url' => $filePath ? asset(Storage::url($filePath)) : asset('static/admin/img/default.png')
        ];
    }
}
