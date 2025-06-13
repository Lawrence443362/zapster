<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\QueryFilters\PostFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Post::with(["user:id,name"]);
        $posts = QueryBuilder::for($query)
            ->allowedSorts(["id", "title", "created_at"])
            ->allowedFilters(PostFilter::filters())
            ->paginate(15);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post =
            $request
                ->user()
                ->posts()
                ->create($request->validated());

        return new PostResource($post->load('user:id,name'));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $post = Post::with("user:id,name")->findOrFail( $id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return new PostResource($post);
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
