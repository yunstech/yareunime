<nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
    <a href="/" class="text-pink-500 font-bold text-xl">🎬 CineVerse</a>

    <div class="flex items-center gap-6">
        <a href="/" class="hover:text-pink-400">Home</a>
        <a href="{{ route('top') }}" class="hover:text-pink-400">Top</a>
        <a href="{{ route('playlist.index') }}" class="hover:text-pink-400">Playlist</a>
        <a href="{{ route('profile.show', auth()->user()->id ?? 1) }}" class="hover:text-pink-400">Profil</a>
    </div>

    @php
        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    @endphp

    <a href="{{ route('notifications.index') }}" class="relative hover:text-pink-400">
        🔔
        @if($unreadCount > 0)
            <span class="absolute -top-2 -right-2 bg-pink-600 text-xs px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
        @endif
    </a>

</nav>
