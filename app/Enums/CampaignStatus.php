<?php

namespace App\Enums;

class CampaignStatus
{
    const DRAFT    = 'draft';
    const ACTIVE   = 'active';
    const PAUSED   = 'paused';
    const EXPIRED  = 'expired';
    const ARCHIVED = 'archived';

    public static function all(): array
    {
        return [self::DRAFT, self::ACTIVE, self::PAUSED, self::EXPIRED, self::ARCHIVED];
    }
}
