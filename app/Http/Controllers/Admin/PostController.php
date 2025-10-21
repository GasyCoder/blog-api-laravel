<?php
// app/Http/Controllers/Admin/PostController.php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use ApiResponse;

    /**
     * Get posts (filtered by role)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        
        $query = Post::with(['user:id,name', 'category:id,name,slug', 'tags:id,name,slug'])
            ->latest();

        // Writers ne voient que leurs posts
        if ($request->user()->isWriter()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $posts = $query->paginate($perPage);

        return $this->paginated($posts);
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        // Upload image
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }

        $validated['user_id'] = Auth::id();

        if ($validated['status'] === 'published' && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        $post->load(['user:id,name', 'category:id,name,slug', 'tags:id,name,slug']);

        return $this->created($post, 'Post created successfully');
    }

    /**
     * Get a single post
     */
    public function show(Post $post)
    {
        // Writers ne peuvent voir que leurs posts
        if (Auth::user()->isWriter() && $post->user_id !== Auth::id()) {
            return $this->forbidden('You can only view your own posts');
        }

        $post->load(['user:id,name', 'category:id,name,slug', 'tags:id,name,slug']);

        return $this->success($post);
    }

    /**
     * Update a post
     */
    public function update(Request $request, Post $post)
    {
        // Writers ne peuvent modifier que leurs posts
        if (Auth::user()->isWriter() && $post->user_id !== Auth::id()) {
            return $this->forbidden('You can only update your own posts');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'sometimes|required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        // Upload new image
        if ($request->hasFile('featured_image')) {
            // Supprimer l'ancienne image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }

        if (isset($validated['status']) && $validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        $post->load(['user:id,name', 'category:id,name,slug', 'tags:id,name,slug']);

        return $this->success($post, 'Post updated successfully');
    }

    /**
     * Delete a post
     */
    public function destroy(Post $post)
    {
        // Writers ne peuvent supprimer que leurs posts
        if (Auth::user()->isWriter() && $post->user_id !== Auth::id()) {
            return $this->forbidden('You can only delete your own posts');
        }

        // Supprimer l'image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return $this->success(null, 'Post deleted successfully');
    }
}