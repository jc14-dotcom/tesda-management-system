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
        // Consolidated into create_documents_table — skip if column already exists.
        if (! Schema::hasColumn('documents', 'document_name')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('document_name')->nullable()->after('certificate_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('document_name');
        });
    }
};