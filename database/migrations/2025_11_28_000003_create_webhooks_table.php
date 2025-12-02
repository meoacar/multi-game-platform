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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // Webhook adı
            $table->string('url', 500); // Webhook URL'i
            $table->string('secret', 64)->nullable(); // İmzalama için secret
            $table->json('events'); // Dinlenecek olaylar: ['user.created', 'post.created', ...]
            $table->json('headers')->nullable(); // Özel HTTP header'ları
            $table->integer('timeout')->default(30); // Saniye
            $table->integer('retry_count')->default(3); // Başarısız olursa kaç kez tekrar denenecek
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
