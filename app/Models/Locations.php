<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_locations', 'location_code', 'user_id', 'code', 'id');
    }
}
