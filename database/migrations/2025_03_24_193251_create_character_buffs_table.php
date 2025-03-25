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
        Schema::create('character_buffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            $table->string('buff_name');
            $table->string('label');
            $table->integer('level')->default(1);
            $table->float('buff_amount');
            $table->timestamp('applied_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('hotkey')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_buffs');
    }
};
