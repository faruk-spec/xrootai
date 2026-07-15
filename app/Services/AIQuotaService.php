<?php

namespace App\Services;

use App\Models\Message;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AIQuotaService
{
    /**
     * Get unique identifier for the acting user or session.
     */
    public static function getIdentifier(?User $user, ?string $sessionToken): string
    {
        if ($user) {
            return 'user:' . $user->id;
        }
        return 'guest:' . ($sessionToken ?: session()->getId() ?: 'unknown');
    }

    /**
     * Acquire a concurrent stream lock or counter check for the user/guest.
     * Returns ['allowed' => bool, 'message' => string]
     */
    public static function acquireConcurrentStreamLock(?User $user, ?string $sessionToken): array
    {
        // Admins and Super Admins bypass strict single-stream restrictions
        if ($user && ($user->isSuperAdmin() || in_array(strtolower($user->role), ['admin', 'super admin']))) {
            return ['allowed' => true, 'message' => ''];
        }

        $identifier = self::getIdentifier($user, $sessionToken);
        $maxStreams = (int) SystemSetting::get('plans_concurrent_streams_limit', 1);
        if ($user && in_array(strtolower($user->role), ['pro', 'enterprise'])) {
            $maxStreams = max($maxStreams, 5);
        }

        $lockKey = "ai_concurrent_lock:{$identifier}";
        $activeStreams = (int) Cache::get($lockKey, 0);

        if ($activeStreams >= $maxStreams) {
            return [
                'allowed' => false,
                'message' => "⚠️ **Concurrent stream limit reached!** You already have an active stream running. Please wait for your current generation to finish before starting a new one."
            ];
        }

        // Increment active streams and set safety expiration (2 minutes maximum to prevent orphan locks)
        Cache::put($lockKey, $activeStreams + 1, now()->addMinutes(2));

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Release a concurrent stream lock.
     */
    public static function releaseConcurrentStreamLock(?User $user, ?string $sessionToken): void
    {
        if ($user && ($user->isSuperAdmin() || in_array(strtolower($user->role), ['admin', 'super admin']))) {
            return;
        }

        $identifier = self::getIdentifier($user, $sessionToken);
        $lockKey = "ai_concurrent_lock:{$identifier}";
        
        $activeStreams = (int) Cache::get($lockKey, 1);
        if ($activeStreams <= 1) {
            Cache::forget($lockKey);
        } else {
            Cache::put($lockKey, $activeStreams - 1, now()->addMinutes(2));
        }
    }

    /**
     * Check if user/guest is within daily/session message & token quotas.
     */
    public static function checkCanStream(?User $user, ?string $sessionToken): array
    {
        if ($user && ($user->isSuperAdmin() || in_array(strtolower($user->role), ['admin', 'super admin']))) {
            return ['allowed' => true, 'message' => ''];
        }

        // Check Guest Limits
        if (!$user) {
            $guestLimit = (int) SystemSetting::get('plans_guest_messages_per_session', 5);
            $tokenToken = $sessionToken ?: session()->getId();
            
            // Check cache first for speed, fallback to DB if needed
            $cacheKey = "ai_guest_msg_count:{$tokenToken}";
            $count = Cache::remember($cacheKey, now()->addHours(6), function () use ($tokenToken) {
                $sessionConversations = \App\Models\Conversation::where('session_token', $tokenToken)->pluck('id');
                return Message::whereIn('conversation_id', $sessionConversations)
                    ->where('role', 'user')
                    ->count();
            });

            if ($count >= $guestLimit) {
                return [
                    'allowed' => false,
                    'message' => "⚠️ **Guest limit reached!** You have sent {$guestLimit} free messages. Please [Register](/register) or [Login](/login) to continue chatting."
                ];
            }

            return ['allowed' => true, 'message' => ''];
        }

        // Check Free User Daily Limit
        if (strtolower($user->role) === 'user') {
            $freeLimit = (int) SystemSetting::get('plans_free_message_limit', 50);
            if ($freeLimit > 0) {
                $cacheKey = "ai_user_daily_msg_count:{$user->id}:" . now()->format('Y-m-d');
                $count = Cache::remember($cacheKey, now()->endOfDay(), function () use ($user) {
                    return Message::whereHas('conversation', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->where('role', 'user')
                        ->where('created_at', '>=', now()->startOfDay())
                        ->count();
                });

                if ($count >= $freeLimit) {
                    return [
                        'allowed' => false,
                        'message' => "⚠️ **Daily message limit reached!** You have sent {$freeLimit} messages today on your Free plan. Please upgrade to Pro for unlimited usage."
                    ];
                }
            }
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Record usage in cache after user prompt or completion.
     */
    public static function recordUsage(?User $user, ?string $sessionToken, int $tokensUsed = 0): void
    {
        if (!$user) {
            $tokenToken = $sessionToken ?: session()->getId();
            $cacheKey = "ai_guest_msg_count:{$tokenToken}";
            if (Cache::has($cacheKey)) {
                Cache::increment($cacheKey);
            } else {
                Cache::put($cacheKey, 1, now()->addHours(6));
            }
            return;
        }

        if (strtolower($user->role) === 'user') {
            $cacheKey = "ai_user_daily_msg_count:{$user->id}:" . now()->format('Y-m-d');
            if (Cache::has($cacheKey)) {
                Cache::increment($cacheKey);
            } else {
                Cache::put($cacheKey, 1, now()->endOfDay());
            }

            if ($tokensUsed > 0) {
                $tokenCacheKey = "ai_user_daily_token_count:{$user->id}:" . now()->format('Y-m-d');
                if (Cache::has($tokenCacheKey)) {
                    Cache::increment($tokenCacheKey, $tokensUsed);
                } else {
                    Cache::put($tokenCacheKey, $tokensUsed, now()->endOfDay());
                }
            }
        }
    }
}
