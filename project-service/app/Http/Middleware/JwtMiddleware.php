<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Retrieve token from Authorization header or cookie
            $token = $request->bearerToken();
            
            if (!$token) {
                $token = $request->cookie('token');
            }

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Token not provided'
                ], 401);
            }

            // Decode and validate signature
            $payload = JWTAuth::setToken($token)->getPayload();
            
            // Extract custom claims
            $userId = $payload->get('user_id');
            $role = $payload->get('role');

            if (!$userId || !$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid token claims'
                ], 401);
            }

            // Inject claims into request attributes
            $request->attributes->add([
                'user_id' => $userId,
                'role' => $role
            ]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Token expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Token invalid'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Could not parse token'
            ], 401);
        }

        return $next($request);
    }
}
