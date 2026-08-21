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
        Schema::table('services', function (Blueprint $table) {
            $table->string('type')->default('both')->after('description'); // clinic, online, both
            $table->decimal('clinic_price', 10, 2)->nullable()->after('price');
            $table->decimal('chat_price', 10, 2)->nullable()->after('clinic_price');
            $table->decimal('voice_price', 10, 2)->nullable()->after('chat_price');
            $table->decimal('video_price', 10, 2)->nullable()->after('voice_price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_type')->default('clinic')->after('service_id'); // clinic, online
            $table->string('consultation_type')->default('clinic')->after('booking_type'); // clinic, chat, voice, video
            $table->decimal('price', 10, 2)->nullable()->after('consultation_type');
            $table->timestamp('rescheduled_at')->nullable()->after('status');
            $table->integer('reschedule_count')->default(0)->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['type', 'clinic_price', 'chat_price', 'voice_price', 'video_price']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_type', 'consultation_type', 'price', 'rescheduled_at', 'reschedule_count']);
        });
    }
};
