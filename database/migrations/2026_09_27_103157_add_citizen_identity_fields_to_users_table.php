<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('national_id', 50)->nullable()->after('phone');
            $table->string('passport_number', 50)->nullable()->after('national_id');

            $table->unique('national_id');
            $table->unique('passport_number');
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['national_id']);
            $table->dropUnique(['passport_number']);
            $table->dropUnique(['phone']);
            $table->dropColumn(['phone', 'national_id', 'passport_number']);
        });
    }
};
