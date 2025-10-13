@extends('layouts.app')

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[60vh] bg-cover bg-center" style="background-image: url('{{ $featured->background ?? $featured->poster ?? '/placeholder.jpg' }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f0f0f] via-black/50 to-transparent"></div>
        <div class="absolute bottom-10 left-10 max-w-xl">
            <h1 class="text-4xl font-extrabold text-white drop-shadow-lg">{{ $featured->title }}</h1>
            <p class="text-gray-300 mt-3 line-clamp-3">{{ $featured->description }}</p>
            <a href="{{ route('movie.show', $featured->slug) }}" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg shadow-lg">Watch Now</a>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-6">
        {{-- Ongoing Anime --}}
        <x-section-title title="Ongoing Anime" />
        <div class="flex space-x-4 overflow-x-auto pb-4">
            @foreach($ongoing as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>

        {{-- Popular This Week --}}
        <x-section-title title="Top This Week" />
        <div class="flex space-x-4 overflow-x-auto pb-4">
            @foreach($weeklyTop as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>

        {{-- All Time Favorites --}}
        <x-section-title title="All-Time Favorites" />
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-5 pb-10">
            @foreach($topAllTime as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>
    </div>
@endsection
