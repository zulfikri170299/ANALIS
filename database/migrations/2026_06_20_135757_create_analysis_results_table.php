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
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained()->onDelete('cascade');
            $table->string('trend')->nullable();
            $table->decimal('rsi', 8, 2)->nullable();
            $table->string('macd')->nullable();
            $table->decimal('support', 16, 4)->nullable();
            $table->decimal('resistance', 16, 4)->nullable();
            $table->string('recommendation')->nullable();
            $table->integer('confidence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
