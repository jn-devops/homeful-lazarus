<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhilippineRegion;
use App\Models\PhilippineProvince;
use App\Models\PhilippineBarangay;
class PhilippineCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'psgc_code',
        'city_municipality_description',
        'region_description',
        'province_code',
        'city_municipality_code',
        ];
    // A city belongs to a province
    public function province()
    {
        return $this->belongsTo(PhilippineProvince::class, 'province_code', 'province_code');
    }

    // A city belongs to a region
    public function region()
    {
        return $this->belongsTo(PhilippineRegion::class, 'region_code', 'region_code');
    }

    // A city has many barangays
    public function barangays()
    {
        return $this->hasMany(PhilippineBarangay::class, 'city_municipality_code', 'city_municipality_code');
    }
}
