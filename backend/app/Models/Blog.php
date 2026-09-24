<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'blog_category_id', 'title', 'slug',
        'excerpt', 'body', 'featured_image', 'status',
        'views_count', 'published_at', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }
}
