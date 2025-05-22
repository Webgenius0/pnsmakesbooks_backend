<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function markFeedback(Request $request, $newsId)
    {
        $request->validate([
            'feedback' => 'required|in:good,bad,saved',
        ]);

        $user = auth()->user();
        $news = News::findOrFail($newsId);

        $user->feedbackNews()->syncWithoutDetaching([
            $news->id => ['feedback' => $request->feedback],
        ]);

        return response()->json(['message' => 'Feedback saved.']);
    }

    public function goodNewsList()
    {
        $user = auth()->user();
        $newsList = $user->goodNews()->latest('published_at')->get();
        dd($newsList);
        // return view('news.good-list', compact('newsList'));
    }
}
