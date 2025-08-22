@props([
    'user',
    // Format item yang disarankan:
    // ['name' => 'Dashboard', 'route_name' => 'dashboard.show']
    // ['name' => 'List',      'route_name' => 'data2.detail']
    //
    // Opsi lanjutan per item (semua opsional):
    // - 'params'      => ['foo' => 'bar']      // param ekstra selain 'kode'
    // - 'active_on'   => 'dashboard.*'         // pola aktif khusus
    // - 'route'       => '/path/with/{kode}'   // kalau ingin pakai path manual, boleh
    'navigation' => [
        ['name' => 'Dashboard', 'route_name' => 'dashboard.show'],
        ['name' => 'List',      'route_name' => 'show.detail.data2'],
    ],
])

@php
    // 1) Ambil kode dari route param atau fallback segment(1)/query
    $kodeAktif = request()->route('kode') ?? request()->segment(1) ?? request('kode');

    // 2) Normalisasi navigation → bentuk href & active
    $normalizedNav = collect($navigation)->map(function ($item) use ($kodeAktif) {
        // Default values
        $name   = $item['name'] ?? 'Menu';
        $active = false;
        $href   = '#';

        // Jika pakai named route
        if (isset($item['route_name'])) {
            $params = array_merge(['kode' => $kodeAktif], $item['params'] ?? []);
            $href   = route($item['route_name'], $params);
            $active = request()->routeIs($item['route_name'].'*');
        }
        // Jika pakai string route manual
        elseif (isset($item['route'])) {
            $href   = str_replace('{kode}', $kodeAktif, $item['route']);
            $active = url()->current() === url($href);
        }

        return compact('name','href','active');
    })->all();
@endphp

<header class="relative flex items-center justify-between p-4 bg-white border-b shrink-0">
    <!-- Left side content -->
    <div class="flex items-center">
        <!-- Tombol Minimize Sidebar (Desktop) -->
        <button 
            @click="sidebarCollapsed = !sidebarCollapsed" 
            class="hidden lg:block text-gray-500 hover:text-blue-600 focus:outline-none mr-4 p-1 rounded-md hover:bg-gray-100 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>
        
        <!-- Tombol Menu Mobile -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="text-gray-500 focus:outline-none lg:hidden p-1 rounded-md hover:bg-gray-100 transition-colors">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        
        <!-- Navigasi Top Bar (Desktop) -->
        <nav class="hidden md:flex items-center space-x-2 ml-4">
            @foreach($normalizedNav as $item)
                <a href="{{ $item['href'] }}" 
                   class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                          {{ $item['active'] ? 'text-white bg-blue-700 hover:bg-blue-800' : 'text-gray-500 hover:text-blue-700 hover:bg-gray-100' }}">
                    {{ $item['name'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Navigasi Top Bar (Mobile - Centered) -->
    <nav class="absolute left-1/2 -translate-x-1/2 md:hidden">
        <div class="flex items-center space-x-2">
            @foreach($normalizedNav as $item)
                <a href="{{ $item['href'] }}" 
                   class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                          {{ $item['active'] ? 'text-white bg-blue-700' : 'text-gray-500 hover:text-blue-700' }}">
                    {{ $item['name'] }}
                </a>
            @endforeach
        </div>
    </nav>

    <!-- User Info -->
    <div class="hidden sm:flex items-center space-x-3">
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-800">{{ $user->name ?? 'Guest User' }}</p>
            <p class="text-xs text-gray-500">{{ $user->email ?? 'guest@example.com' }}</p>
        </div>
        <div class="relative">
            <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" 
                 src="{{ $user->avatar ?? 'https://placehold.co/100x100/E2E8F0/4A5568?text=' . substr($user->name ?? 'G', 0, 1) }}" 
                 alt="{{ $user->name ?? 'User' }} Avatar">
            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
        </div>
    </div>

    <!-- Mobile User Info (Alternative) -->
    <div class="flex sm:hidden items-center">
        <img class="h-8 w-8 rounded-full object-cover border-2 border-gray-200" 
             src="{{ $user->avatar ?? 'https://placehold.co/100x100/E2E8F0/4A5568?text=' . substr($user->name ?? 'G', 0, 1) }}" 
             alt="{{ $user->name ?? 'User' }} Avatar">
    </div>
</header>