<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;
class NewsController extends Controller
{


    public function index()
    {
        $categories = Category::all();
        $news = News::latest()->paginate(10);
        // dd($news, $categories);
        return view('backend.layouts.news.index', compact('categories', 'news'));
    }
    public function show(int $id)
    {
        $news = News::findOrFail($id);
        // dd($news->toArray());
        return view('backend.layouts.news.show', compact( 'news'));
    }

    public function category(int $categoryId)
    {
        $category = Category::where('id', $categoryId)
        ->with('news',function($q){
            $q->orderBy('published_at', 'desc');
        })
        ->firstOrFail();
        return view('backend.layouts.news.category', compact('category'));
    }

}
