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
        Schema::table('api_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('api_keys', 'abilities')) {
                $table->json('abilities')->default('[]')->after('key_hash');
            }
            if (!Schema::hasColumn('api_keys', 'rate_limit_per_minute')) {
                $table->integer('rate_limit_per_minute')->default(60)->after('abilities');
            }
            if (!Schema::hasColumn('api_keys', 'rate_limit_per_day')) {
                $table->integer('rate_limit_per_day')->default(1440)->after('rate_limit_per_minute');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            if (Schema::hasColumn('api_keys', 'abilities')) {
                $table->dropColumn('abilities');
            }
            if (Schema::hasColumn('api_keys', 'rate_limit_per_minute')) {
                $table->dropColumn('rate_limit_per_minute');
            }
            if (Schema::hasColumn('api_keys', 'rate_limit_per_day')) {
                $table->dropColumn('rate_limit_per_day');
            }
        });
    }
};
