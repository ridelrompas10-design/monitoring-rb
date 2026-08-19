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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->string('registration_number')->unique();
        $table->string('name');
        $table->enum('gender', ['L', 'P']);
        $table->boolean('is_watchlist')->default(false);
        $table->text('watchlist_notes')->nullable();
        $table->enum('status', ['Aktif', 'Lulus', 'Keluar'])->default('Aktif');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
