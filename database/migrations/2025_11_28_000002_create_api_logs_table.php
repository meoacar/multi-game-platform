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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->nullable()->constrained()->onDelete('set null');
            $table->string('method', 10); // GET, POST, PUT, DELETE
            $table->string('endpoint', 500);
            $table->integer('status_code');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->text('request_body')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time')->nullable(); // milisaniye
            $table->timestamp('created_at');
            
            $table->index('api_key_id');
            $table->index('created_at');
            $table->index('status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
