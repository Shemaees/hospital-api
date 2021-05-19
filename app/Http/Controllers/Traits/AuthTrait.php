<?php


namespace App\Http\Controllers\Traits;

use App\Http\Resources\HospitalProfileRecourse;
use App\Http\Resources\UserProfileRecourse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait AuthTrait
{

    /**
     * @param $request
     * @param $credentials
     *
     * @return JsonResponse
     */
    protected function createCredential($credentials)
    {
        if (!$token = $this->guard()->attempt((array)$credentials)) {
            return $this->returnJsonResponse('Unauthorized',
                [], FALSE, 209);
        }
        return $this->createNewToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken($token)
    {
        if (request()->has('type') && request('type') == 'user')
            $profile = new UserProfileRecourse($this->guard()->user());
        elseif (request()->has('type') && request('type') == 'hospital')
            $profile = new HospitalProfileRecourse($this->guard()->user());
        return $this->returnJsonResponse('You register successfully',
            [
                "credentials" =>[
                    'access_token'          => $token,
                    'token_type'            => 'bearer',
                    'expires_in'            =>  $this->guard()->factory()->getTTL() * 60*60*24*30
                ],
                "profile" => $profile,
            ],true,202
        );
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard|StatefulGuard
     */
    public function guard()
    {
        if (request()->has('type') && request('type') == 'user')
            return Auth::guard('api');
        elseif (request()->has('type') && request('type') == 'hospital')
            return Auth::guard('hospital');
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function loginValidator($data)
    {
        $validator = Validator::make($data, [
            'email'=>'required|email|',
            'password'=>'required|string|min:5',
        ]);
        if ($validator->fails()) {
            return $this->returnJsonResponse($validator->errors()->first(),
                [], FALSE, 212);
        }
    }


    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function userRegisterValidator($data)
    {
        $validator = Validator::make($data, [
            'email'=>'required|email|unique:users,email|',
            'password' => 'required|string|min:5|max:32',
            'name'=>'required|string|min:3|max:55',
            'phone'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'gender'=>'required|in:male,female',
        ]);
        if ($validator->fails()) {
            return $this->returnJsonResponse($validator->errors()->first(),
                [], FALSE, 212);
        }
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function hospitalRegisterValidator($data)
    {
        $validator = Validator::make($data, [
            'email'=>'required|email|unique:hospitals,email|',
            'password' => 'required|string|min:5|max:32',
            'name'=>'required|string|min:3|max:55',
            'phone'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'branch'=>'string',
            'address'=>'string',
        ]);
        if ($validator->fails()) {
            return $this->returnJsonResponse($validator->errors()->first(),
                [], FALSE, 212);
        }
    }
}
