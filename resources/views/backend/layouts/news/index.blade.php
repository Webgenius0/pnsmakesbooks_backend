@extends('backend.app')
@section('title', 'All News')

@section('content')
<div class="main-content-container overflow-hidden card bg-white p-4">
    <h2 class="text-xl font-bold mb-4">All News</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($news as $item)
            <div class="border rounded p-4">
                <img src="{{ $item->image_url }}" alt="" class="w-full h-40 object-cover mb-2" style="height: 200px;width: 200px;">
                <h3 class="font-bold text-lg">{{ $item->title }}</h3>
                <p class="text-sm text-gray-600">{{ \Str::limit(strip_tags($item->summary), 100) }}</p>
                <a href="{{ route('news.show', $item->id) }}" class="text-blue-500 mt-2 inline-block">Read More</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
