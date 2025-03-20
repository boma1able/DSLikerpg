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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('race');
            $table->string('avatar');
            $table->string('class');
            $table->integer('health')->nullable();
            $table->integer('max_health')->nullable();
            $table->integer('mana')->nullable();
            $table->integer('max_mana')->nullable();
            $table->integer('experience')->default(0);
            $table->integer('level')->default(1);
            $table->integer('skill_points')->default(0);
            $table->integer('damage')->nullable();
            $table->integer('armor')->nullable();
            $table->integer('body')->nullable();
            $table->integer('strength')->nullable();
            $table->integer('agility')->nullable();
            $table->integer('intelligence')->nullable();
            $table->integer('gold')->default(0);
            $table->integer('position_x')->default(5);
            $table->integer('position_y')->default(4);
            $table->integer('offset_x')->default(-5);
            $table->integer('offset_y')->default(-4);
            $table->boolean('is_online')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
