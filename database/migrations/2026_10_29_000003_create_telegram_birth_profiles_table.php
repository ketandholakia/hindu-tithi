<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_birth_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id', 64);
            $table->string('user_id', 64);
            $table->date('birth_date');
            $table->string('birth_time', 5);
            $table->string('timezone', 64)->default('Asia/Kolkata');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->timestamps();
            $table->unique(['chat_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_birth_profiles');
    }
};
