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
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('tesda_registry_number', 'profiles_tesda_registry_number_index');
            $table->index('employment_status', 'profiles_employment_status_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index('certificate_name', 'certificates_certificate_name_index');
            $table->index('certificate_number', 'certificates_certificate_number_index');
            $table->index('qualification_title', 'certificates_qualification_title_index');
            $table->index('issued_by', 'certificates_issued_by_index');
            $table->index(['user_id', 'certificate_name'], 'certificates_user_id_certificate_name_index');
            $table->index(['status', 'expiration_date'], 'certificates_status_expiration_date_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('original_name', 'documents_original_name_index');
            $table->index('type', 'documents_type_index');
            $table->index('created_at', 'documents_created_at_index');
            $table->index('issued_on', 'documents_issued_on_index');
            $table->index('valid_until', 'documents_valid_until_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('type', 'notifications_type_index');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->index('created_at', 'password_reset_tokens_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_tesda_registry_number_index');
            $table->dropIndex('profiles_employment_status_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('certificates_certificate_name_index');
            $table->dropIndex('certificates_certificate_number_index');
            $table->dropIndex('certificates_qualification_title_index');
            $table->dropIndex('certificates_issued_by_index');
            $table->dropIndex('certificates_user_id_certificate_name_index');
            $table->dropIndex('certificates_status_expiration_date_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_original_name_index');
            $table->dropIndex('documents_type_index');
            $table->dropIndex('documents_created_at_index');
            $table->dropIndex('documents_issued_on_index');
            $table->dropIndex('documents_valid_until_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_type_index');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropIndex('password_reset_tokens_created_at_index');
        });
    }
};
