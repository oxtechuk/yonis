<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('title')->nullable();
            $table->string('title_en')->nullable();
            $table->string('specialty')->nullable();
            $table->string('specialty_en')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('experience_years')->default(5);
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
