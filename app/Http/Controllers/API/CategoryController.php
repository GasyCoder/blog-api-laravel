<?php
// app/Http/Controllers/API/CategoryController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get all active categories
     */
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'color' => $category->color,
                    'posts_count' => $category->posts_count,
                ];
            });

        return $this->success($categories);
    }
}

