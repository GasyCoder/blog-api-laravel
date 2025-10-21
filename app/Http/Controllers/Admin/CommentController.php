<?php
// app/Http/Controllers/Admin/CommentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ApiResponse;

    /**
     * Get comments for moderation
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status', 'pending');
        
        $query = Comment::with(['user:id,name,avatar', 'post:id,title,slug'])
            ->latest();

        // Writers ne voient que les commentaires de leurs posts
        if ($request->user()->isWriter()) {
            $query->whereHas('post', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $comments = $query->paginate($perPage);

        return $this->paginated($comments);
    }

    /**
     * Approve a comment
     */
    public function approve(Comment $comment)
    {
        // Writers ne peuvent approuver que les commentaires de leurs posts
        if (auth()->user()->isWriter() && $comment->post->user_id !== auth()->id()) {
            return $this->forbidden('You can only approve comments on your own posts');
        }

        $comment->approve(auth()->id());

        return $this->success($comment, 'Comment approved successfully');
    }

    /**
     * Reject a comment
     */
    public function reject(Comment $comment)
    {
        // Writers ne peuvent rejeter que les commentaires de leurs posts
        if (auth()->user()->isWriter() && $comment->post->user_id !== auth()->id()) {
            return $this->forbidden('You can only reject comments on your own posts');
        }

        $comment->reject();

        return $this->success($comment, 'Comment rejected successfully');
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Writers ne peuvent supprimer que les commentaires de leurs posts
        if (auth()->user()->isWriter() && $comment->post->user_id !== auth()->id()) {
            return $this->forbidden('You can only delete comments on your own posts');
        }

        $comment->delete();

        return $this->success(null, 'Comment deleted successfully');
    }
}