<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhilippineRegion;
use App\Models\PhilippineCity;
use App\Models\PhilippineBarangay;
class PhilippineProvince extends Model
{
    use HasFactory;

    protected $fillable = [
        'psgc_code',
        'province_description',
        'region_code',
        'province_code',
        ];
    // A province belongs to a region
    public function region()
    {
        return $this->belongsTo(PhilippineRegion::class, 'region_code', 'region_code');
    }

    // A province has many cities
    public function cities()
    {
        return $this->hasMany(PhilippineCity::class, 'province_code', 'province_code');
    }

    // A province has many barangays
    public function barangays()
    {
        return $this->hasMany(PhilippineBarangay::class, 'province_code', 'province_code');
    }
}
