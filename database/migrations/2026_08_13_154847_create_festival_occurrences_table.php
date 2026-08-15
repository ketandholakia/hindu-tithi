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
        Schema::create('festival_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained('festival_definitions')->onDelete('cascade');
            $table->date('date');
            $table->integer('location_id')->nullable();
            $table->string('calendar_system')->nullable();
            $table->foreignId('rule_id')->nullable()->constrained('festival_rules')->onDelete('set null');
            $table->integer('tithi')->nullable();
            $table->string('nakshatra')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('kala')->nullable();
            $table->string('tradition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_occurrences');
    }
};
