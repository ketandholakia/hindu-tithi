<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id', 64);
            $table->string('type', 16);
            $table->string('send_time', 5)->default('08:00');
            $table->string('timezone', 64)->default('Asia/Kolkata');
            $table->decimal('latitude', 10, 7)->default(28.6139);
            $table->decimal('longitude', 11, 7)->default(77.2090);
            $table->string('language', 2)->default('en');
            $table->date('last_sent_on')->nullable();
            $table->timestamps();
            $table->unique(['chat_id', 'type']);
            $table->index(['send_time', 'timezone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_reminders');
    }
};
