@props(['movie'])

<div class="bg-gray-900 rounded-xl p-5">
    <form action="{{ route('comment.store') }}" method="POST" class="mb-5">
        @csrf
        <textarea name="content" class="w-full bg-gray-800 text-white rounded p-3" rows="3" placeholder="Add a comment..."></textarea>
        <input type="hidden" name="movie_id" value="{{ $movie->id }}">
        <div class="flex justify-end mt-2">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Post</button>
        </div>
    </form>

    <div class="divide-y divide-gray-800">
        @foreach($movie->comments ?? [] as $comment)
            <div class="py-3">
                <p class="text-sm text-gray-200">{{ $comment->content }}</p>
                <span class="text-xs text-gray-500">by {{ $comment->user->name ?? 'Anonymous' }}</span>
            </div>
        @endforeach
    </div>
</div>
