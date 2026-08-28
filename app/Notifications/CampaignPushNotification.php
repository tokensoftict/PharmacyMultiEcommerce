<?php

namespace App\Notifications;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

/**
 * CampaignPushNotification
 *
 * FCM push notification for a campaign delivery.
 * Follows the same pattern as DevicePushNotification.
 * The notifiable is a SupermarketUser or WholesalesUser (whichever has device_key).
 */
class CampaignPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Campaign $campaign,
        public readonly User $user
    ) {}

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $campaign = $this->campaign;

        $pushTitle = $campaign->push_title ?? $campaign->title ?? 'New Message';
        $pushBody  = $campaign->push_body ?? $campaign->message ?? '';
        $image     = $campaign->image
            ? asset('storage/' . $campaign->image)
            : "https://wholesale.generaldrugcentre.com/assets/images/logo.png";

        $fcm = new FcmMessage(notification: new FcmNotification(
            title: $pushTitle,
            body: $pushBody,
            image: $image
        ));

        $fcm->name("campaign_{$campaign->id}");

        // Build the data payload — React Native reads these fields in usePushNotification
        $data = [
            'notificationType' => 'CAMPAIGN',
            'campaign_id'      => strval($campaign->id),
            'campaign_slug'    => $campaign->slug,
            'action_type'      => $campaign->action_type,
            'action_data'      => json_encode($campaign->action_data ?? []),
            'display_type'     => $campaign->display_type,
            'title'            => $pushTitle,
            'message'          => $pushBody,
            'image'            => $image,
            'cta_text'         => $campaign->cta_text ?? '',
            'channel'          => 'push',
            // Mirror existing environment field pattern
            'environment'      => $this->resolveEnvironment($notifiable),
        ];

        $fcm->data($data);

        $fcm->custom([
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'color'      => '#004481',
                    'sound'      => 'default',
                    'channel_id' => 'psgdc_campaign_v1',
                ],
                'fcm_options' => [
                    'analytics_label' => 'campaign',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                    ],
                ],
                'fcm_options' => [
                    'analytics_label' => 'campaign',
                ],
            ],
        ]);

        $fcm->token($notifiable->device_key);

        return $fcm;
    }

    private function resolveEnvironment($notifiable): string
    {
        $class = get_class($notifiable);
        return match (true) {
            str_contains($class, 'Supermarket') => 'supermarket',
            str_contains($class, 'Wholesales')  => 'wholesales',
            default                              => 'supermarket',
        };
    }
}
