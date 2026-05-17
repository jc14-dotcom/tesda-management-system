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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('contact_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('company_id')->nullable();
            $table->string('position_title')->nullable();
            $table->json('position_roles')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('status')->default('active');
            $table->date('date_hired')->nullable();
            $table->string('region')->nullable();
            $table->string('branch')->nullable();
            $table->string('tesda_registry_number')->nullable();
            $table->string('qualification_title')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
