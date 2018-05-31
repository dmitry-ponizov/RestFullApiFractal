<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Post;
use App\Topic;
use App\Transformers\TopicTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::latestFirst()->paginate(3);

        $topicsCollection = $topics->getCollection();

        return fractal()
            ->collection($topicsCollection)
            ->parseIncludes(['user'])
            ->transformWith(new TopicTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($topics))
            ->toArray();
    }

    public function store(StoreTopicRequest $request)
    {
        $topic = new Topic;
        $topic->title = $request->title;
        $topic->user()->associate($request->user());

        $post = new Post;
        $post->body = $request->body;
        $post->user()->associate($request->user());

        $topic->save();
        $topic->posts()->save($post);

        return fractal()
            ->item($topic)
            ->transformWith(new TopicTransformer())
            ->parseIncludes(['user', 'posts', 'posts.user'])
            ->toArray();
    }

    public function show(Topic $topic)
    {
        return fractal()
            ->item($topic)
            ->transformWith(new TopicTransformer())
            ->parseIncludes(['user', 'posts', 'posts.user'])
            ->toArray();
    }

    /**
     * @param UpdateTopicRequest $request
     * @param Topic $topic
     * @return array|string
     */
    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        try {
            $this->authorize('update', $topic);
        } catch (AuthorizationException $e) {
            return $e->getMessage();
        }

        $topic->title = $request->title;

        $topic->save();

        return fractal()
            ->item($topic)
            ->transformWith(new TopicTransformer())
            ->parseIncludes(['user', 'posts', 'posts.user'])
            ->toArray();
    }

    /**
     * @param Topic $topic
     * @return string
     * @throws \Exception
     */
    public function destroy(Topic $topic)
    {
        try {
            $this->authorize('destroy', $topic);
        } catch (AuthorizationException $e) {
            return $e->getMessage();
        }

        $topic->delete();

        return response(null, 204);
    }

    public function reply()
    {

    }
}
