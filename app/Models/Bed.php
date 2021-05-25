<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'category_id', 'type_id', 'hospital_id', 'status', 'day_cost'
    ];


    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function userReserve()
    {
        return $this->hasMany(Reservation::class)->where('user_id', auth('api')->id())
            ->whereDate('start_at', '>' , Carbon::today());
    }
}
