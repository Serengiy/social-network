<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $guarded = false;
    protected $with = ['image', 'likes', 'user'];
    protected $withCount = ['comments'];

    public function getDateAttribute(){
        return $this->created_at->diffForHumans();
    }

    public function image()
    {
        return$this->hasOne(PostImage::class, 'post_id', 'id')
            ->whereNotNull('post_id');
    }

    public function likes()
    {
        return $this->hasMany(LikedPost::class, 'post_id', 'id');
    }

    public function repostedPost()
    {
        return $this->belongsTo(Post::class, 'reposted_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
