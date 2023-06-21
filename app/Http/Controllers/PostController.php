<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentRequest;
use App\Http\Requests\Post\RepostRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Comment;
use App\Models\LikedPost;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class PostController extends Controller
{
    public function index(){
        $posts = Post::where('user_id', auth()->id())->latest()->get();
        $this->isLiked($posts);
        return PostResource::collection($posts);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        try{
            DB::beginTransaction();
            $imageId = $data['image_id'];
            unset($data['image_id']);

            $data['user_id'] = auth()->id();
            $post = Post::create($data);
            $this->processImage($post, $imageId);

            PostImage::clearStorage();

            DB::commit();
        } catch (Exception $exception){
            DB::rollBack();
            return response()->json(['error'=>$exception->getMessage()]);
        }
        return new PostResource($post);
    }

    public function repost(RepostRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['reposted_id'] = $post->id;
        $post = Post::create($data);
    }

    public function processImage($post, $imageId){
        if(isset($imageId)){
            $image = PostImage::find($imageId);
            $image->update([
                'post_id' => $post->id
            ]);
        }
    }

    public function toggleLike(Post $post)
    {
        $res = auth()->user()->likedPost()->toggle($post->id);
        $data['is_liked'] = count($res['attached']) > 0;
        $data['likes_count'] = $post->likes()->count();
        return $data;
    }

//    Check if posts are liked
    public function isLiked($posts)
    {
        $is_liked = LikedPost::where('user_id', auth()->id())->get()->pluck('post_id')->toArray();
        foreach ($posts as $post){
            if(in_array($post->id, $is_liked)){
                $post->is_liked = true;
            }
        }
        return $posts;
    }

    public function comment(Post $post, CommentRequest $request)
    {
        $data = $request->validated();
        $data['post_id'] = $post->id;
        $data['user_id'] = auth()->id();
        $comment = Comment::create($data);

        return new CommentResource($comment);
    }

    public function commentList(Post $post)
    {
        $comments = $post->comments()->latest()->get();
        return CommentResource::collection($comments);
    }
}
