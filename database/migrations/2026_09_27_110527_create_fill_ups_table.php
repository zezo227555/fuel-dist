<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fill_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->restrictOnDelete();
            $table->foreignId('attendant_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('filled_at');
            $table->timestamps();

            $table->index(['vehicle_id', 'filled_at']);
            $table->index(['station_id', 'filled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fill_ups');
    }
};
