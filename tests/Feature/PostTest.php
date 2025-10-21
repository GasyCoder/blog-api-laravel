<?php
// tests/Feature/PostTest.php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_published_posts(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        Post::factory()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
                'links',
            ]);
    }

    public function test_can_get_single_post_by_slug(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'slug' => 'test-post',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/posts/test-post');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'author',
                    'category',
                ],
            ]);
    }

    public function test_writer_can_create_post(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $category = Category::factory()->create();
        $token = $writer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/admin/posts', [
                'title' => 'Test Post',
                'content' => 'Test content for the post.',
                'category_id' => $category->id,
                'status' => 'published',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'user_id' => $writer->id,
        ]);
    }

    public function test_regular_user_cannot_create_post(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/admin/posts', [
                'title' => 'Test Post',
                'content' => 'Test content.',
                'category_id' => $category->id,
                'status' => 'published',
            ]);

        $response->assertStatus(403);
    }

    public function test_writer_can_only_update_own_posts(): void
    {
        $writer1 = User::factory()->create(['role' => 'writer']);
        $writer2 = User::factory()->create(['role' => 'writer']);
        $category = Category::factory()->create();
        
        $post = Post::factory()->create([
            'user_id' => $writer1->id,
            'category_id' => $category->id,
        ]);

        $token = $writer2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/admin/posts/' . $post->id, [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(403);
    }
}