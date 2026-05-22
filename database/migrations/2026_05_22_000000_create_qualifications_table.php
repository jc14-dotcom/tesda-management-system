<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);   // 'trainer' | 'assessor'
            $table->string('title', 255);
            $table->timestamps();

            $table->unique(['type', 'title']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
