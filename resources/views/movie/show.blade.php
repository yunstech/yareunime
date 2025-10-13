@extends('layouts.app')

@section('content')
    {{-- Hero / Banner --}}
    <section class="relative h-[70vh] bg-cover bg-center" style="background-image: url('{{ $movie->background ?? $movie->poster }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f0f0f] via-black/60 to-transparent"></div>
        <div class="absolute bottom-12 left-10 max-w-xl">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg">{{ $movie->title }}</h1>
            <div class="flex flex-wrap gap-2 mt-3">
                @foreach($movie->genres ?? [] as $genre)
                    <span class="text-xs bg-red-600/60 px-2 py-1 rounded">{{ $genre->name }}</span>
                @endforeach
            </div>
            <p class="text-gray-300 mt-3 line-clamp-3">{{ $movie->description ?? 'No description available.' }}</p>
            <div class="mt-5 flex gap-3">
                <a href="{{ route('movie.play', ['slug' => $movie->slug]) }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow">
                    ▶ Watch Now
                </a>
                <button class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow">
                    ❤️ Favorite
                </button>
                <button class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow">
                    ➕ Add to Playlist
                </button>
            </div>
        </div>
    </section>

    {{-- Details + Episodes --}}
    <div class="max-w-7xl mx-auto px-6 mt-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            {{-- Poster --}}
            <div>
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="rounded-xl shadow-xl w-full object-cover">
                <div class="mt-3 text-gray-400 text-sm">
                    <p><strong>Type:</strong> {{ ucfirst($movie->type) }}</p>
                    <p><strong>Total Seasons:</strong> {{ $movie->seasons->count() }}</p>
                    <p>
                        <strong>Total Episodes:</strong>
                        {{ $movie->seasons?->flatMap->episodes->count() ?? 0 }}
                    </p>
                </div>
            </div>

            {{-- Episode List --}}
            <div class="lg:col-span-3">
                <h2 class="text-2xl font-bold mb-4">Episodes</h2>

                @forelse($movie->seasons as $season)
                    <h3 class="text-lg font-semibold mt-4 mb-2 text-gray-200">
                        {{ $season->title }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($season->episodes as $episode)
                            <a href="{{ route('episode.show', ['id' => $episode->id]) }}" class="group relative block bg-gray-900 rounded-lg overflow-hidden hover:shadow-xl transition">
                                <img src="{{ $episode->thumbnail ?? $movie->poster }}" class="w-full h-40 object-cover group-hover:opacity-70">
                                <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black via-black/40 to-transparent">
                                    <h4 class="text-white font-semibold text-sm">{{ $episode->title }}</h4>
                                    <p class="text-xs text-gray-400">Ep {{ $episode->episode_number }} • {{ $episode->duration ?? '?' }} min</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @empty
                    <p class="text-gray-500">No episodes available yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Comments Section --}}
        <div class="mt-16">
            <h2 class="text-xl font-semibold mb-3">Comments</h2>
            <x-comment-section :movie="$movie" />
        </div>
    </div>
@endsection
