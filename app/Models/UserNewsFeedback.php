<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNewsFeedback extends Model
{
    protected $table = 'user_news_feedback';
    protected $fillable = ['user_id', 'news_id', 'feedback'];
    protected $casts = [
        'user_id' => 'integer',
        'news_id' => 'integer',
        'feedback' => 'string'
    ];
    public function feedbackUsers()
    {
        return $this->belongsToMany(User::class, 'user_news_feedback')
            ->withPivot('feedback')
            ->withTimestamps();
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
