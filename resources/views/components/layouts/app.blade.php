<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'KMI System' }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    
    {{-- ✅ PERBAIKAN: Mengkonfigurasi Tailwind untuk menggunakan font default baru --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Alpine Core (harus dimuat pertama) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine Plugins (dimuat setelah core) -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Pustaka lainnya -->
    <script defer type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Style ini tidak lagi diperlukan karena sudah dihandle oleh config Tailwind */
        /* body { font-family: 'Plus Jakarta Sans', sans-serif; } */
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body 
    x-data="{ sidebarOpen: false, sidebarCollapsed: window.innerWidth < 1024, isLoading: false }" 
    @resize.window="sidebarCollapsed = window.innerWidth < 1024"
    x-init="$watch('isLoading', value => { if (value) startLoaderTypingEffect() })"
    @link-clicked.window="isLoading = true; setTimeout(() => { window.location.href = $event.detail.href }, 150)"
    class="h-full bg-gray-100 antialiased font-sans"> {{-- ✅ PERBAIKAN: Menambahkan class font-sans --}}
    
    <!-- Halaman Overlay Loading -->
    <div x-show="isLoading" x-cloak class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm">
        <div class="w-96 h-96">
            <dotlottie-wc src="https://lottie.host/efd6845b-3f14-492b-98a8-9336a08a5f98/RJUcUzKVqm.lottie" speed="1" autoplay loop></dotlottie-wc>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 tracking-wide -mt-12">
            <span id="loader-typing-effect"></span>
            <span id="loader-typing-cursor" class="inline-block w-0.5 h-7 bg-gray-800 animate-pulse ml-1" style="margin-bottom: -5px;"></span>
        </h2>
        <p class="mt-2 text-gray-600">Mohon tunggu, kami sedang mengambil data dari SAP.</p>
    </div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <x-navigation.sidebar />
        
        <!-- Backdrop untuk layar kecil -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-black/30 z-30 lg:hidden"></div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 min-w-0">
            <!-- Topbar -->
            <x-navigation.topbar />

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script>
        function startLoaderTypingEffect() {
            const textToType = "mohon tunggu sebentar...";
            const element = document.getElementById('loader-typing-effect');
            const cursor = document.getElementById('loader-typing-cursor');
            if (!element || !cursor) return;
            let charIndex = 0; let isDeleting = false;
            function typeLoop() {
                cursor.style.display = 'inline-block';
                const currentText = textToType.substring(0, charIndex);
                element.textContent = currentText;
                if (!isDeleting && charIndex < textToType.length) {
                    charIndex++; setTimeout(typeLoop, 120);
                } else if (isDeleting && charIndex > 0) {
                    charIndex--; setTimeout(typeLoop, 80);
                } else {
                    isDeleting = !isDeleting; setTimeout(typeLoop, isDeleting ? 2000 : 500); 
                }
            }
            typeLoop();
        }
    </script>

    @stack('scripts')
</body>
</html>