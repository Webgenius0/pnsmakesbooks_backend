<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\NewsIndexResource;
use App\Http\Resources\API\V1\NewsSingleResource;
use App\Models\News;
use Carbon\Carbon;
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
        $vatidateData = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'category_ids' => 'nullable|array',
            'title' => 'nullable|string',
            'news_type' => 'nullable|in:newsfeed,trending,headline',
        ]);
        try {

            $searchByCategories = $request->has('category_ids') ? $request->category_ids : null;
            $searchByFromDate = $request->has('from_date') ? Carbon::parse($request->from_date)->format('Y-m-d') : null;
            $searchByToDate = $request->has('to_date') ? Carbon::parse($request->to_date)->format('Y-m-d') : null;
            $searchByTitle = $request->has('title') ? $request->title : null;
            $searchByNewsType = $request->has('news_type') ? $request->news_type : null;
            $per_page = $request->get('per_page', 5); // cleaner fallback

            $news = News::select('id', 'title', 'published_at', 'author', 'image_url', 'category_id')
                ->with([
                    'category' => function ($q) {
                        $q->select('id', 'name', 'image');
                    }
                ])
                ->when($searchByCategories, function ($q) use ($searchByCategories) {
                    $q->whereIn('category_id', $searchByCategories);
                })
                ->when($searchByFromDate, function ($q) use ($searchByFromDate) {
                    $q->whereDate('published_at', '>=', $searchByFromDate);
                })
                ->when($searchByToDate, function ($q) use ($searchByToDate) {
                    $q->whereDate('published_at', '<=', $searchByToDate);
                })
                ->when($searchByTitle, function ($q) use ($searchByTitle) {
                    $q->where('title', 'like', '%' . $searchByTitle . '%');
                })
                ->when($searchByNewsType, function ($q) use ($searchByNewsType) {
                    $q->where('news_type', $searchByNewsType);
                })
                ->orderBy('published_at', 'desc')
                ->paginate($per_page);

            return Helper::jsonResponse(true, 'News fetched successfully', 200, NewsIndexResource::collection($news), true);
        } catch (Exception $e) {
            Log::error("NewsController::index - " . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch news', 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $news = News::with('userFeedback:id,news_id,feedback', 'category:id,name,image')
                ->select('id', 'title', 'url', 'summary', 'published_at', 'image_url', 'author', 'category_id', 'content', 'news_type')
                ->find($id);
            return Helper::jsonResponse(true, 'News fetched successfully', 200, NewsSingleResource::make($news));
        } catch (Exception $e) {
            Log::error("NewsController::show" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch news', 500);
        }
    }

}
