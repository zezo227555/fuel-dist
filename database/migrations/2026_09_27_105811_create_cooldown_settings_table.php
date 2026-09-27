<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooldown_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('period_hours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooldown_settings');
    }
};
