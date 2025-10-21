<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // TODO: Implement blog listing logic
        // Example: $posts = Post::with('user')->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Blog posts retrieved successfully',
            'data' => [
                'posts' => [
                    // Your posts data here
                ]
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // TODO: Add validation
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'content' => 'required|string',
        // ]);

        // TODO: Create post
        // $post = Post::create([
        //     'user_id' => $request->user()->id,
        //     'title' => $request->title,
        //     'content' => $request->content,
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully',
            'data' => [
                // 'post' => $post
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // TODO: Find and return post
        // $post = Post::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Blog post retrieved successfully',
            'data' => [
                // 'post' => $post
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // TODO: Add validation
        // TODO: Find post and check authorization
        // $post = Post::findOrFail($id);
        // $this->authorize('update', $post);

        // TODO: Update post
        // $post->update($request->only(['title', 'content']));

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully',
            'data' => [
                // 'post' => $post
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // TODO: Find post and check authorization
        // $post = Post::findOrFail($id);
        // $this->authorize('delete', $post);

        // TODO: Delete post
        // $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully'
        ], 200);
    }
}
