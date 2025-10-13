<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yareunime - Watch Anime Online</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f0f0f] text-white font-sans">

{{-- Navbar --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f0f]/80 backdrop-blur border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold text-red-500 tracking-wide">Yareunime</a>
        <div class="flex items-center space-x-6 text-gray-300">
            <a href="/" class="hover:text-white">Home</a>
            <a href="/top" class="hover:text-white">Top Anime</a>
            <a href="/playlist" class="hover:text-white">Playlists</a>
            <a href="/profile" class="hover:text-white">Profile</a>
        </div>
    </div>
</nav>

{{-- Main Content --}}
<main class="pt-20">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="mt-20 border-t border-gray-800 py-6 text-center text-gray-500">
    <p>© {{ date('Y') }} <span class="text-red-500">Yareunime</span> — Watch Anime Anytime.</p>
</footer>

<script src="https://unpkg.com/scrollreveal"></script>
<script>
    ScrollReveal().reveal('.group', { interval: 100, distance: '40px', origin: 'bottom' });
</script>
</body>
</html>
