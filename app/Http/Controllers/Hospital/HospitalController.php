<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Resources\Hospital\BedRecourse;
use App\Http\Resources\Hospital\ReservationRecourse;
use App\Models\Bed;
use App\Models\Category;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class HospitalController extends Controller
{
    public function beds(Category $category)
    {
        try {
            $beds = $category->beds->where('hospital_id', auth('hospital')->id());
            if (auth('hospital')->user()->beds->count()>0)
                return $this->returnJsonResponse('success',
                [
                    "all" => $beds->count(),
                    "active" => $beds->where('status', '=', 'Active')->count(),
                    "reserved" => $beds->where('status', '=', 'Reserved')->count(),
                    "out" => $beds->where('status', '=', 'Out of service')->count(),

                    "beds" => BedRecourse::collection($beds),
                ]);
            else
                return $this->returnJsonResponse('لا يوجد أسرة!',[], false);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

    public function createBed(Category $category, Request $request)
    {
        try {
            $bed = new Bed();
            $bed->category_id = $category->id;
            $bed->hospital_id = auth('hospital')->id();
            $bed->type_id = 1;
            $bed->day_cost = $request->day_cost;
            if ($bed->save())
                return $this->returnJsonResponse('تم الحفظ بنجاح');
            else
                return $this->returnJsonResponse('هناك خطأ ما', [], FALSE, 414);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

    public function editBed(Bed $bed, Request $request)
    {
        try {

            if(isset($request->day_cost)) $bed->day_cost = $request->day_cost;

            if(isset($request->status)) $bed->status = $request->status;

            if ($bed->update())
                return $this->returnJsonResponse('تم التحديث بنجاح');
            else
                return $this->returnJsonResponse('هناك خطأ ما', [], FALSE, 414);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

    public function deleteBed(Bed $bed)
    {
        try {
            if ($bed->reservations->count() > 0)
                return $this->returnJsonResponse(
                    'لا يمكن حذف السرير لوجود '. $bed->reservations->count() .' حجوزات حالية',
                    [], FALSE, 414);
            else
                if ($bed->delete())
                    return $this->returnJsonResponse('تم الحذف بنجاح');
                else
                    return $this->returnJsonResponse('هناك خطأ ما', [], FALSE, 414);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

    public function reservations(Bed $bed)
    {
        try {
            if ($bed->reservations()->count()>0)
                return $this->returnJsonResponse('success',
                    ReservationRecourse::collection($bed->reservations));
            else
                return $this->returnJsonResponse('لا يوجد حجوزات!',[], false);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

    public function endReservation(Bed $bed, Reservation $reservation)
    {
        try {

            $reservation->end_at = Carbon::now()->toDateTimeString();
            if ($reservation->update())
            {
                $bed->status = 'Active';
                if ($bed->update())
                    return $this->returnJsonResponse('تم التحديث');
                else
                    return $this->returnJsonResponse('هناك خطأ ما', [], FALSE, 414);
            }
            else
                return $this->returnJsonResponse('Can not end reservation now please try again later', [], FALSE, 415);
        }
        catch (JWTException $e)
        {
            return response()->json($e);
        }
    }

}
