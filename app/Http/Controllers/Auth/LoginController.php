<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;


/**
 * @header X-Api-Version v1
 * @group Auth
 *
 * APIs for managing users
 */
class LoginController extends Controller
{
    use AuthTrait;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            if ($this->loginValidator($request->all())) {
                return $this->loginValidator($request->all());
            }
            return $this->createCredential($request->only('email', 'password'));

        }
        catch (Exception | JWTException $e)
        {
            return $this->returnJsonResponse('هناك خطأ ما'. $e, [],
                FALSE, 213);
        }
    }
    public function getLogin(Request $request)
    {
        return $this->returnJsonResponse('الرمز غير صحيح', [],
            FALSE, 213);
    }
}
