<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = isset($this->image) ? $this->image->url : null;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content'=> $this->content,
            'image' => $url,
            'user' => new UserResource($this->user),
            'date' => $this->date,
            'likes_count' => $this->likes()->count(),
            'is_liked' => $this->is_liked ?? false,
            'comments_count' => $this->comments_count,
            'reposted_posts' => new RepostedPostResource($this->repostedPost)
        ];
    }
}
