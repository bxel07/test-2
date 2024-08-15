<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_files', function (Blueprint $table) {
            $table->id();
            $table->string('excel_filename')->nullable();
            $table->string('zip_filename')->nullable();
            $table->string('npwp_pemotong', 15);
            $table->string('nama_pemotong');
            $table->integer('total_documents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_files');
    }
};