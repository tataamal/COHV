<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'KMI System' }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    
</head>
<body x-data="{ isLoading: false }" class="h-full bg-gray-50 antialiased">
    
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
        
        <div class="w-72 h-72">
            <dotlottie-wc
                src="https://lottie.host/efd6845b-3f14-492b-98a8-9336a08a5f98/RJUcUzKVqm.lottie"
                speed="1"
                autoplay
                loop>
            </dotlottie-wc>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 tracking-wide -mt-6">
            <span id="loader-typing-effect"></span>
            <span id="loader-typing-cursor" class="inline-block w-0.5 h-7 bg-gray-800 animate-pulse ml-1" style="margin-bottom: -5px;"></span>
        </h2>
        <p class="mt-2 text-gray-600">Mohon tunggu, kami sedang mengambil data dari SAP.</p>
    </div>

    {{ $slot }}

    {{-- [DIPERBARUI] Menambahkan script untuk efek mengetik pada loader --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('loaderTyping', () => ({
                init() {
                    // Awasi perubahan pada variabel isLoading
                    this.$watch('isLoading', (value) => {
                        if (value) {
                            // Jika isLoading menjadi true, mulai animasi
                            this.startTypingEffect();
                        }
                    });
                },
                startTypingEffect() {
                    const textToType = "Sedang Memuat Data...";
                    const element = document.getElementById('loader-typing-effect');
                    const cursor = document.getElementById('loader-typing-cursor');
                    if (!element || !cursor) return;

                    let index = 0;
                    element.textContent = ''; // Kosongkan teks setiap kali dimulai
                    cursor.style.display = 'inline-block'; // Tampilkan kursor

                    function type() {
                        if (index < textToType.length) {
                            element.textContent += textToType.charAt(index);
                            index++;
                            setTimeout(type, 100);
                        } else {
                            // Anda bisa menyembunyikan kursor setelah selesai jika mau
                            // cursor.style.display = 'none'; 
                        }
                    }
                    type();
                }
            }));
        });
    </script>

    @stack('scripts')
</body>
</html>

