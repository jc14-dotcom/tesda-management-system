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
        // Consolidated into create_documents_table — skip if columns already exist.
        if (! Schema::hasColumn('documents', 'certificate_no')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('certificate_no')->nullable()->after('document_name');
                $table->date('issued_on')->nullable()->after('certificate_no');
                $table->date('valid_until')->nullable()->after('issued_on');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['certificate_no', 'issued_on', 'valid_until']);
        });
    }
};