@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white px-8 py-10">
        <div class="flex justify-between items-center mb-6">
            <x-section-title>Notifikasi</x-section-title>
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="bg-gray-800 px-3 py-1 rounded hover:bg-gray-700 text-sm">Tandai Semua Dibaca</button>
            </form>
        </div>

        @forelse($notifications as $notif)
            <div class="bg-gray-800 p-4 rounded-xl mb-3 flex justify-between items-center
                    {{ $notif->is_read ? 'opacity-70' : 'border-l-4 border-pink-500' }}">
                <div>
                    <h3 class="font-semibold text-pink-400">{{ $notif->title }}</h3>
                    <p class="text-gray-300 text-sm">{{ $notif->message }}</p>
                    @if($notif->link)
                        <a href="{{ $notif->link }}" class="text-sm text-blue-400 hover:underline">Lihat detail →</a>
                    @endif
                </div>
                @if(!$notif->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                        @csrf
                        <button class="text-xs text-gray-400 hover:text-pink-400">Tandai Dibaca</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-gray-400">Tidak ada notifikasi.</p>
        @endforelse
    </div>
@endsection
