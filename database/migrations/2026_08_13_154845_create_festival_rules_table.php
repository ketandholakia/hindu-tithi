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
        Schema::create('festival_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained('festival_definitions')->onDelete('cascade');
            $table->string('rule_type');
            $table->string('month')->nullable();
            $table->string('paksha')->nullable();
            $table->integer('tithi')->nullable();
            $table->string('nakshatra')->nullable();
            $table->integer('weekday')->nullable();
            $table->string('required_kala')->nullable();
            $table->integer('priority')->default(1);
            $table->string('tradition')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_rules');
    }
};
