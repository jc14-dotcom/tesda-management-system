<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheBuster
{
    public static function userVersion(int $userId): int
    {
        $key = self::userVersionKey($userId);
        $version = Cache::get($key);

        if (! $version) {
            Cache::forever($key, 1);
            return 1;
        }

        return (int) $version;
    }

    public static function bumpUser(int $userId): void
    {
        $key = self::userVersionKey($userId);
        $version = (int) Cache::get($key, 1);

        Cache::forever($key, $version + 1);
        Cache::forget(self::userDashboardKey($userId));
    }

    public static function userDashboardKey(int $userId): string
    {
        return "user:{$userId}:dashboard:counts";
    }

    public static function adminUsersVersion(): int
    {
        $key = self::adminUsersVersionKey();
        $version = Cache::get($key);

        if (! $version) {
            Cache::forever($key, 1);
            return 1;
        }

        return (int) $version;
    }

    public static function bumpAdminUsers(): void
    {
        $key = self::adminUsersVersionKey();
        $version = (int) Cache::get($key, 1);

        Cache::forever($key, $version + 1);
        Cache::forget(self::adminDashboardKey());
    }

    public static function adminDashboardKey(): string
    {
        return 'admin:dashboard:metrics';
    }

    private static function userVersionKey(int $userId): string
    {
        return "user:{$userId}:cache_version";
    }

    private static function adminUsersVersionKey(): string
    {
        return 'admin:users:cache_version';
    }
}
