<?php

namespace App\Http\Controllers\Post;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Requests\Post\ShowPostRequest;
use App\Http\Requests\Post\IndexPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{
    public function index(IndexPostRequest $request)
    {
        return PostResource::collection(Post::all());
    }

    public function store(StorePostRequest $request)
    {
        $model = Post::create($request->validated());
        return new PostResource($model);
    }

    public function show(ShowPostRequest $request,Post $post)
    {
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return new PostResource($post);
    }

    public function destroy(DeletePostRequest $request,Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
