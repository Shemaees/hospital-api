<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function hospitals()
    {
        return $this->belongsToMany(Hospital::class);
    }
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}
