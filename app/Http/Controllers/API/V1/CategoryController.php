<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CategoryShowResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Get 'per_page' from the request or default to 25
            $per_page = $request->has('per_page') ? $request->per_page : 25;
            $categories = Category::select('id', 'name', 'related_keywords', 'image')
                ->paginate($per_page);
            return Helper::jsonResponse(true, 'Categories fetched successfully', 200, $categories, true);
        } catch (Exception $e) {
            Log::error("CategoryController::index" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch categories', 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::select('id', 'name', 'related_keywords', 'image')
                ->with([
                    'news' => function ($q) {
                        $q->select('id', 'category_id', 'url', 'summary', 'published_at', 'image_url', 'author')
                            ->orderBy('published_at', 'desc');
                    }
                ])->findOrFail($id);
            return Helper::jsonResponse(true, 'Category fetched successfully', 200, new CategoryShowResource($category));
        } catch (Exception $e) {
            Log::error("CategoryController::show" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch category', 500);
        }
    }

}
