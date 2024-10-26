<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lobby_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('round_number');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
            $table->index(['lobby_id', 'round_number']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_rounds');
    }
};
