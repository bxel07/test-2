<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_dipotong',
        'nama',
        'pasal',
        'kode_objek_pajak',
        'no_bukti_potong',
        'tanggal_bupot',
        'pph_dipotong',
        'jumlah_bruto',
        'keterangan',
        'user_id',
    ];

      // Define the relationship with User model
      public function user()
      {
          return $this->belongsTo(User::class);
      }
}
