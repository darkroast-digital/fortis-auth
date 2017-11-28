<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'reset_token',
        'role',
        'avatar',
        'active',
        'phone',
        'position',
    ];

    public function Posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
