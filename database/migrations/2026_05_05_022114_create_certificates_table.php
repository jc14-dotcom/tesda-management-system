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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_name');
            $table->string('certificate_type', 50);
            $table->string('qualification_title')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('status')->default('valid');
            $table->string('verification_status')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->json('notified_days')->nullable();
            $table->unsignedInteger('notification_count')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expiration_date']);
            $table->index(['certificate_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
