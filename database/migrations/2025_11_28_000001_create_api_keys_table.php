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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // API key'in açıklayıcı adı
            $table->string('key', 64)->unique(); // API key (hash'lenmiş)
            $table->string('prefix', 8); // Görünür prefix (pk_live_xxx gibi)
            $table->text('permissions')->nullable(); // JSON: izin verilen endpoint'ler
            $table->integer('rate_limit')->default(60); // Dakika başına istek limiti
            $table->string('ip_whitelist')->nullable(); // İzin verilen IP'ler (virgülle ayrılmış)
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('key');
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
