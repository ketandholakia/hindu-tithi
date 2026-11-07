<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panchang_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('send_time', 5)->default('08:00');
            $table->string('timezone', 64)->default('Asia/Kolkata');
            $table->decimal('latitude', 10, 7)->default(28.6139);
            $table->decimal('longitude', 11, 7)->default(77.2090);
            $table->decimal('elevation_meters', 7, 2)->default(0);
            $table->boolean('notify_ekadashi')->default(true);
            $table->boolean('notify_purnima')->default(true);
            $table->boolean('notify_amavasya')->default(true);
            $table->boolean('notify_festivals')->default(true);
            $table->date('last_sent_on')->nullable();
            $table->timestamps();
            $table->index(['enabled', 'send_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panchang_reminder_settings');
    }
};
