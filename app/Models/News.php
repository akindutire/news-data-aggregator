<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'url',
        'category',
        'published_at',
        'source',
        'author',
        'content',
        'description',
        'image_url',
        'article_remote_key',
        'article_remote_source',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
