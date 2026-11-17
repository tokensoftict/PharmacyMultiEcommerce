<?php

namespace App\Http\Controllers\Api\General;

use App\Classes\Settings;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * GET /api/app/contact-info
 *
 * Returns backend-controlled contact details and map coordinates for the mobile app's
 * Contact Us / Store Location dialog.
 */
class ContactInfoController extends ApiController
{
    public const CACHE_KEY = 'app_contact_info:payload';
    public const CACHE_TTL = 300;

    public const DEFAULTS = [
        'store_title' => '📍 Our Store',
        'store_address' => 'Muritala Muhamed Way, opposite Forte Oil, Ilorin 240101, Kwara State',
        'phone_number' => '+2349095950088',
        'email' => 'info@generaldrugcentre.com',
        'whatsapp_number' => '2349095950088',
        'store_location' => [
            'latitude' => 8.4915319,
            'longitude' => 4.566907,
            'latitudeDelta' => 0.005,
            'longitudeDelta' => 0.005,
        ],
    ];

    public function __invoke(Settings $settings): JsonResponse
    {
        $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () use ($settings) {
            return $this->resolvePayload($settings);
        });

        return response()
            ->json([
                'status' => true,
                'success' => true,
                'data' => $data,
            ])
            ->header('Cache-Control', 'public, max-age=60');
    }

    public function resolvePayload(Settings $settings): array
    {
        try {
            $all = $settings->all();
        } catch (Throwable) {
            $all = [];
        }

        $title = $this->cleanString($all['store_title'] ?? null) ?? self::DEFAULTS['store_title'];
        $address = $this->cleanString($all['contact_address'] ?? ($all['first_address'] ?? null))
            ?? self::DEFAULTS['store_address'];
        $phone = $this->cleanString($all['contact_phone'] ?? ($all['contact_number'] ?? null))
            ?? self::DEFAULTS['phone_number'];
        $email = $this->cleanString($all['contact_email'] ?? null) ?? self::DEFAULTS['email'];
        $whatsappRaw = $this->cleanString($all['contact_whatsapp'] ?? null) ?? self::DEFAULTS['whatsapp_number'];
        $whatsapp = preg_replace('/[^0-9]/', '', $whatsappRaw) ?: self::DEFAULTS['whatsapp_number'];

        $lat = $this->coord($all['store_latitude'] ?? null, -90, 90, self::DEFAULTS['store_location']['latitude']);
        $lng = $this->coord($all['store_longitude'] ?? null, -180, 180, self::DEFAULTS['store_location']['longitude']);
        $latDelta = $this->coord($all['store_latitude_delta'] ?? null, 0.0001, 10, self::DEFAULTS['store_location']['latitudeDelta']);
        $lngDelta = $this->coord($all['store_longitude_delta'] ?? null, 0.0001, 10, self::DEFAULTS['store_location']['longitudeDelta']);

        return [
            'store_title' => $title,
            'store_address' => $address,
            'phone_number' => $phone,
            'email' => $email,
            'whatsapp_number' => $whatsapp,
            'store_location' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'latitudeDelta' => $latDelta,
                'longitudeDelta' => $lngDelta,
            ],
        ];
    }

    private function cleanString(mixed $val): ?string
    {
        if (! is_string($val)) {
            return null;
        }
        $trimmed = trim(strip_tags($val));

        return $trimmed !== '' ? $trimmed : null;
    }

    private function coord(mixed $val, float $min, float $max, float $default): float
    {
        if (! is_numeric($val)) {
            return $default;
        }
        $num = (float) $val;

        return ($num >= $min && $num <= $max) ? $num : $default;
    }
}

