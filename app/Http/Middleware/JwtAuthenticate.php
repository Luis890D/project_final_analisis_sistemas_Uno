<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $exception) {
            return response()->json([
                'message' => 'Token inválido o expirado.',
            ], 401);
        }

        $user = auth('api')->user();
        $tenant = $request->attributes->get('tenant');

        // Compara el tenant del usuario contra el tenant resuelto (no el header raw)
        // Esto permite que el header sea un UUID o un slug, ya que TenantMiddleware
        // ya resolvió el tenant real y lo guardó en los atributos del request.
        if ($tenant !== null && $user !== null
            && (string) $user->tenant_id !== (string) $tenant->id) {
            return response()->json([
                'message' => 'El tenant indicado no coincide con el usuario del token.',
            ], 403);
        }

        return $next($request);
    }
}
