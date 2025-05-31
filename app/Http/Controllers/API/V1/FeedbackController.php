<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\UserNewsFeedback;
use Exception;
use Illuminate\Http\Request;
use Log;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Get 'per_page' from the request or default to 5
            $per_page = $request->has('per_page') ? $request->per_page : 5;
            $serachByFeedback = $request->has('feedback') ? $request->feedback : null;
            $searchByTitle = $request->has('title') ? $request->title : null;

            $feedback = UserNewsFeedback::
                when($serachByFeedback, function ($q) use ($serachByFeedback) {
                    $q->where('feedback', 'like', '%' . $serachByFeedback . '%');
                })
                ->when($searchByTitle, function ($q) use ($searchByTitle) {
                    // Filter feedbacks where related news matches the title
                    $q->whereHas('news', function ($q) use ($searchByTitle) {
                        $q->where('title', 'like', '%' . $searchByTitle . '%');
                    });
                })
                ->with([
                    'news' => function ($q) use ($searchByTitle) {
                        $q->select('id', 'title', 'published_at', 'author', 'image_url', 'category_id')
                            ->when($searchByTitle, function ($q) use ($searchByTitle) {
                                $q->where('title', 'like', '%' . $searchByTitle . '%');
                            })
                            ->with([
                                'category' => function ($q) {
                                    $q->select('id', 'name', 'image');
                                }
                            ]);
                    }
                ])
                ->select('id', 'news_id', 'feedback')
                ->latest()
                ->paginate($per_page);
            return Helper::jsonResponse(true, 'Feedback fetched successfully', 200, $feedback, true);
        } catch (Exception $e) {
            Log::error("FeedbackController::index" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch feedback', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'news_id' => 'required|exists:news,id',
            'feedback' => 'required|in:good,bad,saved',
        ]);

        try {
            $data = UserNewsFeedback::where('news_id', $validatedData['news_id'])->first();

            if ($data) {
                if ($data->feedback == $validatedData['feedback']) {
                    $data->delete();
                    return Helper::jsonResponse(true, 'Feedback deleted successfully', 200);
                } else {
                    $data->update($validatedData);
                    return Helper::jsonResponse(true, 'Feedback updated successfully', 200);
                }
            }

            $feedback = UserNewsFeedback::create($validatedData);
            return Helper::jsonResponse(true, 'Feedback saved successfully', 200, $feedback);
        } catch (Exception $e) {
            Log::error("FeedbackController::store" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to save feedback: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function clearSavesData()
    {
        try {
            UserNewsFeedback::where('feedback', 'saved')->delete();
            return Helper::jsonResponse(true, 'Saves cleared successfully', 200);
        } catch (Exception $e) {
            Log::error("FeedbackController::clearSavesData" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to clear saves: ' . $e->getMessage(), 500);
        }
    }
}
