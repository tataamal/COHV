<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? config('app.name', 'Laravel') }} - SAP PO Interface</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Custom styles for responsive table */
        @media (max-width: 767px) {
            .responsive-table thead { display: none; }
            .responsive-table tr {
                display: block;
                margin-bottom: 1.5rem;
                border-bottom-width: 2px;
                padding-bottom: 1rem;
            }
            .responsive-table tr:last-child {
                margin-bottom: 0;
                border-bottom: none;
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 0.5rem 1rem;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                margin-right: 1rem;
            }
        }
    </style>

    <style>
        /* Loader dari penyedia — ditambah perbaikan kecil agar pasti jalan */
        .loader {
            position: relative;         /* perlu untuk pseudo-elements */
            display: inline-block;      /* pastikan width/height berlaku */
            transform: rotateZ(45deg);
            perspective: 1000px;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            color: #fff;
        }
        .loader:before,
        .loader:after {
            content: '';
            display: block;
            position: absolute;
            top: 0; left: 0;
            width: inherit;
            height: inherit;
            border-radius: 50%;
            transform: rotateX(70deg);
            animation: 1s spin linear infinite;
        }
        .loader:after {
            color: #FF3D00;
            transform: rotateY(70deg);
            animation-delay: .4s;
        }

        /* Keyframes tambahan dari penyedia (rotate/rotateccw tidak dipakai tapi aman disertakan) */
        @keyframes rotate {
            0% { transform: translate(-50%, -50%) rotateZ(0deg); }
            100% { transform: translate(-50%, -50%) rotateZ(360deg); }
        }
        @keyframes rotateccw {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(-360deg); }
        }
        @keyframes spin {
            0%, 100% { box-shadow: .2em 0 0 0 currentcolor; }
            12%      { box-shadow: .2em .2em 0 0 currentcolor; }
            25%      { box-shadow: 0 .2em 0 0 currentcolor; }
            37%      { box-shadow: -.2em .2em 0 0 currentcolor; }
            50%      { box-shadow: -.2em 0 0 0 currentcolor; }
            62%      { box-shadow: -.2em -.2em 0 0 currentcolor; }
            75%      { box-shadow: 0 -.2em 0 0 currentcolor; }
            87%      { box-shadow: .2em -.2em 0 0 currentcolor; }
        }
        </style>
    
    <!-- Additional Styles -->
    <script>
        (function () {
        const overlay = document.getElementById('global-loading');
        if (!overlay) return;

        const show = () => overlay.classList.remove('hidden');
        const hide = () => overlay.classList.add('hidden');

        // A) Klik pada elemen dengan [data-loading]
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-loading]');
            if (!trigger) return;

            // Abaikan open-in-new-tab / klik non-left / target=_blank
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (e.button !== 0) return;
            if (trigger.getAttribute('target') === '_blank') return;

            // Opsional: cegah double click pada button
            if (trigger.tagName === 'BUTTON') {
            trigger.disabled = true;
            trigger.classList.add('opacity-70', 'pointer-events-none');
            }

            show();
        }, { passive: true });

        // B) Submit form apa pun
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.matches('form')) return;

            const btn = form.querySelector('button[type="submit"][data-loading]');
            if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            }
            show();
        }, true);

        // C) Pindah halaman (redirect server-side)
        window.addEventListener('beforeunload', show);

        // Hooks optional untuk AJAX manual:
        window.addEventListener('loading:show', show);
        window.addEventListener('loading:hide', hide);
        })();
        </script>
    @stack('styles')

</head>
<body class="h-full">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen bg-gray-100">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
            x-cloak>
        </div>

        <!-- Sidebar -->
        <x-navigation.sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Navigation -->
            <x-navigation.topbar :user="auth()->user()" />

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 sm:p-6">
                <div class="container mx-auto space-y-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Global Loading Overlay -->
    <div id="global-loading"
        class="hidden fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-black bg-opacity-50"
        role="status" aria-live="polite" aria-label="Loading">
    
    <!-- Spinner dari penyedia -->
    <span class="loader"></span>

    <!-- Teks kecil -->
    <p class="mt-4 text-white text-sm font-medium tracking-wide">
        Sedang mengambil data dari SAP…
    </p>
    </div>

    <!-- Scripts -->

    <script>
    (function () {
        const overlay = document.getElementById('global-loading');
        if (!overlay) return;

        const show = () => overlay.classList.remove('hidden');
        const hide = () => overlay.classList.add('hidden');

        // A) Klik pada elemen dengan [data-loading]
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-loading]');
            if (!trigger) return;

            // Abaikan open-in-new-tab / klik non-left / target=_blank
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (e.button !== 0) return;
            if (trigger.getAttribute('target') === '_blank') return;

            // Opsional: cegah double click pada button
            if (trigger.tagName === 'BUTTON') {
            trigger.disabled = true;
            trigger.classList.add('opacity-70', 'pointer-events-none');
            }

            show();
        }, { passive: true });

        // B) Submit form apa pun
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.matches('form')) return;

            const btn = form.querySelector('button[type="submit"][data-loading]');
            if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            }
            show();
        }, true);

        // C) Pindah halaman (redirect server-side)
        window.addEventListener('beforeunload', show);

        // Hooks optional untuk AJAX manual:
        window.addEventListener('loading:show', show);
        window.addEventListener('loading:hide', hide);
    })();
    </script>

    
    @stack('scripts')
</body>
</html>