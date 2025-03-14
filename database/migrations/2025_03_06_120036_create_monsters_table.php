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
        Schema::create('monsters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar');
            $table->integer('health');
            $table->integer('damage');
            $table->integer('level');
            $table->integer('experience');
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->float('hit_chance')->default(0.75);
            $table->integer('gold_min')->default(0);
            $table->integer('gold_max')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monsters');
    }
};
