<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AttachAudioToPostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\QueryFilters\PostFilter;
use App\Services\PostService;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    public PostService $service;
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $per_page = request('per_page', 15);
        $query = Post::with(["tags", "user:id,name", "audio"]);
        $posts = QueryBuilder::for($query)
            ->allowedSorts(["id", "title", "created_at"])
            ->allowedFilters(PostFilter::filters())
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->service->store($request->user(), $request->validated());

        return new PostResource($post->load(['user:id,name', 'tags']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function attachAudio(AttachAudioToPostRequest $request, string $id)
    {
        $post = Post::with(['user:id,name', 'tags:id,name', 'audio'])->find($id);

        $this->service->attachAudio($post, $request->file('audio'));

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $post = Post::with(['user:id,name', 'tags:id,name', 'audio'])->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = $this->service->update($request->user(), $post, $request->validated());

        return new PostResource($post->load(['user:id,name', 'tags', 'audio']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeletePostRequest $request, Post $post)
    {
        $post->delete();

        return response()->json([
            "message" => "Post removed"
        ]);
    }
}
