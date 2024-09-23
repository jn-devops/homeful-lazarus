<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
//use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
//use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
//use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
//use Spatie\MediaLibrary\MediaCollections\File;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;
class Documents extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'file_attachment',
//        'fields',
        'data',
        'company_code',
        'projects',
        'approvers'
    ];

    protected $casts = [
        'file_attachment' => 'array',
//        'fields' => 'array',
        'data' => 'array',
        'company_code' => 'array',
        'projects' => 'array',
        'approvers' => 'array',
    ];

//    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(Companies::class, 'code','company_code');
//    }
}
