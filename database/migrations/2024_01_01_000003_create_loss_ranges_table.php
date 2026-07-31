<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loss_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('isim', 255);
            $table->decimal('min_deger', 15, 2)->nullable();
            $table->decimal('max_deger', 15, 2)->nullable();
            $table->integer('sira')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loss_ranges');
    }
};
