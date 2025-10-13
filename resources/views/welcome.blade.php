<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flix.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animasi halus */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hover-zoom:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-800 via-gray-900 to-gray-950 text-white font-sans min-h-screen">

<!-- Navbar -->
<nav class="flex justify-between items-center px-10 py-6">
    <h1 class="text-2xl font-bold">Flix.id</h1>
    <div class="flex gap-6 text-gray-300">
        <button class="hover:text-white transition">Movie</button>
        <button class="hover:text-white transition">Series</button>
        <button class="hover:text-white transition">Originals</button>
    </div>
    <div class="flex items-center gap-4">
        <input type="text" placeholder="Search..." class="px-3 py-2 rounded-full bg-gray-700 text-sm focus:outline-none">
        <div class="flex items-center gap-2">
            <img src="https://i.pravatar.cc/40" class="rounded-full w-8 h-8">
            <span class="font-semibold">Sarah J</span>
            <span class="bg-yellow-500 text-black px-2 py-1 rounded-full text-xs">Premium</span>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="px-10 py-6 fade-in">
    <div class="grid grid-cols-2 gap-6">
        <div class="relative hover-zoom rounded-3xl overflow-hidden">
            <img src="https://images.unsplash.com/photo-1558980664-10e7170d01d4" class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent p-6 flex flex-col justify-end">
                <h2 class="text-2xl font-bold">The Adventure of Blue Sword</h2>
                <p class="text-sm text-gray-300">Let Play Moview</p>
            </div>
        </div>
        <div class="relative hover-zoom rounded-3xl overflow-hidden">
            <img src="https://images.unsplash.com/photo-1606112219348-204d7d8b94ee" class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent p-6 flex flex-col justify-end">
                <h2 class="text-2xl font-bold">Recalling the journey of Dol's exciting story</h2>
                <p class="text-sm text-gray-300">Let Play Moview</p>
            </div>
        </div>
    </div>
</section>

<!-- Category Buttons -->
<div class="flex justify-center gap-4 py-4">
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Trending</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Action</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Romance</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition bg-white/10 text-white">Animation</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Horror</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Special</button>
    <button class="px-4 py-2 rounded-full glass hover:bg-white/10 transition">Drakor</button>
</div>

<!-- Trending in Animation -->
<section class="px-10 mt-6 fade-in">
    <h3 class="text-xl font-semibold mb-4">Trending in Animation</h3>
    <div class="flex gap-5 overflow-x-auto scrollbar-hide pb-4">
        <!-- Card -->
        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/vZ3G8oK.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Loetoeng Kasarung</h4>
                <p class="text-xs text-gray-300">⭐ 7.8 • 2023</p>
            </div>
        </div>

        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/xg8t3jH.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Gajah Langka</h4>
                <p class="text-xs text-gray-300">⭐ 6.0 • 2023</p>
            </div>
        </div>

        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/qNn7utN.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Si Kang Satay</h4>
                <p class="text-xs text-gray-300">⭐ 7.1 • 2023</p>
            </div>
        </div>

        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/qvD88jZ.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Mommy Cat</h4>
                <p class="text-xs text-gray-300">⭐ 7.8 • 2023</p>
            </div>
        </div>

        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/MXfJjBt.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Hijaber Cantiq</h4>
                <p class="text-xs text-gray-300">⭐ 6.1 • 2023</p>
            </div>
        </div>

        <div class="flex-shrink-0 w-40 hover-zoom transition relative">
            <img src="https://i.imgur.com/8K5VuZm.jpeg" class="rounded-2xl w-full h-56 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-2xl">
                <h4 class="text-sm font-semibold">Xatra-X</h4>
                <p class="text-xs text-gray-300">⭐ 6.5 • 2022</p>
            </div>
        </div>
    </div>
</section>

</body>
</html>
