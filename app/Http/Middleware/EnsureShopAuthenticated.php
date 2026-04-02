<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use App\Traits\ApiResponse;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EnsureShopAuthenticated
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $payload = JWT::decode($token, new Key(env('SHOPIFY_CLIENT_SECRET'), 'HS256'));
        $shopDomain = parse_url($payload->dest, PHP_URL_HOST);
        $user = Cache::remember($shopDomain, 60,  function () use ($shopDomain) {
            return Shop::where('domain', $shopDomain)->first();
        });
        if ($user === null) {
            return $this->error('Unauthorized', 401);
        }
        Auth::guard('shop')->login($user);

        return $next($request);
    }
}
