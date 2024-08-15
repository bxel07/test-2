<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;
    protected $fillable = [
      'excel_filename',
        'zip_filename',
        'npwp_pemotong',
        'nama_pemotong',
        'total_documents',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
