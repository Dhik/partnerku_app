<?php

namespace App\Domain\User\Enums;

enum RoleEnum: string
{
    const SuperAdmin = 'superadmin';

    const Client1 = 'client_1';

    const Client2 = 'client_2';

    const TimInternal = 'tim_internal';

    const TimAds = 'tim_ads';

    public static function getDescription($value): string
    {
        return match ($value) {
            self::SuperAdmin => 'SuperAdmin',
            self::Client1 => 'Client User',
            self::Client2 => 'Client Viewers',
            self::TimInternal => 'Tim Internal',
            self::TimAds => 'Tim Ads',
            default => 'Unknown'
        };
    }
}
