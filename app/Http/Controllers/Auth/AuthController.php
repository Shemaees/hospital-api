<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthTrait;
use Illuminate\Http\JsonResponse;
use PHPUnit\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @header X-Api-Version v1
 * @group Auth
 *
 * APIs for managing users
 */
class AuthController extends Controller
{
    use AuthTrait;

    /**
     * @return JsonResponse
     */
    public function profile()
    {
        try {
            return $this->returnJsonResponse('success', $this->guard()->user());
        }
        catch (Exception | JWTException $e)
        {
            return $this->returnJsonResponse('there is something wrong. please, try again later' .$e, [],
                FALSE, 213);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            $this->guard()->logout();
            return $this->returnJsonResponse('Successfully logged out');
        }
        catch (Exception | JWTException $e)
        {
            return $this->returnJsonResponse('there is something wrong. please, try again later' .$e,
                [], FALSE, 213);
        }
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        try {
            return $this->createNewToken($this->guard()->refresh());
        }
        catch (JWTException $e)
        {
            return $this->returnJsonResponse('there is something wrong. please, try again later',
                [], FALSE, 213);
        }
    }
}
