@props(['movie'])

<div class="group relative w-48 shrink-0 cursor-pointer">
    <div class="aspect-[2/3] overflow-hidden rounded-xl bg-gray-900 shadow-lg">
        <img src="{{ $movie->poster ?? '/placeholder.jpg' }}" alt="{{ $movie->title }}" class="object-cover w-full h-full transition duration-300 group-hover:scale-110">
    </div>
    <div class="mt-2">
        <h3 class="text-sm font-semibold truncate">{{ $movie->title }}</h3>
    </div>


    {{-- Overlay Play Button --}}
    <div class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition">
        <a href="{{ route('movie.show', $movie->slug) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg flex items-center space-x-1">
            <x-lucide-play class="w-4 h-4"/> <span>Watch</span>
        </a>
    </div>
</div>
