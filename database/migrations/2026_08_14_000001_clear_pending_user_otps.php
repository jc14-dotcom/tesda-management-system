<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Pending accounts cannot yet use verification codes. Clear codes issued
     * before OTP delivery was moved to the first approved login.
     */
    public function up(): void
    {
        DB::table('users')
            ->where(function ($query) {
                $query->whereNotNull('otp')->orWhereNotNull('otp_expires_at');
            })
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('profiles')
                    ->whereColumn('profiles.user_id', 'users.id')
                    ->where('profiles.status', 'pending');
            })
            ->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);
    }

    public function down(): void
    {
        // Cleared OTP values are intentionally not restored.
    }
};
