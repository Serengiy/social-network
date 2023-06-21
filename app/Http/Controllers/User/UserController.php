<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StatRequest;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use App\Models\Comment;
use App\Models\LikedPost;
use App\Models\Post;
use App\Models\SubscriberFollowing;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::whereNot('id', auth()->id())->get();
        $followingIds = SubscriberFollowing::where('subscriber_id', auth()->id())->get('following_id')
            ->pluck('following_id')->toArray();
        foreach ($users as $user){
            if(in_array($user->id, $followingIds)){
                $user->is_followed = true;
            };
        }
        return UserResource::collection($users);
    }

    public function post(User $user)
    {
        $posts = $user->posts;
        $this->isLiked($posts);
        return PostResource::collection($posts);
    }

    public function toggleFollowing(User $user)
    {
        $res = auth()->user()->followings()->toggle($user->id);
        $data['is_followed'] = count($res['attached']) > 0;
        return $data;
    }

    public function followingPost()
    {
        $followedIds = auth()->user()->followings()->get()->pluck('id')->toArray();
        $likedPostsIds = LikedPost::where('user_id', auth()->id())->get()->pluck('post_id')->toArray();
        $posts =  Post::whereIn('user_id', $followedIds)
            ->whereNotIn('id', $likedPostsIds)->latest()->get();

        return PostResource::collection($posts);
    }

//    Check if posts are liked
    public function isLiked($posts)
    {
        $likedPostsIds = LikedPost::where('user_id', auth()->id())->get()->pluck('post_id')->toArray();
        foreach ($posts as $post){
            if(in_array($post->id, $likedPostsIds)){
                $post->is_liked = true;
            }
        }
        return $posts;
    }

    public function stat(StatRequest $request)
    {
        $data = $request->validated();


        $response = [];
        $response['user_id'] = $data['user_id'] ?? auth()->id();
        $postsIds = User::find($response['user_id'])->posts()->get('id')->pluck('id')->toArray();

        $response['postsIds'] = $postsIds;
        $response['posts_count'] = count($postsIds);
        $response['likes_count'] = LikedPost::whereIn('post_id', $postsIds)->get()->count();
        $response['comments_count'] = Comment::whereIn('post_id', $postsIds)->get()->count();
        $response['followers_count'] = SubscriberFollowing::where('following_id', $response['user_id'])->get()->count();


        return response()->json(['data'=> $response]);

    }
}
