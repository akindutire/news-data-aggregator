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
        Schema::create('new_source_orchestration_states', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->timestamp('last_fetched_at');
            $table->integer('last_fetched_page')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_source_orchestration_states');
    }
};
