<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Post;
use App\Topic;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(StorePostRequest $request, Topic $topic)
    {
        $post = new Post();
        $post->body = $request->body;
        $post->user()->associate($request->user());
        $topic->posts()->save($post);


        return fractal()
            ->item($post)
            ->parseIncludes(['user'])
            ->transformWith(new PostTransformer())
            ->toArray();
    }
}
