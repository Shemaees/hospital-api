<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthTrait;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;


/**
 * @header X-Api-Version v1
 * @group Auth
 *
 * APIs for managing users
 */
class RegisterController extends Controller
{
    use AuthTrait;
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        try {
            if ($this->userRegisterValidator($request->all())) {
                return $this->userRegisterValidator($request->all());
            }
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->longitude = $request->longitude;
            $user->latitude = $request->latitude;
            $user->gender = $request->gender;

            if ($user->save())
            {
                return $this->createCredential($request->only('email', 'password'));
            }
            else
                return $this->returnJsonResponse('there is something wrong.', [],
                    FALSE, 211);
        }
        catch (JWTException $e)
        {
            return $this->returnJsonResponse('there is something wrong. please, try again later', [],
                FALSE, 213);
        }
    }
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function hospitalRegister(Request $request)
    {
        try {
            if ($this->hospitalRegisterValidator($request->all())) {
                return $this->hospitalRegisterValidator($request->all());
            }
            $user = new Hospital();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->longitude = $request->longitude;
            $user->latitude = $request->latitude;
            $user->address = $request->address;
            $user->branch = $request->branch;

            if ($user->save())
            {
                return $this->createCredential($request->only('email', 'password'));
            }
            else
                return $this->returnJsonResponse('there is something wrong.', [],
                    FALSE, 211);
        }
        catch (JWTException $e)
        {
            return $this->returnJsonResponse('there is something wrong. please, try again later', [],
                FALSE, 213);
        }
    }
}
