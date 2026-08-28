<?php

namespace App\Http\ViewModels;

use App\Models\User;

/**
 * ReferralShareViewModel
 *
 * DTO carrying all data required for the referral invitation page and OpenGraph meta tags.
 */
readonly class ReferralShareViewModel
{
    public string $referrerName;
    public string $referralCode;
    public string $title;
    public string $description;
    public string $ogDescription;
    public string $image;
    public string $url;
    public string $canonicalUrl;
    public string $detourUrl;
    public string $playStoreUrl;
    public string $appStoreUrl;
    public string $siteName;
    public string $locale;
    public string $bonusPointsText;

    public function __construct(string $referralCode, ?User $referrer, string $currentUrl)
    {
        $this->referralCode = strtoupper(trim($referralCode));

        // Format referrer name
        if ($referrer && (!empty($referrer->firstname) || !empty($referrer->lastname))) {
            $this->referrerName = trim("{$referrer->firstname} {$referrer->lastname}");
        } elseif ($referrer && !empty($referrer->name)) {
            $this->referrerName = trim($referrer->name);
        } else {
            $this->referrerName = 'A Friend';
        }

        $this->siteName = 'PS General Drugs Centre';
        $this->locale   = 'en_NG';

        // Title and descriptions
        $this->title = "Join General Drug Centre | You've been invited by {$this->referrerName}";
        
        $this->ogDescription = "You have been referred by {$this->referrerName}! Download the PS General Drug Centre app to buy genuine pharmaceuticals, groceries, and supermarket items at the best prices, and earn loyalty reward points.";
        
        $this->description = $this->ogDescription;

        // Image — Ensure public HTTPS absolute logo image for WhatsApp and OpenGraph crawlers
        $appLogo = asset('logo/logo.png');
        if (!str_starts_with($appLogo, 'http') || str_contains($appLogo, 'localhost')) {
            $appLogo = 'https://generaldrugcentre.com/logo/logo.png';
        } else {
            $appLogo = str_replace('http://', 'https://', $appLogo);
        }
        $this->image = $appLogo;

        // URLs
        $this->url          = $currentUrl;
        $this->canonicalUrl = $currentUrl;

        $detourBase = rtrim(
            config('app.detour_referral_base_url', 'https://psgdc.godetour.link/PrdRthERNv/ref/'),
            '/'
        );
        $this->detourUrl    = !empty($this->referralCode) ? "{$detourBase}/{$this->referralCode}" : $detourBase;

        $this->playStoreUrl = config(
            'app.android_app_store_url',
            'https://play.google.com/store/apps/details?id=com.tokensoftict.psgdc'
        );
        $this->appStoreUrl  = config(
            'app.ios_app_store_url',
            'https://apps.apple.com/us/app/ps-gdc/id6741708076'
        );

        $this->bonusPointsText = 'Earn Loyalty Points on Phone Verification';
    }

    public function faviconUrl(): string
    {
        return $this->image;
    }
}
