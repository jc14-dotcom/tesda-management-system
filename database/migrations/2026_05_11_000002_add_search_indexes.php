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
        Schema::table('users', function (Blueprint $table) {
            $table->index('name', 'users_name_index');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->index('status', 'profiles_status_index');
            $table->index('region', 'profiles_region_index');
            $table->index('branch', 'profiles_branch_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index('expiration_date', 'certificates_expiration_date_index');
            $table->index('status', 'certificates_status_index');
            $table->index('verification_status', 'certificates_verification_status_index');
            $table->index('last_notified_at', 'certificates_last_notified_at_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('document_name', 'documents_document_name_index');
            $table->index('certificate_no', 'documents_certificate_no_index');
            $table->index(['user_id', 'created_at'], 'documents_user_id_created_at_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('read_at', 'notifications_read_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_index');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_status_index');
            $table->dropIndex('profiles_region_index');
            $table->dropIndex('profiles_branch_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('certificates_expiration_date_index');
            $table->dropIndex('certificates_status_index');
            $table->dropIndex('certificates_verification_status_index');
            $table->dropIndex('certificates_last_notified_at_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_document_name_index');
            $table->dropIndex('documents_certificate_no_index');
            $table->dropIndex('documents_user_id_created_at_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_read_at_index');
        });
    }
};
