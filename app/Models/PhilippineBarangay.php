<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhilippineRegion;
use App\Models\PhilippineProvince;
use App\Models\PhilippineCity;
class PhilippineBarangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_code',
        'barangay_description',
        'region_code',
        'province_code',
        'city_municipality_code',
        ];
    // A barangay belongs to a city
    public function city()
    {
        return $this->belongsTo(PhilippineCity::class, 'city_municipality_code', 'city_municipality_code');
    }

    // A barangay belongs to a province
    public function province()
    {
        return $this->belongsTo(PhilippineProvince::class, 'province_code', 'province_code');
    }

    // A barangay belongs to a region
    public function region()
    {
        return $this->belongsTo(PhilippineRegion::class, 'region_code', 'region_code');
    }
}
