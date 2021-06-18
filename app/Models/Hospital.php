<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Hospital extends Authenticatable  implements JWTSubject
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'email',
        'longitude',
        'latitude',
        'branch',
        'address'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function scopeOrderByDistance($query, $latitude , $longitude)
    {
//        $sqlDistance = DB::raw('
//        ( 111.045 * acos( cos( radians(?) )
//        * cos( radians( addresses.latitude ) )
//        * cos( radians( addresses.longitude )
//        - radians(' . $data['lng']  . ') )
//        + sin( radians(' . $data['lat']  . ') )
//        * sin( radians( addresses.latitude ) ) ) )');

        return $query->selectRaw("*,
            (111.045 * acos( cos( radians(?) )
            * cos( radians( latitude ) )
            * cos( radians( longitude )
            - radians(?) )
            + sin( radians(?) )
            * sin( radians( latitude ))))
             AS distance", [$latitude, $longitude, $latitude])
            ->orderBy("distance",'asc');
//        return $query->selectRaw("*,
//            ( 6371000 * acos( cos( radians(?) ) *
//                cos( radians( latitude ) )
//                * cos( radians( longitude ) - radians(?)
//                ) + sin( radians(?) ) *
//                sin( radians( latitude ) ) )
//            ) AS distance", [$latitude, $longitude, $latitude])
//            ->orderBy("distance",'asc');
    }

    public function scopeOrderByCost($query)
    {
        return $query->whereHas('beds', function ($q){
            return $q->selectRaw("day_cost as cost");
        });
    }
}
