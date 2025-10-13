@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-10">
        {{-- Episode Header --}}
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                <img src="{{ $episode->thumbnail ?? $episode->season->movie->poster }}" alt="{{ $episode->title }}"
                     class="rounded-lg w-56 h-80 object-cover shadow-lg">
            </div>

            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white">{{ $episode->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">
                    {{ $episode->season->movie->title }} • Episode {{ $episode->episode_number }}
                    ({{ $episode->duration ?? '?' }} min)
                </p>

                {{-- Watch Buttons --}}
                <div class="flex items-center gap-3 mt-4">
                    <a href="#player" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                        ▶ Play Episode
                    </a>
                    <a href="{{ route('movie.show', $episode->season->movie->slug) }}"
                       class="bg-gray-800 hover:bg-gray-700 text-gray-200 px-4 py-2 rounded-lg">
                        ⬅ Back to Anime
                    </a>
                </div>

                {{-- Description --}}
                <p class="mt-4 text-gray-300 line-clamp-4">
                    {{ $episode->season->movie->description }}
                </p>
            </div>
        </div>

        {{-- Player Section --}}
        <div id="player" class="mt-10 bg-black rounded-xl overflow-hidden shadow-lg">
            <div class="relative w-full pt-[56.25%]"> {{-- 16:9 aspect ratio --}}
                <div class="absolute inset-0" id="iframeBox" style="transition: opacity 0.4s ease;">
                    @if($episode->embed_html)
                        {!! preg_replace('/(width|height)="[^"]*"/', '', $episode->embed_html) !!}
                    @else
                        <div class="flex justify-center items-center h-full text-gray-500">
                            <p>No player available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mirror Server Selector --}}
        @if($episode->streams->count() > 0)
            <div class="mt-8 bg-gray-900 rounded-xl p-5">
                <h2 class="text-lg font-bold mb-3">Alternate Streaming Servers</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($episode->streams->groupBy('quality') as $quality => $servers)
                        <div>
                            <h3 class="text-sm text-gray-400 font-semibold mb-2">{{ strtoupper($quality) }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($servers as $s)
                                    <button
                                        class="server-btn bg-gray-800 hover:bg-gray-700 text-white text-xs px-3 py-2 rounded"
                                        data-quality="{{ $s->quality }}"
                                        data-server="{{ strtolower($s->server_name) }}"
                                        data-content="{{ $s->data_content }}"
                                        data-embed="{{ $s->embed_html ?? '' }}"
                                    >
                                        {{ ucfirst($s->server_name) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Download Links --}}
        @if($episode->downloads->count() > 0)
            <div class="mt-8 bg-gray-900 rounded-xl p-5">
                <h2 class="text-lg font-bold mb-3">Download Links</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($episode->downloads as $dl)
                        <a href="{{ $dl->url }}" target="_blank"
                           class="bg-gray-800 hover:bg-red-600 transition text-white text-xs px-3 py-2 rounded">
                            {{ $dl->label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Episode Navigation --}}
        <div class="mt-10 flex justify-between items-center text-gray-400">
            @if($prevEpisode)
                <a href="{{ route('episode.show', $prevEpisode->id) }}" class="hover:text-white">⬅ Previous Episode</a>
            @else
                <span></span>
            @endif

            @if($nextEpisode)
                <a href="{{ route('episode.show', $nextEpisode->id) }}" class="hover:text-white">Next Episode ➡</a>
            @endif
        </div>

        {{-- Comments --}}
        <div class="mt-14">
            <x-comment-section :movie="$episode->season->movie" />
        </div>
    </div>

    {{-- JS Player Switcher --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const iframeBox = document.getElementById('iframeBox');

            document.querySelectorAll('.server-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const embedHtml = btn.dataset.embed;
                    const server = btn.dataset.server;
                    const quality = btn.dataset.quality?.toUpperCase() || "???";

                    if (!embedHtml) return alert("No embed available for this server.");

                    const cleanHtml = embedHtml
                        .replace(/width="\d+"/g, 'width="100%"')
                        .replace(/height="\d+"/g, 'height="100%"')
                        .replace(/style="[^"]*"/g, '')
                        .replace(/<\/?div[^>]*>/g, '');

                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 flex items-center justify-center bg-black/70 text-white text-sm z-20';
                    overlay.innerHTML = `<span class="animate-pulse">Loading <b>${server}</b> • ${quality}...</span>`;
                    iframeBox.appendChild(overlay);

                    iframeBox.style.opacity = '0.3';

                    setTimeout(() => {
                        iframeBox.innerHTML = `
                    <div class="responsive-embed-stream absolute inset-0">
                        ${cleanHtml}
                    </div>
                `;
                        iframeBox.style.opacity = '1';
                        setTimeout(() => overlay.remove(), 500);
                    }, 300);
                });
            });
        });
    </script>

    <style>
        .responsive-embed-stream iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0;
            display: block;
        }
    </style>
@endsection
