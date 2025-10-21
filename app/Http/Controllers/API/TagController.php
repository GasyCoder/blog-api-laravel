<?php 
// app/Http/Controllers/API/TagController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Traits\ApiResponse;

class TagController extends Controller
{
    use ApiResponse;

    /**
     * Get all tags with post counts
     */
    public function index()
    {
        $tags = Tag::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'posts_count' => $tag->posts_count,
                ];
            });

        return $this->success($tags);
    }
}