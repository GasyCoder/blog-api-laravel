<?php
// app/Http/Controllers/API/CommentController.php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\Comment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponse;

    /**
     * Store a new comment
     */
    public function store(Request $request, Post $post)
    {
        if ($post->status !== 'published') {
            return $this->forbidden('Cannot comment on unpublished posts');
        }

        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Vérifier que le parent_id appartient au même post
        if ($request->parent_id) {
            $parent = Comment::findOrFail($request->parent_id);
            if ($parent->post_id !== $post->id) {
                return $this->error('Parent comment does not belong to this post', 400);
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'status' => 'pending', // En attente de modération
        ]);

        $comment->load('user:id,name,avatar');

        return $this->created([
            'id' => $comment->id,
            'content' => $comment->content,
            'status' => $comment->status,
            'created_at' => $comment->created_at,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'avatar' => $comment->user->avatar,
            ],
        ], 'Comment submitted for moderation');
    }
}