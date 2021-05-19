<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof TokenInvalidException){
                return response()->json([
                    'message' => 'Token is Invalid',
                    'status'  => FALSE
                ]);
            }else if ($exception instanceof TokenExpiredException){
                return response()->json([
                    'message' => 'Token is Expired',
                    'status'  => FALSE
                ]);
            }else if ($exception instanceof UnauthorizedHttpException || $exception instanceof TokenBlacklistedException){
                return response()->json([
                    'message' => 'The token has been unauthorized or blacklisted',
                    'status'  => FALSE
                ]);
            }else{
                return response()->json([
                    'message' => 'Authorization Token not found' . $exception,
                    'status'  => FALSE
                ]);
            }
        }
        return $next($request);
    }
}
