<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the webhook secret from environment
        $webhookSecret = config('app.webhook_secret');

        if (!$webhookSecret) {
            \Log::warning('Webhook secret not configured');
            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        // Check for the authorization header
        $authHeader = $request->header('Authorization');
        $providedSecret = $request->header('X-Webhook-Secret');

        // Support both Authorization: Bearer token and X-Webhook-Secret header
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $providedSecret = substr($authHeader, 7);
        }

        if (!$providedSecret) {
            \Log::warning('Webhook auth failed: No secret provided', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Use hash_equals for secure comparison
        if (!hash_equals($webhookSecret, $providedSecret)) {
            \Log::warning('Webhook auth failed: Invalid secret', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info('Webhook authenticated successfully', [
            'ip' => $request->ip()
        ]);

        return $next($request);
    }
}
