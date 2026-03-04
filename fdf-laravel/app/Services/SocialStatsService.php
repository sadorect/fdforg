<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialStatsService
{
    public function buildChannels(array $settings): array
    {
        $channels = [
            $this->facebookChannel($settings),
            $this->instagramChannel($settings),
            $this->youtubeChannel($settings),
            $this->xChannel($settings),
            $this->linkedinChannel($settings),
            $this->simpleChannel('tiktok', 'TikTok', $settings['social_tiktok_url'] ?? '', 'Watch'),
        ];

        return collect($channels)
            ->filter(fn (array $channel) => !empty($channel['url']) || (($channel['status'] ?? 'disconnected') === 'connected'))
            ->values()
            ->all();
    }

    private function facebookChannel(array $settings): array
    {
        $channel = $this->baseChannel('facebook', 'Facebook', $settings['social_facebook_url'] ?? '', 'Follow');
        $pageId = trim((string) ($settings['social_facebook_page_id'] ?? ''));
        $token = trim((string) ($settings['social_facebook_access_token'] ?? ''));

        if ($pageId === '' || $token === '') {
            return $this->notConnected($channel, 'Add Facebook Page ID and Access Token to show live stats.');
        }

        $cacheKey = 'social_stats_facebook_' . md5($pageId);
        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($pageId, $token) {
            $response = Http::timeout(8)->get("https://graph.facebook.com/v23.0/{$pageId}", [
                'fields' => 'name,fan_count,followers_count',
                'access_token' => $token,
            ]);

            if (!$response->successful()) {
                Log::warning('Facebook stats fetch failed.', ['page_id' => $pageId, 'status' => $response->status()]);

                return null;
            }

            return $response->json();
        });

        if (!is_array($payload)) {
            return $this->notConnected($channel, 'Unable to load Facebook stats. Check token/page permissions.');
        }

        $likes = $this->toInt($payload['fan_count'] ?? null);
        $followers = $this->toInt($payload['followers_count'] ?? null);

        return [
            ...$channel,
            'status' => 'connected',
            'primary_label' => 'Followers',
            'primary_value' => $this->formatCount($followers ?? $likes),
            'details' => [
                ['label' => 'Page Likes', 'value' => $this->formatCount($likes)],
                ['label' => 'Followers', 'value' => $this->formatCount($followers)],
            ],
        ];
    }

    private function instagramChannel(array $settings): array
    {
        $channel = $this->baseChannel('instagram', 'Instagram', $settings['social_instagram_url'] ?? '', 'Follow');
        $userId = trim((string) ($settings['social_instagram_user_id'] ?? ''));
        $token = trim((string) ($settings['social_instagram_access_token'] ?? ''));

        if ($userId === '' || $token === '') {
            return $this->notConnected($channel, 'Add Instagram User ID and Access Token to show live stats.');
        }

        $cacheKey = 'social_stats_instagram_' . md5($userId);
        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($userId, $token) {
            $response = Http::timeout(8)->get("https://graph.facebook.com/v23.0/{$userId}", [
                'fields' => 'username,followers_count,media_count',
                'access_token' => $token,
            ]);

            if (!$response->successful()) {
                Log::warning('Instagram stats fetch failed.', ['user_id' => $userId, 'status' => $response->status()]);

                return null;
            }

            return $response->json();
        });

        if (!is_array($payload)) {
            return $this->notConnected($channel, 'Unable to load Instagram stats. Check token/account permissions.');
        }

        $followers = $this->toInt($payload['followers_count'] ?? null);
        $mediaCount = $this->toInt($payload['media_count'] ?? null);

        return [
            ...$channel,
            'status' => 'connected',
            'primary_label' => 'Followers',
            'primary_value' => $this->formatCount($followers),
            'details' => [
                ['label' => 'Followers', 'value' => $this->formatCount($followers)],
                ['label' => 'Posts', 'value' => $this->formatCount($mediaCount)],
            ],
        ];
    }

    private function youtubeChannel(array $settings): array
    {
        $channel = $this->baseChannel('youtube', 'YouTube', $settings['social_youtube_url'] ?? '', 'Watch');
        $channelId = trim((string) ($settings['social_youtube_channel_id'] ?? ''));
        $apiKey = trim((string) ($settings['social_youtube_api_key'] ?? ''));

        if ($channelId === '' || $apiKey === '') {
            return $this->notConnected($channel, 'Add YouTube Channel ID and API Key to show live stats.');
        }

        $cacheKey = 'social_stats_youtube_' . md5($channelId);
        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($channelId, $apiKey) {
            $response = Http::timeout(8)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'statistics,snippet',
                'id' => $channelId,
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                Log::warning('YouTube stats fetch failed.', ['channel_id' => $channelId, 'status' => $response->status()]);

                return null;
            }

            $items = $response->json('items');

            return is_array($items) && isset($items[0]) ? $items[0] : null;
        });

        if (!is_array($payload)) {
            return $this->notConnected($channel, 'Unable to load YouTube stats. Check channel ID/key and quota.');
        }

        $stats = is_array($payload['statistics'] ?? null) ? $payload['statistics'] : [];
        $subscribers = $this->toInt($stats['subscriberCount'] ?? null);
        $views = $this->toInt($stats['viewCount'] ?? null);
        $videos = $this->toInt($stats['videoCount'] ?? null);

        return [
            ...$channel,
            'status' => 'connected',
            'primary_label' => 'Subscribers',
            'primary_value' => $this->formatCount($subscribers),
            'details' => [
                ['label' => 'Subscribers', 'value' => $this->formatCount($subscribers)],
                ['label' => 'Total Views', 'value' => $this->formatCount($views)],
                ['label' => 'Videos', 'value' => $this->formatCount($videos)],
            ],
        ];
    }

    private function xChannel(array $settings): array
    {
        $channel = $this->baseChannel('x', 'X / Twitter', $settings['social_x_url'] ?? '', 'Follow');
        $username = trim((string) ($settings['social_x_username'] ?? ''));
        $token = trim((string) ($settings['social_x_bearer_token'] ?? ''));

        if ($username === '' || $token === '') {
            return $this->notConnected($channel, 'Add X username and bearer token to show live stats.');
        }

        $cacheKey = 'social_stats_x_' . md5($username);
        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($username, $token) {
            $response = Http::withToken($token)
                ->timeout(8)
                ->get("https://api.x.com/2/users/by/username/{$username}", [
                    'user.fields' => 'public_metrics,name,username',
                ]);

            if (!$response->successful()) {
                Log::warning('X stats fetch failed.', ['username' => $username, 'status' => $response->status()]);

                return null;
            }

            return $response->json('data');
        });

        if (!is_array($payload)) {
            return $this->notConnected($channel, 'Unable to load X stats. Check username/token/plan access.');
        }

        $metrics = is_array($payload['public_metrics'] ?? null) ? $payload['public_metrics'] : [];
        $followers = $this->toInt($metrics['followers_count'] ?? null);
        $following = $this->toInt($metrics['following_count'] ?? null);
        $tweets = $this->toInt($metrics['tweet_count'] ?? null);

        return [
            ...$channel,
            'status' => 'connected',
            'primary_label' => 'Followers',
            'primary_value' => $this->formatCount($followers),
            'details' => [
                ['label' => 'Followers', 'value' => $this->formatCount($followers)],
                ['label' => 'Following', 'value' => $this->formatCount($following)],
                ['label' => 'Posts', 'value' => $this->formatCount($tweets)],
            ],
        ];
    }

    private function linkedinChannel(array $settings): array
    {
        $channel = $this->baseChannel('linkedin', 'LinkedIn', $settings['social_linkedin_url'] ?? '', 'Connect');
        $orgId = trim((string) ($settings['social_linkedin_org_id'] ?? ''));
        $token = trim((string) ($settings['social_linkedin_access_token'] ?? ''));

        if ($orgId === '' || $token === '') {
            return $this->notConnected($channel, 'Add LinkedIn Org ID and access token to show live stats.');
        }

        $cacheKey = 'social_stats_linkedin_' . md5($orgId);
        $followers = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($orgId, $token) {
            $response = Http::withToken($token)
                ->timeout(8)
                ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
                ->get("https://api.linkedin.com/v2/networkSizes/urn:li:organization:{$orgId}", [
                    'edgeType' => 'CompanyFollowedByMember',
                ]);

            if (!$response->successful()) {
                Log::warning('LinkedIn stats fetch failed.', ['org_id' => $orgId, 'status' => $response->status()]);

                return null;
            }

            return $this->toInt($response->json('firstDegreeSize'));
        });

        if ($followers === null) {
            return $this->notConnected($channel, 'Unable to load LinkedIn stats. Check org scope/permissions.');
        }

        return [
            ...$channel,
            'status' => 'connected',
            'primary_label' => 'Followers',
            'primary_value' => $this->formatCount($followers),
            'details' => [
                ['label' => 'Followers', 'value' => $this->formatCount($followers)],
            ],
        ];
    }

    private function simpleChannel(string $key, string $label, ?string $url, string $actionText): array
    {
        $channel = $this->baseChannel($key, $label, $url, $actionText);

        return $this->notConnected($channel, 'Live stats are not configured for this channel yet.');
    }

    private function baseChannel(string $key, string $label, ?string $url, string $actionText): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'url' => $url,
            'action_text' => $actionText,
            'status' => 'disconnected',
            'primary_label' => 'Status',
            'primary_value' => 'Not Connected',
            'details' => [],
            'message' => null,
        ];
    }

    private function notConnected(array $channel, string $message): array
    {
        $channel['status'] = 'disconnected';
        $channel['message'] = $message;

        return $channel;
    }

    private function toInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        return null;
    }

    private function formatCount(?int $value): string
    {
        if ($value === null) {
            return 'N/A';
        }

        return number_format($value);
    }
}
