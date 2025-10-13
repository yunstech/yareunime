@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <div class="flex justify-between items-center mb-8">
            <x-section-title>Top Anime</x-section-title>

            <form method="GET">
                <select name="filter" onchange="this.form.submit()"
                        class="bg-gray-800 text-white rounded-lg p-2">
                    <option value="daily" {{ $filter == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="alltime" {{ $filter == 'alltime' ? 'selected' : '' }}>Sepanjang Waktu</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($movies as $movie)
                <x-movie-card :poster="$movie->poster" :title="$movie->title" :genre="$movie->genre" />
            @endforeach
        </div>
    </div>
@endsection
