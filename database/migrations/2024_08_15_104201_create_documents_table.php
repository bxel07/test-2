<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); 
            $table->string('no_bukti'); 
            $table->date('tanggal_bukti'); 
            $table->bigInteger('npwp_pemotong'); 
            $table->string('nama_pemotong'); 

            $table->string('identitas_penerima'); 
            $table->string('nama_penerima'); 
            $table->bigInteger('penghasilan_bruto'); 
            $table->bigInteger('pph'); 

            $table->string('kode_objek_pajak');
            $table->string('pasal'); 
            $table->integer('masa_pajak');
            $table->string('periode');
            $table->string('tahun_pajak');

            $table->string('status');
            $table->integer('rev_no');
            $table->string('posting');
            $table->string('id_sistem');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

// $table->string('id_dipotong'); 
// $table->string('nama'); 
// $table->string('pasal'); 
// $table->string('kode_objek_pajak'); 
// $table->string('no_bukti_potong'); 
// $table->date('tanggal_bupot'); 
// $table->decimal('pph_dipotong', 10, 2); 
// $table->decimal('jumlah_bruto', 15, 2); 
// $table->text('keterangan'); 

// $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
// $table->timestamps(); 