@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <x-section-title>{{ $season->movie->title }} — Season {{ $season->season_number }}</x-section-title>

        <p class="text-gray-400 mb-6">{{ $season->description }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($season->episodes as $episode)
                <a href="{{ route('episode.show', $episode->id) }}"
                   class="group bg-gray-800 rounded-xl p-4 hover:bg-gray-700 transition-all duration-300 flex gap-4 items-center">
                    <div class="w-32 h-20 rounded-lg overflow-hidden">
                        <img src="{{ $episode->thumbnail }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-white">{{ $episode->title }}</h4>
                        <p class="text-sm text-gray-400">Durasi: {{ $episode->duration }} menit</p>
                        @if($episode->isWatched)
                            <span class="text-xs text-green-400">✅ Sudah ditonton</span>
                        @else
                            <span class="text-xs text-yellow-400">▶ Belum ditonton</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
