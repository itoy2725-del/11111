<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('dosya_adi', 255);
            $table->integer('toplam_kayit')->default(0);
            $table->integer('basarili')->default(0);
            $table->integer('mukerrer')->default(0);
            $table->integer('hata_sayisi')->default(0);
            $table->json('import_detay_json')->nullable();
            $table->foreignId('yukleyen')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
