<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Clear old free-text qualification titles from all profiles.
     * Users will now select from admin-managed qualification titles via dropdowns.
     */
    public function up(): void
    {
        DB::table('profiles')->update([
            'trainer_qualification_titles'  => null,
            'assessor_qualification_titles' => null,
        ]);
    }

    /**
     * Data cannot be restored — intentionally irreversible.
     */
    public function down(): void
    {
        //
    }
};
