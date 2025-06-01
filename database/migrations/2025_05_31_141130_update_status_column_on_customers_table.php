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
         Schema::table('customers', function (Blueprint $table) {
            // Ubah kolom status menjadi enum baru (jika enum berbeda)
            $table->enum('status', ['active', 'inactive', 'terminated', 'free', 'other'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Kembalikan ke enum awal (jika ingin rollback)
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active')->change();
        });
    }
};
