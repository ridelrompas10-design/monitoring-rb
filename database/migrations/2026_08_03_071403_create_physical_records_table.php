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
    Schema::create('physical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->date('record_date');
        $table->decimal('weight_kg', 5, 2)->nullable();
        $table->decimal('height_cm', 5, 2)->nullable();
        $table->integer('run_12_min_dist')->nullable(); // Dalam meter
        $table->integer('pull_up_reps')->nullable();
        $table->integer('sit_up_reps')->nullable();
        $table->integer('push_up_reps')->nullable();
        $table->decimal('shuttle_run_sec', 5, 2)->nullable(); // Dalam detik
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_records');
    }
};
