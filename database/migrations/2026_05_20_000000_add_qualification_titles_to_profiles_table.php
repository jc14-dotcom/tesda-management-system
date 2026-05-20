<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('trainer_qualification_title')->nullable()->after('qualification_title');
            $table->string('assessor_qualification_title')->nullable()->after('trainer_qualification_title');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['trainer_qualification_title', 'assessor_qualification_title']);
        });
    }
};
