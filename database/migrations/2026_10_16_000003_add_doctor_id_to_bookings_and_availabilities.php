<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add doctor_id to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('patient_id')->constrained('doctors')->nullOnDelete();
            $table->index('doctor_id', 'idx_bookings_doctor_id');
        });

        // 2. Add doctor_id to availabilities
        Schema::table('availabilities', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('id')->constrained('doctors')->cascadeOnDelete();
            $table->index('doctor_id', 'idx_availabilities_doctor_id');
        });

        // 3. Add doctor_id to blocked_times
        Schema::table('blocked_times', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('id')->constrained('doctors')->cascadeOnDelete();
            $table->index('doctor_id', 'idx_blocked_times_doctor_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });

        Schema::table('blocked_times', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};
