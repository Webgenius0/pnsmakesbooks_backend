<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->longText('title');
            $table->longText('url');
            $table->longText('summary')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->longText('source_name');
            $table->longText('source_id')->nullable(); // unique ID from API
            $table->longText('content')->nullable();
            $table->longText('image_url')->nullable();
            $table->longText('author')->nullable();
            $table->longText('news_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
