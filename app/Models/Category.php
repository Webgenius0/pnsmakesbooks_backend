<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'related_keywords',
        'image',
        'status'
    ];
    protected $casts = [
        'name' => 'string',
        'related_keywords' => 'array',
        'image' => 'string',
        'status' => 'string',
    ];

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function getImageAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // Check if the request is an API request
        if (request()->is('api/*') && !empty($value)) {
            // Return the full URL for API requests
            return url($value);
        }

        // Return only the path for web requests
        return $value;
    }
}
