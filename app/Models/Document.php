<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
   
    protected $fillable = [
       'no_bukti',
       'tanggal_bukti',
       'npwp_pemotong',
       'nama_pemotong',

       'identitas_penerima',
       'nama_penerima',
       'penghasilan_bruto',
       'pph',

       'kode_objek_pajak',
       'pasal',
       'masa_pajak',
       'periode',
       'tahun_pajak',

       'status',
       'rev_no',
       'posting',
       'id_sistem',

       'user_id'


    ];

      // Define the relationship with User model
      public function user()
      {
          return $this->belongsTo(User::class);
      }
}
// 'id_dipotong',
// 'nama',
// 'pasal',
// 'kode_objek_pajak',
// 'no_bukti_potong',
// 'tanggal_bupot',
// 'pph_dipotong',
// 'jumlah_bruto',
// 'keterangan',
// 'user_id',