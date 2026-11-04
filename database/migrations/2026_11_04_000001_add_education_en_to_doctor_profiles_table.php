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
        Schema::table('doctor_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_profiles', 'education_en')) {
                $table->json('education_en')->nullable()->after('education');
            }
            if (!Schema::hasColumn('doctor_profiles', 'certificates_en')) {
                $table->json('certificates_en')->nullable()->after('certificates');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_profiles', 'education_en')) {
                $table->dropColumn('education_en');
            }
            if (Schema::hasColumn('doctor_profiles', 'certificates_en')) {
                $table->dropColumn('certificates_en');
            }
        });
    }
};

