<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhilippineProvince;
use App\Models\PhilippineCity;
use App\Models\PhilippineBarangay;

class PhilippineRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'psgc_code',
        'region_description',
        'region_code',
        ];
    // A region has many provinces
    public function provinces()
    {
        return $this->hasMany(PhilippineProvince::class, 'region_code', 'region_code');
    }

    // A region has many cities
    public function cities()
    {
        return $this->hasMany(PhilippineCity::class, 'region_code', 'region_code');
    }

    // A region has many barangays
    public function barangays()
    {
        return $this->hasMany(PhilippineBarangay::class, 'region_code', 'region_code');
    }
}
