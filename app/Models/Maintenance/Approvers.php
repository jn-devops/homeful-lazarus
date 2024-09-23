<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approvers extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'position',
        'id_type',
        'id_number',
        'issued_on',
        'issued_date',
        'valid_until',
        ];

    protected $casts = [
        'issued_date' => 'date',
        'valid_until' => 'date',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
