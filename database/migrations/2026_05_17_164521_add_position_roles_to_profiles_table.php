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
        if (! Schema::hasColumn('profiles', 'position_roles')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->json('position_roles')->nullable()->after('position_title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('position_roles');
        });
    }
};
