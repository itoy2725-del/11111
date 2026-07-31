<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('meta_lead_id', 255)->nullable()->index();
            $table->timestamp('created_time')->nullable();
            $table->string('ad_id', 255)->nullable();
            $table->string('ad_name', 255)->nullable();
            $table->string('adset_id', 255)->nullable();
            $table->string('adset_name', 255)->nullable();
            $table->string('campaign_id', 255)->nullable();
            $table->string('campaign_name', 255)->nullable();
            $table->string('form_id', 255)->nullable();
            $table->string('form_name', 255)->nullable();
            $table->boolean('is_organic')->default(false);
            $table->string('platform', 100)->nullable();
            $table->string('ad_soyad', 255)->nullable();
            $table->string('telefon', 50)->index();
            $table->string('email', 255)->index();
            $table->foreignId('fraud_type_id')->nullable()->constrained('fraud_types')->nullOnDelete();
            $table->foreignId('loss_range_id')->nullable()->constrained('loss_ranges')->nullOnDelete();
            $table->foreignId('wallet_type_id')->nullable()->constrained('wallet_types')->nullOnDelete();
            $table->string('sikayet_durumu', 255)->nullable();
            $table->string('ek_guvenlik_hizmeti', 255)->nullable();
            $table->string('toplam_kripto', 255)->nullable();
            $table->foreignId('status_id')->default(1)->constrained('lead_statuses');
            $table->foreignId('atanan_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('sonraki_arama_tarihi')->nullable();
            $table->text('operator_notu')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status_id');
            $table->index('atanan_operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
