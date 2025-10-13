@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">{{ $playlist->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $playlist->movies_count }} film</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($playlist->movies as $movie)
                <x-movie-card :poster="$movie->poster" :title="$movie->title" :genre="$movie->genre" />
            @endforeach
        </div>

        <x-comment-section
            :comments="$comments"
            :targetType="App\Models\Playlist::class"
            :targetId="$playlist->id" />

    </div>
@endsection
