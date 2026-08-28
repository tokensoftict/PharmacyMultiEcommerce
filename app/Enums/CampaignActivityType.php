<?php

namespace App\Enums;

class CampaignActivityType
{
    const IMPRESSION      = 'impression';
    const DISMISSED       = 'dismissed';
    const CLICKED         = 'clicked';
    const CONVERTED       = 'converted';
    const PUSH_SCHEDULED  = 'push_scheduled';
    const PUSH_SENT       = 'push_sent';
    const PUSH_FAILED     = 'push_failed';
    const PUSH_OPENED     = 'push_opened';

    public static function all(): array
    {
        return [
            self::IMPRESSION, self::DISMISSED, self::CLICKED, self::CONVERTED,
            self::PUSH_SCHEDULED, self::PUSH_SENT, self::PUSH_FAILED, self::PUSH_OPENED,
        ];
    }
}
