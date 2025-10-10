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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('article_remote_key')->index();
            $table->string('title')->index();
            $table->text('content')->nullable();
            $table->string('author')->nullable();
            $table->string('url')->unique();
            $table->string('source')->index()->nullable();
            $table->string('category')->index()->nullable();
            $table->text('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('published_at')->index()->nullable();
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
