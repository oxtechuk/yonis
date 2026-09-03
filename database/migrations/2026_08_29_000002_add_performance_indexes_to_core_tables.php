<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Indexes on bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['date', 'status'], 'idx_bookings_date_status');
            $table->index(['patient_id', 'status'], 'idx_bookings_user_status'); // patient_id is the FK column name
            $table->index('service_id', 'idx_bookings_service_id');
            $table->index('booking_reference', 'idx_bookings_reference');
        });

        // Composite string index uses prefix lengths to stay within MySQL's 1000-byte limit
        // booking_type & consultation_type are short enum-like values (max ~20 chars each)
        DB::statement('CREATE INDEX idx_bookings_types ON bookings (booking_type(50), consultation_type(50))');

        // 2. Indexes on payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_payments_status_created');
            $table->index('booking_id', 'idx_payments_booking_id');
        });

        // 3. Indexes on users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
        });

        // 4. Index on settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->index('key', 'idx_settings_key');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_date_status');
            $table->dropIndex('idx_bookings_user_status');
            $table->dropIndex('idx_bookings_types');
            $table->dropIndex('idx_bookings_service_id');
            $table->dropIndex('idx_bookings_reference');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status_created');
            $table->dropIndex('idx_payments_booking_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('idx_settings_key');
        });
    }
};
