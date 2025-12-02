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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->onDelete('cascade');
            $table->string('event'); // Tetiklenen olay
            $table->text('payload'); // Gönderilen veri
            $table->integer('status_code')->nullable();
            $table->text('response')->nullable();
            $table->integer('response_time')->nullable(); // milisaniye
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->integer('attempt')->default(1); // Kaçıncı deneme
            $table->timestamp('created_at');
            
            $table->index('webhook_id');
            $table->index('created_at');
            $table->index('success');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
