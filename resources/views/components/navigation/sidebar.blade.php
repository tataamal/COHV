<aside 
    :class="{
        'translate-x-0': sidebarOpen, 
        '-translate-x-full': !sidebarOpen,
        'lg:w-20': sidebarCollapsed,
        'lg:w-64': !sidebarCollapsed,
        'lg:overflow-hidden': sidebarCollapsed
    }" 
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white transform transition-all duration-300 ease-in-out lg:static lg:inset-0 lg:transform-none lg:translate-x-0"
    style="transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;">
    
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-center h-20 border-b border-gray-800 shrink-0 px-4">
            <img src="{{ asset('images/KMI.png') }}" alt="Logo" class="rounded-full bg-white p-1 transition-all flex-shrink-0" :class="{'h-10 w-10': !sidebarCollapsed, 'h-8 w-8': sidebarCollapsed}">
            <h1 
                x-show="!sidebarCollapsed" 
                x-transition:enter="transition-opacity ease-in-out duration-150 delay-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in-out duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="text-lg font-base ml-3 whitespace-nowrap">PT. KMI</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-6 space-y-4 overflow-y-auto">
            @foreach($menuItems as $section)
                <div>
                    <!-- Section Title -->
                    <h2 
                        x-show="!sidebarCollapsed" 
                        x-transition:enter="transition-opacity ease-in-out duration-150 delay-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in-out duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="px-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $section['title'] }}
                    </h2>
                    <div x-show="sidebarCollapsed" class="h-px bg-gray-700 my-2 mx-2"></div>
                    
                    @foreach($section['items'] as $item)
                        @if(isset($item['submenu']))
                            {{-- Menu dengan Submenu --}}
                            @php
                                $isParentActive = false;
                                $activeSubmenuName = '';
                                foreach ($item['submenu'] as $subitem) {
                                    // Handle dynamic routes
                                    $routePattern = ltrim($subitem['route'], '/');
                                    
                                    // Check for exact match first
                                    if (request()->is($routePattern)) {
                                        $isParentActive = true;
                                        $activeSubmenuName = $subitem['name'];
                                        break;
                                    }
                                    
                                    // Check for wildcard match
                                    if (request()->is($routePattern . '/*')) {
                                        $isParentActive = true;
                                        $activeSubmenuName = $subitem['name'];
                                        break;
                                    }
                                    
                                    // Check if route has dynamic segments (contains {})
                                    if (strpos($routePattern, '{') !== false) {
                                        // Convert route pattern to regex
                                        $regexPattern = preg_replace('/\{[^}]+\}/', '[^/]+', $routePattern);
                                        $regexPattern = '#^' . $regexPattern . '(/.*)?$#';
                                        
                                        if (preg_match($regexPattern, request()->path())) {
                                            $isParentActive = true;
                                            $activeSubmenuName = $subitem['name'];
                                            break;
                                        }
                                    }
                                    
                                    // Check if current route name matches (if using named routes)
                                    if (isset($subitem['route_name']) && request()->routeIs($subitem['route_name'])) {
                                        $isParentActive = true;
                                        $activeSubmenuName = $subitem['name'];
                                        break;
                                    }
                                }
                            @endphp

                            <div x-data="{ open: {{ $isParentActive ? 'true' : 'false' }} }">
                                <!-- Parent Menu Button -->
                                <button 
                                    @click="!sidebarCollapsed && (open = !open)" 
                                    class="relative w-full flex items-center p-2.5 rounded-md focus:outline-none group transition-all duration-200 hover:scale-[1.02]"
                                    :class="{
                                        'justify-center': sidebarCollapsed, 
                                        'justify-between': !sidebarCollapsed,
                                        'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg border-l-4 border-blue-400': {{ $isParentActive ? 'true' : 'false' }}, 
                                        'text-gray-300 hover:bg-gray-700 hover:text-white': !{{ $isParentActive ? 'true' : 'false' }}
                                    }"
                                    :title="sidebarCollapsed ? '{{ $item['name'] }}' : ''">
                                    
                                    <!-- Active Indicator Line -->
                                    @if($isParentActive)
                                        <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 rounded-r-full"></span>
                                    @endif
                                    
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                                        </svg>
                                        <span 
                                            x-show="!sidebarCollapsed" 
                                            x-transition:enter="transition-opacity ease-in-out duration-150 delay-150"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition-opacity ease-in-out duration-150"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="ml-3 whitespace-nowrap font-medium">{{ $item['name'] }}</span>
                                    </span>
                                    
                                    <!-- Dropdown Arrow & Active Badge -->
                                    <div x-show="!sidebarCollapsed" class="flex items-center space-x-2">
                                        @if($isParentActive)
                                            <span class="px-2 py-0.5 text-xs bg-blue-500 text-white rounded-full font-medium">Active</span>
                                        @endif
                                        <svg 
                                            :class="{'rotate-180': open}" 
                                            class="w-5 h-5 transform transition-all duration-300 shrink-0 group-hover:text-blue-400" 
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </button>
                                
                                <!-- Submenu Container -->
                                <div 
                                    x-show="open && !sidebarCollapsed" 
                                    x-cloak 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
                                    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2 scale-95"
                                    class="pl-4 space-y-1 mt-2 mb-2">
                                    
                                    @foreach($item['submenu'] as $subitem)
                                        @php
                                            $routePattern = ltrim($subitem['route'], '/');
                                            $isSubmenuActive = false;
                                            
                                            // Check for exact match
                                            if (request()->is($routePattern)) {
                                                $isSubmenuActive = true;
                                            }
                                            // Check for wildcard match
                                            elseif (request()->is($routePattern . '/*')) {
                                                $isSubmenuActive = true;
                                            }
                                            // Check for dynamic routes (contains {})
                                            elseif (strpos($routePattern, '{') !== false) {
                                                // Convert route pattern to regex
                                                $regexPattern = preg_replace('/\{[^}]+\}/', '[^/]+', $routePattern);
                                                $regexPattern = '#^' . $regexPattern . '(/.*)?$#';
                                                
                                                if (preg_match($regexPattern, request()->path())) {
                                                    $isSubmenuActive = true;
                                                }
                                            }
                                            // Check named routes if available
                                            elseif (isset($subitem['route_name']) && request()->routeIs($subitem['route_name'])) {
                                                $isSubmenuActive = true;
                                            }
                                        @endphp
                                        
                                        <a href="{{ $subitem['route'] }}" 
                                           class="relative flex items-center justify-between p-2.5 pl-8 rounded-md transition-all duration-200 group hover:scale-[1.02]"
                                           :class="{
                                               'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md transform scale-[1.02]': {{ $isSubmenuActive ? 'true' : 'false' }},
                                               'text-gray-400 hover:text-white hover:bg-gray-700': !{{ $isSubmenuActive ? 'true' : 'false' }}
                                           }">
                                            
                                            <!-- Active Indicator -->
                                            @if($isSubmenuActive)
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 h-2 w-2 bg-white rounded-full animate-pulse"></span>
                                                <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-300 rounded-r-full"></span>
                                            @else
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 h-1.5 w-1.5 bg-gray-500 rounded-full group-hover:bg-blue-400 transition-colors"></span>
                                            @endif
                                            
                                            <span class="font-medium">{{ $subitem['name'] }}</span>
                                            
                                            <!-- Badge & Active Indicator -->
                                            <div class="flex items-center space-x-2">
                                                @if(isset($subitem['badge']))
                                                    <span class="bg-gray-700 text-xs rounded-full px-2 py-0.5 font-medium">{{ $subitem['badge'] }}</span>
                                                @endif
                                                @if($isSubmenuActive)
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                <!-- Collapsed State Tooltip -->
                                <div x-show="sidebarCollapsed && {{ $isParentActive ? 'true' : 'false' }}" 
                                     class="absolute left-20 top-0 bg-gray-800 text-white px-3 py-2 rounded-md shadow-lg z-50 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">{{ $item['name'] }}</span>
                                        <span class="text-xs bg-blue-500 px-2 py-0.5 rounded-full">{{ $activeSubmenuName }}</span>
                                    </div>
                                    <div class="absolute left-0 top-1/2 -translate-x-1 -translate-y-1/2 w-0 h-0 border-4 border-transparent border-r-gray-800"></div>
                                </div>
                            </div>

                        @else
                            {{-- Menu Tanpa Submenu --}}
                            @php
                                $routePattern = ltrim($item['route'] ?? '#', '/');
                                $isMenuActive = false;
                                
                                // Check for exact match
                                if (request()->is($routePattern)) {
                                    $isMenuActive = true;
                                }
                                // Check for wildcard match
                                elseif (request()->is($routePattern . '/*')) {
                                    $isMenuActive = true;
                                }
                                // Check for dynamic routes (contains {})
                                elseif (strpos($routePattern, '{') !== false) {
                                    // Convert route pattern to regex
                                    $regexPattern = preg_replace('/\{[^}]+\}/', '[^/]+', $routePattern);
                                    $regexPattern = '#^' . $regexPattern . '(/.*)?$#';
                                    
                                    if (preg_match($regexPattern, request()->path())) {
                                        $isMenuActive = true;
                                    }
                                }
                                // Check named routes if available
                                elseif (isset($item['route_name']) && request()->routeIs($item['route_name'])) {
                                    $isMenuActive = true;
                                }
                            @endphp

                            <a href="{{ $item['route'] ?? '#' }}" 
                               class="relative flex items-center p-2.5 rounded-md focus:outline-none group transition-all duration-200 hover:scale-[1.02]"
                               :class="{
                                   'justify-center': sidebarCollapsed, 
                                   'justify-start': !sidebarCollapsed,
                                   'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg transform scale-[1.02]': {{ $isMenuActive ? 'true' : 'false' }}, 
                                   'text-gray-300 hover:bg-gray-700 hover:text-white': !{{ $isMenuActive ? 'true' : 'false' }}
                               }"
                               :title="sidebarCollapsed ? '{{ $item['name'] }}' : ''">
                               
                                <!-- Active Indicator Line -->
                                @if($isMenuActive)
                                    <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 rounded-r-full"></span>
                                @endif
                                
                                <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                                </svg>
                                
                                <span 
                                    x-show="!sidebarCollapsed" 
                                    x-transition:enter="transition-opacity ease-in-out duration-150 delay-150"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition-opacity ease-in-out duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="ml-3 whitespace-nowrap font-medium">{{ $item['name'] }}</span>
                                
                                @if($isMenuActive)
                                    <span x-show="!sidebarCollapsed" class="ml-auto">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </nav>

        <!-- Sidebar Footer -->
        <div class="px-4 pb-4 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full p-2.5 text-gray-400 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-700 hover:text-white rounded-md transition-all duration-200 group hover:scale-[1.02] hover:shadow-lg" 
                    :class="{'justify-center': sidebarCollapsed}"
                    :title="sidebarCollapsed ? 'Logout' : ''">
                    
                    <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>

                    <span 
                        x-show="!sidebarCollapsed" 
                        x-transition:enter="transition-opacity ease-in-out duration-150 delay-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in-out duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="ml-3 whitespace-nowrap font-medium">Logout
                    </span>
                </button>
            </form>
        </div>
    </div>
</aside>