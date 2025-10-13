@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            {{-- Avatar --}}
            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-pink-600">

            {{-- Info User --}}
            <div>
                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                <p class="text-gray-400">{{ $user->bio }}</p>

                <div class="mt-4 flex flex-wrap gap-4">
                    <div><span class="font-bold">{{ $user->totalWatched }}</span> Film Ditonton</div>
                    <div><span class="font-bold">{{ $user->totalHours }}</span> Jam</div>
                    <div><span class="font-bold">{{ $user->playlists_count }}</span> Playlist</div>
                </div>
            </div>
        </div>

        {{-- Playlist Publik --}}
        <section class="mt-10">
            <x-section-title>Playlist Publik</x-section-title>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($user->playlists as $playlist)
                    <x-playlist-card :name="$playlist->name" :count="$playlist->movies_count" />
                @endforeach
            </div>
        </section>
    </div>
@endsection
