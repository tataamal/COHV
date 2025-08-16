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
        <!-- Sidebar Header -->
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

        <!-- Navigation Links -->
        <nav class="flex-1 px-2 py-6 space-y-4 overflow-y-auto">
            @foreach($menuItems as $section)
                <div>
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
                        <!-- Accordion Menu -->
                        <div x-data="{ open: true }">
                            <button 
                                @click="!sidebarCollapsed && (open = !open)" 
                                class="w-full flex items-center p-2.5 text-gray-300 hover:bg-gray-700 rounded-md focus:outline-none group"
                                :class="sidebarCollapsed ? 'justify-center' : 'justify-between'"
                                :title="sidebarCollapsed ? '{{ $item['name'] }}' : ''">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        class="ml-3 whitespace-nowrap">{{ $item['name'] }}</span>
                                </span>
                                @if(isset($item['submenu']))
                                    <svg 
                                        x-show="!sidebarCollapsed" 
                                        :class="{'rotate-180': open}" 
                                        class="w-5 h-5 transform transition-transform duration-200 shrink-0" 
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </button>
                            
                            @if(isset($item['submenu']))
                                <div 
                                    x-show="open && !sidebarCollapsed" 
                                    x-cloak 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                                    class="pl-8 space-y-1 text-sm mt-1">
                                    @foreach($item['submenu'] as $subitem)
                                        <a href="{{ $subitem['route'] }}" class="flex items-center justify-between p-2 text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition-colors">
                                            <span>{{ $subitem['name'] }}</span>
                                            @if(isset($subitem['badge']))
                                                <span class="bg-gray-700 text-xs rounded-full px-2 py-0.5">{{ $subitem['badge'] }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <!-- Sidebar Footer -->
        <div class="px-4 pb-4 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full p-2.5 text-gray-400 hover:bg-red-600 hover:text-white rounded-md transition-colors group" 
                    :class="{'justify-center': sidebarCollapsed}"
                    :title="sidebarCollapsed ? 'Logout' : ''">
                    
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="ml-3 whitespace-nowrap">Logout
                    </span>
                </button>
            </form>
        </div>
    </div>
</aside>