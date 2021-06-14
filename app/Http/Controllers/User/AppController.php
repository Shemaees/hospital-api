<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\BedRecourse;
use App\Http\Resources\User\CategoryRecourse;
use App\Http\Resources\User\HospitalResource;
use App\Http\Resources\User\ReservationRecourse;
use App\Models\Bed;
use App\Models\Category;
use App\Models\Hospital;
use App\Models\Reservation;
use Carbon\Carbon;
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
                return $q->where('category_id', $category->id)->whereHas('beds', function ($q)use ($category){
                    return $q->where('category_id', $category->id);
                });
            })->when(request('search') , function ($query) use($category) {
                return $query->where('name', 'like', '%'.request('search').'%');
            })->when(\request('distance') === true , function ($q){
                $latitude = \request('lat') ?? auth('api')->user()->latitude;
                $longitude = \request('long') ?? auth('api')->user()->longitude;
                return $q->orderByDistance($latitude, $longitude);
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
                return $this->returnJsonResponse('لقد حجزت هذا السرير مسبقا!',[],FALSE, 422);
            elseif ($bed->status !== 'Active')
                return $this->returnJsonResponse('لا يمكن حجز هذا السريرالآن!',[],FALSE, 422);
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
                        return $this->returnJsonResponse('تم الحجز بنجاح');
                    else
                        return $this->returnJsonResponse('يرجى المحاولة مرة أخرى!',[],FALSE, 413);

                }
                else
                    return $this->returnJsonResponse('هناك خطأ ما!',[],FALSE, 423);
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
