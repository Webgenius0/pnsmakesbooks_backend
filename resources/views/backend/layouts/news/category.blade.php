@extends('backend.app')
@section('title', 'News: ' . $category->name)

@push('styles')
    <style>
        .news-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden card bg-white p-4">

        <h2 class="text-xl font-semibold mb-4">Category: {{ $category->name }}</h2>
        <img src="{{ asset($category->image) ?? '' }}" alt="" class="w-full my-4" style="height: 200px;width: 200px;">
        @forelse($category->news as $item)
            <div class="news-card">
                {{-- <a href="{{ $item->url }}" target="_blank" class="text-blue-600 hover:underline font-bold">
                    {{ $item->title }}
                </a> --}}
                <h2 class="mt-2">{{ strip_tags($item->title) }}</h2>
                <p class="text-sm text-gray-600 mt-1">published: {{ $item->published_at->format('d-M-Y g:i A') }}</p>
                <p class="mt-2">{{ strip_tags($item->summary) }}</p>
                @if ($item->image_url)
                    <img src="{{ $item->image_url }}" alt="" class="w-full my-4"
                        style="height: 200px;width: 200px;">
                @endif

                @if ($item->content)
                    <a href="{{ route('news.show', $item->id) }}" class="text-blue-500 mt-2 inline-block">Read More</a>
                @endif

            </div>
        @empty
            <p>No news found for this category.</p>
        @endforelse

    </div>
@endsection
