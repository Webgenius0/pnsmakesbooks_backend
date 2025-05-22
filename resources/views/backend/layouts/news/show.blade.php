@extends('backend.app')

@section('title', $news->title)

@section('content')
<div class="main-content-container overflow-hidden card bg-white p-6">
    <!-- News Title -->
    <h1 class="text-2xl font-bold mb-2">
        {{ $news->title }}
    </h1>

    <!-- Author and Publish Date -->
    <p class="text-sm text-gray-500 mb-4">
        By {{ $news->author ?? 'Unknown' }} |
        Published: {{ $news->published_at->format('d-M-Y g:i A') }}
    </p>

    <!-- News Image -->
    @if($news->image_url)
        <img src="{{ $news->image_url }}" alt="News Image" class="w-full my-4 object-cover rounded" style="height: 200px; max-width: 100%; width: 200px;">
    @endif

    <!-- News Content -->
    <div class="prose">
        {!! $news->content !!}
    </div>
</div>
@endsection
