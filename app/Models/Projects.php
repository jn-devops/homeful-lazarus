<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Projects extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'description','active'];
    protected $casts = [
        'active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_projects', 'project_code', 'user_id', 'code', 'id');
    }
}
