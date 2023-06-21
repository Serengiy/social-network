<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $name = $this->parent ? $this->parent->user->name : null;
        return[
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'body' => $this->body,
            'date' => $this->date,
            'reply_for_user' => $name,
        ];
    }
}
