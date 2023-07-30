<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->header('access_token');
        $userId      = $request->header('user_id');

        if (is_null($accessToken) || is_null($userId)) {
            return response('', Response::HTTP_FORBIDDEN);
        }

        $user = User::find($userId);
        if ($accessToken !== $user->access_token) {
            return response('', Response::HTTP_FORBIDDEN);
        }

        $now = Carbon::now();
        $expire = new Carbon($user->access_token_expire);
        if ($now->gt($expire)) {
            return response('', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
