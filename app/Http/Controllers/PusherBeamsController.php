<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\PushNotifications\PushNotifications;

class PusherBeamsController extends Controller
{
    protected $beamsClient;

    public function __construct()
    {
        $this->beamsClient = new PushNotifications([
            'instanceId' => config('services.pusher.beams_instance_id'),
            'secretKey' => config('services.pusher.beams_secret_key'),
        ]);
    }

    public function beamsAuth(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $requestedUserId = $request->query('user_id') ?? $request->input('user_id');

            // Verify that the requested user ID matches the authenticated user
            if ($requestedUserId != $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $beamsToken = $this->beamsClient->generateToken($user->id);
            return response()->json($beamsToken);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate auth token: ' . $e->getMessage()
            ], 500);
        }
    }


    public function storeSubscription(Request $request)
    {
        $user = $request->user();

        try {
            // Create or update the push subscription record
            $subscription = $user->pushSubscriptions()->updateOrCreate(
                [
                    'endpoint' => $request->input('endpoint', 'pusher-beams')
                ],
                [
                    'public_key' => $request->input('public_key', 'pusher-beams'),
                    'auth_token' => $request->input('auth_token', 'pusher-beams'),
                    'content_encoding' => $request->input('content_encoding', 'aesgcm'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription stored successfully',
                'subscription_id' => $subscription->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkSubscription(Request $request)
    {
        $user = $request->user();
        $hasSubscription = $user->pushSubscriptions()->exists();

        return response()->json([
            'has_subscription' => $hasSubscription,
            'subscription_count' => $user->pushSubscriptions()->count()
        ]);
    }
}
