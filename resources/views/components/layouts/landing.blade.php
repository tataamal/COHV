<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css', 'resources/js/app.js')
    <title>{{ $title ?? 'KMI System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    
</head>
<body x-data="{ isLoading: false }" 
      x-init="$watch('isLoading', value => { if (value) startLoaderTypingEffect() })"
      class="h-full bg-gray-50 antialiased">
    
    <!-- Halaman Overlay Loading -->
    <div x-show="isLoading"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm">
        
        {{-- [DIPERBARUI] Ukuran container animasi diperbesar --}}
        <div class="w-96 h-96">
            <dotlottie-wc
                src="https://lottie.host/efd6845b-3f14-492b-98a8-9336a08a5f98/RJUcUzKVqm.lottie"
                speed="1"
                autoplay
                loop>
            </dotlottie-wc>
        </div>
        
        {{-- [DIPERBARUI] Margin atas disesuaikan untuk posisi teks yang lebih baik --}}
        <h2 class="text-2xl font-bold text-gray-800 tracking-wide -mt-10">
            <span id="loader-typing-effect"></span>
            <span id="loader-typing-cursor" class="inline-block w-0.5 h-7 bg-gray-800 animate-pulse ml-1" style="margin-bottom: -5px;"></span>
        </h2>
        <p class="mt-2 text-gray-600">Mohon tunggu, kami sedang mengambil data dari SAP.</p>
    </div>

    {{ $slot }}

    <script>
        function startLoaderTypingEffect() {
            const textToType = "mohon tunggu sebentar...";
            const element = document.getElementById('loader-typing-effect');
            const cursor = document.getElementById('loader-typing-cursor');
            if (!element || !cursor) return;

            let charIndex = 0;
            let isDeleting = false;

            function typeLoop() {
                cursor.style.display = 'inline-block';
                const currentText = textToType.substring(0, charIndex);
                element.textContent = currentText;

                if (!isDeleting && charIndex < textToType.length) {
                    charIndex++;
                    setTimeout(typeLoop, 120);
                } else if (isDeleting && charIndex > 0) {
                    charIndex--;
                    setTimeout(typeLoop, 80);
                } else {
                    isDeleting = !isDeleting;
                    setTimeout(typeLoop, isDeleting ? 2000 : 500); 
                }
            }
            typeLoop();
        }
    </script>

    @stack('scripts')
</body>
</html>

