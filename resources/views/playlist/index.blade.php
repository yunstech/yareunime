@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <x-section-title>Playlist Saya</x-section-title>

        <div class="flex justify-end mb-6">
            <a href="{{ route('playlist.create') }}" class="bg-pink-600 px-4 py-2 rounded-lg hover:bg-pink-700 transition-all">
                ➕ Buat Playlist Baru
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($playlists as $playlist)
                <x-playlist-card :name="$playlist->name" :count="$playlist->movies_count" />
            @endforeach
        </div>
    </div>
@endsection
