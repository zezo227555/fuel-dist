<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fill_up_denials', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number');
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('station_id')->constrained()->restrictOnDelete();
            $table->foreignId('attendant_id')->constrained('users')->restrictOnDelete();
            $table->string('reason');
            $table->timestamp('attempted_at');
            $table->timestamp('eligible_at')->nullable();
            $table->timestamps();

            $table->index(['plate_number', 'attempted_at']);
            $table->index(['station_id', 'attempted_at']);
            $table->index('reason');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fill_up_denials');
    }
};
