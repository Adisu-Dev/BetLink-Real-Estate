<?php

namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $blogs = Blog::published()
            ->with(['author:id,name,avatar', 'blogCategory:id,name,slug'])
            ->when($request->category, fn($q, $v) => $q->whereHas('blogCategory', fn($q) => $q->where('slug', $v)))
            ->when($request->q, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->latest('published_at')
            ->paginate($request->per_page ?? 12);

        return $this->paginated($blogs);
    }

    public function show(string $slug): JsonResponse
    {
        $blog = Blog::published()
            ->with(['author:id,name,avatar', 'blogCategory'])
            ->where('slug', $slug)
            ->firstOrFail();

        $blog->increment('views_count');

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('blog_category_id', $blog->blog_category_id)
            ->with('author:id,name,avatar')
            ->latest('published_at')
            ->take(3)
            ->get();

        return $this->success([
            'blog'    => $blog,
            'related' => $related,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->get();
        return $this->success($categories);
    }
}
