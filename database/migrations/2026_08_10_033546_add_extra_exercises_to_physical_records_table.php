<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_records', function (Blueprint $table) {
            // Kolom JSON ini bisa menampung banyak data latihan sekaligus
            $table->json('extra_exercises')->nullable()->after('push_up_reps');
        });
    }

    public function down(): void
    {
        Schema::table('physical_records', function (Blueprint $table) {
            $table->dropColumn('extra_exercises');
        });
    }
};