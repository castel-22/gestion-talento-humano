<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class PdfReport extends Model
{
    protected $fillable = [
        'uuid',
        'type',
        'parameters',
        'status',
        'file_path',
        'error_message'
    ];

    protected $casts = [
        'parameters' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
