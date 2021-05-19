<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\HospitalResource;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AppController extends Controller
{
    public function index(Request $request)
    {
        try {
            $hospitals = Hospital::when(request('search') , function ($query) {
                return $query->where('name', 'like', '%'.request('search').'%')->
                orWhere('email', 'like', '%'.request('search').'%');
            })->when(\request('distance'), function ($q){
                return $q->orderByDistance();
            })->when(\request('cost'), function ($q){
                return $q->whereHas('beds', function ($q){
                    return $q->orderBy("day_cost",'asc');
                });
            })->get();
            return $this->returnJsonResponse('success', HospitalResource::collection($hospitals));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
}
