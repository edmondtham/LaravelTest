<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatusEnum;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'author_id', 'status'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    protected $casts = [
        'status' => PostStatusEnum::class,
        'published_at' => 'datetime',
    ];
}
