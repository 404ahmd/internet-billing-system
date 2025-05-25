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
        Schema::create('router_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->decimal('cpu_load', 5, 2);
            $table->decimal('memory_usage', 5, 2);
            $table->timestamp('logged_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('router_stats');
    }
};
