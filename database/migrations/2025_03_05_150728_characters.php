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
            $table->integer('health')->default(108);
            $table->integer('max_health')->default(108);
            $table->integer('mana')->default(36);
            $table->integer('max_mana')->default(36);
            $table->integer('experience')->default(0);
            $table->integer('body')->default(3);  // Тіло
            $table->integer('strength')->default(3);  // Сила
            $table->integer('agility')->default(3);  // Ловкість
            $table->integer('intelligence')->default(3);  // Розум
            $table->integer('level')->default(1);
            $table->integer('damage')->default(5);
            $table->integer('armor')->default(0);
            $table->integer('gold')->default(0);
            $table->integer('spawn_x')->default(3);
            $table->integer('spawn_y')->default(3);
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
