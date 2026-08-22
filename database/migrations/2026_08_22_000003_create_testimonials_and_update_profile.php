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
        // 1. Create Testimonials Table
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('client_name_ar');
                $table->string('client_name_en')->nullable();
                $table->string('client_avatar')->nullable();
                $table->integer('rating')->default(5);
                $table->text('content_ar');
                $table->text('content_en')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Add columns to doctor_profiles table
        Schema::table('doctor_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_profiles', 'hero_image')) {
                $table->string('hero_image')->nullable();
            }
            if (!Schema::hasColumn('doctor_profiles', 'about_image')) {
                $table->string('about_image')->nullable();
            }
            if (!Schema::hasColumn('doctor_profiles', 'title_en')) {
                $table->string('title_en')->nullable();
            }
            if (!Schema::hasColumn('doctor_profiles', 'bio_en')) {
                $table->text('bio_en')->nullable();
            }
            if (!Schema::hasColumn('doctor_profiles', 'specialties_en')) {
                $table->json('specialties_en')->nullable();
            }
        });

        // 3. Add title_en to reels table
        Schema::table('reels', function (Blueprint $table) {
            if (!Schema::hasColumn('reels', 'title_en')) {
                $table->string('title_en')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
