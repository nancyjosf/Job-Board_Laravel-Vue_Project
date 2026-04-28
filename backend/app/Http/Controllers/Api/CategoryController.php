<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->withCount([
                'jobListings as public_jobs_count' => fn ($q) => $q->public(),
            ])
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
