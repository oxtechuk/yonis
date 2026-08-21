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
            $table->foreignId('patient_id')->nullable()->change();
            $table->string('title')->nullable()->after('end_time');
            $table->text('notes')->nullable()->after('title');
            $table->json('temp_user_data')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable(false)->change();
            $table->dropColumn(['title', 'notes', 'temp_user_data']);
        });
    }
};
