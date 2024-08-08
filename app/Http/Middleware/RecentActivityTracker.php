<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecentActivityTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user != null) {
            User::withoutTimestamps(function () use ($user) {
                $user->latest_activity = now();
                $user->saveQuietly();
            });

        }

        return $next($request);
    }
}
