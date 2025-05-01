<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(Post::all());
    }

    public function store(StorePostRequest $request)
    {
        $model = Post::create($request->validated());
        return new PostResource($model);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
