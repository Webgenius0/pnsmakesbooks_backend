<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'url',
        'summary',
        'content',
        'image_url',
        'author',
        'published_at',
        'source_name',
        'source_id',
        'news_type'
    ];
    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function userFeedback()
    {
        return $this->hasMany(UserNewsFeedback::class);
    }
}
