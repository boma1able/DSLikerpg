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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('level');
            $table->text('description')->nullable();
            $table->enum('type', [
                'weapon', 'shield', 'helmet', 'chest', 'gloves', 'leggings', 'boots',
                'ring', 'amulet', 'necklace', 'cloak', 'belt', 'potion', 'ingredient'
            ]);
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary'])->default('common');
            $table->boolean('stackable')->default(false);
            $table->integer('max_stack')->default(1);
            $table->float('weight')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
