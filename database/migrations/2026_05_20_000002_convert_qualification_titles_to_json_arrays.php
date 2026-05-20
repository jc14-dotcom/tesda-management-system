<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new JSON array columns
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('trainer_qualification_titles')->nullable()->after('trainer_qualification_title');
            $table->json('assessor_qualification_titles')->nullable()->after('assessor_qualification_title');
        });

        // 2. Migrate existing single-string data → single-element arrays
        DB::table('profiles')->orderBy('id')->each(function (object $profile) {
            $updates = [];
            if (!empty($profile->trainer_qualification_title)) {
                $updates['trainer_qualification_titles'] = json_encode([$profile->trainer_qualification_title]);
            }
            if (!empty($profile->assessor_qualification_title)) {
                $updates['assessor_qualification_titles'] = json_encode([$profile->assessor_qualification_title]);
            }
            if ($updates) {
                DB::table('profiles')->where('id', $profile->id)->update($updates);
            }
        });

        // 3. Drop the old single-value columns
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['trainer_qualification_title', 'assessor_qualification_title']);
        });
    }

    public function down(): void
    {
        // 1. Re-add old string columns
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('trainer_qualification_title')->nullable()->after('qualification_title');
            $table->string('assessor_qualification_title')->nullable()->after('trainer_qualification_title');
        });

        // 2. Migrate first element of each array back to the string column
        DB::table('profiles')->orderBy('id')->each(function (object $profile) {
            $updates = [];
            if (!empty($profile->trainer_qualification_titles)) {
                $arr = json_decode($profile->trainer_qualification_titles, true);
                if (is_array($arr) && !empty($arr[0])) {
                    $updates['trainer_qualification_title'] = $arr[0];
                }
            }
            if (!empty($profile->assessor_qualification_titles)) {
                $arr = json_decode($profile->assessor_qualification_titles, true);
                if (is_array($arr) && !empty($arr[0])) {
                    $updates['assessor_qualification_title'] = $arr[0];
                }
            }
            if ($updates) {
                DB::table('profiles')->where('id', $profile->id)->update($updates);
            }
        });

        // 3. Drop the JSON columns
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['trainer_qualification_titles', 'assessor_qualification_titles']);
        });
    }
};
