<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\NewsIndexResource;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Log;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $searchByCategories = $request->has('category_ids') ? $request->category_ids : null;
            $searchByFormDate = $request->has('from_date') ? $request->from_date : null;
            $searchByToDate = $request->has('to_date') ? $request->to_date : null;
            $searchByTitile = $request->has('title') ? $request->title : null;
            // Get 'per_page' from the request or default to 25
            $per_page = $request->has('per_page') ? $request->per_page : 25;
            $news = News::select('id', 'title', 'published_at', 'author', 'image_url', 'category_id')
                ->with([
                    'category' => function ($q) {
                        $q->select('id','name', 'image');
                    }
                ])
                ->when($searchByCategories, function ($q) use ($searchByCategories) {
                    $q->whereIn('category_id', $searchByCategories);
                })
                ->when($searchByFormDate, function ($q) use ($searchByFormDate) {
                    $q->whereDate('published_at', '>=', $searchByFormDate);
                })
                ->when($searchByToDate, function ($q) use ($searchByToDate) {
                    $q->whereDate('published_at', '<=', $searchByToDate);
                })
                ->when($searchByTitile, function ($q) use ($searchByTitile) {
                    $q->where('title', 'like', '%' . $searchByTitile . '%');
                })
                ->orderBy('published_at', 'desc')
                ->paginate($per_page);
            return Helper::jsonResponse(true, 'News fetched successfully', 200, NewsIndexResource::collection($news), true);
        } catch (Exception $e) {
            Log::error("NewsController::index" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch news', 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $news = News::findOrFail($id);
            return Helper::jsonResponse(true, 'News fetched successfully', 200, $news);
        } catch (Exception $e) {
            Log::error("NewsController::show" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch news', 500);
        }
    }

}
