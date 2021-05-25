<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BedRecourse;
use App\Http\Resources\CategoryRecourse;
use App\Http\Resources\HospitalResource;
use App\Http\Resources\ReservationRecourse;
use App\Models\Bed;
use App\Models\Category;
use App\Models\Hospital;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AppController extends Controller
{
    public function categories(Request $request)
    {
        try {
            return $this->returnJsonResponse('success', CategoryRecourse::collection(Category::paginate(10)));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
    public function hospitals(Category $category, Request $request)
    {
        try {
            $hospitals = Hospital::whereHas('categories',function($q) use ($category){
                return $q->where('category_id', $category->id);
            })->when(request('search') , function ($query) use($category) {
                return $query->where('name', 'like', '%'.request('search').'%');
            })->when(\request('distance') === true , function ($q){
                return $q->orderByDistance();
            })->when(\request('cost') === true , function ($q){
                return $q->orderByCost();
            });
            return $this->returnJsonResponse('success', HospitalResource::collection($hospitals->paginate()));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
    public function beds(Category $category, Hospital $hospital)
    {
        try {
            return $this->returnJsonResponse('success',
                BedRecourse::collection(Bed::where(['category_id'=> $category->id, 'hospital_id'=>$hospital->id])
                    ->active()->get()));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
    public function reserve(Bed $bed)
    {
        try {
            if ($bed->userReserve->count() > 0)
                return $this->returnJsonResponse('Already you have reserved this bed !',[],FALSE, 422);
            elseif ($bed->status !== 'Active')
                return $this->returnJsonResponse('This bed can not be reserved!',[],FALSE, 422);
            else
            {
                $reservation = new Reservation();
                $reservation->bed_id = $bed->id;
                $reservation->user_id = auth('api')->id();
                $reservation->start_at =Carbon::create(\request('start_at'))->toDateTimeString();
                if ($reservation->save())
                {
                    $bed->status = 'Reserved';
                    if ($bed->update())
                        return $this->returnJsonResponse('bed reserved successfully');
                    else
                        return $this->returnJsonResponse('try again later!',[],FALSE, 413);

                }
                else
                    return $this->returnJsonResponse('There is something wrong!',[],FALSE, 423);
            }
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
    public function upcoming()
    {
        try {
            return $this->returnJsonResponse('success',
                ReservationRecourse::collection(auth('api')->user()->upcoming));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }
    public function history()
    {
        try {
            return $this->returnJsonResponse('success',
                ReservationRecourse::collection(auth('api')->user()->history));
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

}
