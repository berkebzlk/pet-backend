<?php

namespace App\Modules\Auth\Middleware;

use App\Modules\Auth\Exceptions\ExpiredAccessTokenException;
use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Token as PassportToken;
use Carbon\Carbon;
use Exception;

class CheckExpiredAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $bearer = (string) $request->bearerToken();
        if (!$bearer) {
            return $next($request);
        }

        $parts = explode('.', $bearer);
        if (count($parts) !== 3) {
            return $next($request);
        }

        $payloadB64 = strtr($parts[1], '-_', '+/');
        $payloadB64 .= str_repeat('=', (4 - strlen($payloadB64) % 4) % 4);

        $payloadJson = base64_decode($payloadB64, true);
        if (!$payloadJson) {
            return $next($request);
        }

        $payload = json_decode($payloadJson, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($payload['exp'])) {
            if ((int) $payload['exp'] <= time()) {
                throw new ExpiredAccessTokenException('Access token has expired.');
            }
        }

        // check DB via jti if available (Passport stores token with id == jti)
        if (json_last_error() === JSON_ERROR_NONE && isset($payload['jti'])) {
            $token = PassportToken::where('id', (string) $payload['jti'])->first();
            if ($token && $token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
                throw new ExpiredAccessTokenException('Access token has expired.');
            }
        }

        return $next($request);
    }
}
