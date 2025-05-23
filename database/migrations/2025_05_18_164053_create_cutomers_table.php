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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->string('username')->unique();
            $table->string('package');
            $table->string('address');
            $table->string('group')->nullable();
            $table->string('phone', 20);
            $table->date('join_date');
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->date('last_payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
