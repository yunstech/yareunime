@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <x-section-title>Film Favorit Saya</x-section-title>

        @if($favorites->isEmpty())
            <p class="text-gray-400">Belum ada film favorit.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($favorites as $fav)
                    <x-movie-card :poster="$fav->movie->poster"
                                  :title="$fav->movie->title"
                                  :genre="$fav->movie->genre" />
                @endforeach
            </div>
        @endif
    </div>
@endsection
