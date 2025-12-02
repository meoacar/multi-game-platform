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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Menü adı (Header Menu, Footer Menu, vb.)
            $table->string('slug')->unique(); // header-menu, footer-menu
            $table->string('location'); // header, footer, sidebar
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('title'); // Menü öğesi başlığı
            $table->string('url')->nullable(); // Harici URL
            $table->string('route')->nullable(); // Laravel route adı
            $table->string('type')->default('custom'); // custom, page, category, url
            $table->foreignId('target_id')->nullable(); // Page ID, Category ID, vb.
            $table->string('target_type')->nullable(); // App\Models\Page, vb.
            $table->string('icon')->nullable(); // Icon class (fa-home, vb.)
            $table->string('css_class')->nullable(); // Özel CSS class
            $table->string('target')->default('_self'); // _self, _blank
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
