<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Tag;
use App\QueryFilters\PostFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $per_page = request('per_page', 15);
        $query = Post::with(["tags", "user:id,name"]);
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
        $params = $request->validated();
        $user = $request->user();

        return DB::transaction(function () use ($request, $params, $user) {
            $tags = Tag::createAllNewTags($params["tags"]);

            $post = $user
                ->createPost($params)
                ->attachTags($tags);

            if ($request->hasFile("audio")) {
                $file = $request->file("audio");

                $storedName = (string) Str::uuid();
                $folder = 'posts/audio';
                $disk = config('filesystems.default');

                $file->storeAs($folder, $storedName . '.' . $file->extension(), $disk);

                $post->audio()->create([
                    'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'stored_name' => $storedName,
                    'folder' => $folder,
                    'disk' => $disk,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->extension(),
                    'duration' => null, // можно позже заполнить через ffmpeg
                ]);
            }

            return new PostResource($post->load(['user:id,name', 'tags']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $post = Post::with(['user:id,name', 'tags:id,name'])->findOrFail($id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        return DB::transaction(function () use ($request, $post) {
            $params = $request->validated();
            $post->update($params);

            if ($request->has('tags')) {
                $tags = Tag::createAllNewTags($params["tags"]);

                $post->updateTags($tags);
            }

            return new PostResource($post->load('tags'));
        });
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
