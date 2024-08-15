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
            $table->id(); // Primary key for the document
            $table->string('id_dipotong'); // Corresponds to ID_DIPOTONG
            $table->string('nama'); // Corresponds to NAMA
            $table->string('pasal'); // Corresponds to PASAL
            $table->string('kode_objek_pajak'); // Corresponds to KODE_OBJEK_PAJAK
            $table->string('no_bukti_potong'); // Corresponds to NO_BUKTI_POTONG
            $table->date('tanggal_bupot'); // Corresponds to TANGGAL_BUPOT
            $table->decimal('pph_dipotong', 10, 2); // Corresponds to PPH_DIPOTONG
            $table->decimal('jumlah_bruto', 15, 2); // Corresponds to JUMLAH_BRUTO
            $table->text('keterangan'); // Corresponds to KETERANGAN
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->timestamps(); // Created at and updated at columns
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
