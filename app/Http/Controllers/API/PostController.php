<?php
// app/Http/Controllers/API/PostController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;

    /**
     * Get all published posts
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $category = $request->input('category');
        $tag = $request->input('tag');

        $query = Post::with(['user:id,name,avatar', 'category:id,name,slug,color', 'tags:id,name,slug'])
            ->published()
            ->latest('published_at');

        if ($search) {
            $query->search($search);
        }

        if ($category) {
            $query->byCategory($category);
        }

        if ($tag) {
            $query->byTag($tag);
        }

        $posts = $query->paginate($perPage);

        return $this->paginated($posts);
    }

    /**
     * Get a single post by slug
     */
    public function show(string $slug)
    {
        $post = Post::with([
            'user:id,name,avatar,bio',
            'category:id,name,slug,color',
            'tags:id,name,slug',
            'approvedComments' => function ($query) {
                $query->with(['user:id,name,avatar', 'replies' => function ($q) {
                    $q->approved()->with('user:id,name,avatar')->latest();
                }])
                ->topLevel()
                ->latest();
            }
        ])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

        // Increment views
        $post->incrementViews();

        return $this->success([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'featured_image' => $post->featured_image,
            'published_at' => $post->published_at,
            'reading_time' => $post->reading_time,
            'views_count' => $post->views_count,
            'meta' => $post->meta,
            'author' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'avatar' => $post->user->avatar,
                'bio' => $post->user->bio,
            ],
            'category' => [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
                'color' => $post->category->color,
            ],
            'tags' => $post->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }),
            'comments' => $post->approvedComments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->avatar,
                    ],
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'created_at' => $reply->created_at,
                            'user' => [
                                'id' => $reply->user->id,
                                'name' => $reply->user->name,
                                'avatar' => $reply->user->avatar,
                            ],
                        ];
                    }),
                ];
            }),
            'comments_count' => $post->approvedComments->count(),
        ]);
    }
}