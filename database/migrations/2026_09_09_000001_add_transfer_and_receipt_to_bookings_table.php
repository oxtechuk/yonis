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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'transfer_number')) {
                $table->string('transfer_number')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('bookings', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('transfer_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('bookings', 'payment_method')) $cols[] = 'payment_method';
            if (Schema::hasColumn('bookings', 'transfer_number')) $cols[] = 'transfer_number';
            if (Schema::hasColumn('bookings', 'receipt_image')) $cols[] = 'receipt_image';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
