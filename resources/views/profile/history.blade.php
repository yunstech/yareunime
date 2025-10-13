@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <x-section-title>Riwayat Tontonan</x-section-title>

        @forelse($histories as $progress)
            <a href="{{ route('episode.show', $progress->episode->id) }}"
               class="flex items-center gap-4 bg-gray-800 hover:bg-gray-700 transition-all rounded-xl p-4 mb-3">
                <img src="{{ $progress->episode->season->movie->poster }}" class="w-24 h-16 rounded-lg object-cover">
                <div>
                    <h4 class="text-lg font-semibold">{{ $progress->episode->title }}</h4>
                    <p class="text-gray-400 text-sm">
                        {{ $progress->episode->season->movie->title }} — Season {{ $progress->episode->season->season_number }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Terakhir ditonton {{ $progress->updated_at->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <p class="text-gray-400">Belum ada riwayat tontonan.</p>
        @endforelse

        <div class="mt-6">
            {{ $histories->links() }}
        </div>
    </div>
@endsection
