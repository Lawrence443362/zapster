<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostService
{
    public AudioFileService $audioFileService;
    public function __construct(AudioFileService $audioFileService)
    {
        $this->audioFileService = $audioFileService;
    }

    public function store(User $user, array $validated): Post
    {
        $post = new Post($validated);
        $post->user()->associate($user);

        return DB::transaction(function () use ($post, $validated) {
            $tags = Tag::createAllNewTags($validated["tags"]);
            $post->save();
            $post->attachTags($tags);

            return $post;
        });
    }

    public function update(User $user, Post $post, array $validated): Post
    {
        return DB::transaction(function () use ($post, $validated) {
            $post->update($validated);

            $tags = Tag::createAllNewTags($validated["tags"]);
            $post->attachTags($tags);

            return $post;
        });
    }
}
