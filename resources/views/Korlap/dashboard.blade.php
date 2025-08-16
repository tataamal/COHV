<x-layouts.app title="Dashboard Korlap">
    <!-- Welcome Section -->
    <x-ui.card>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->name }}!</h2>
                <p class="text-gray-600 mt-1">Hari ini adalah {{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
            </div>
            <div class="hidden sm:block">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- My Orders -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">5</h4>
                    <p class="text-sm text-gray-600">Order Saya</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Completed Today -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">2</h4>
                    <p class="text-sm text-gray-600">Selesai Hari Ini</p>
                </div>
            </div>
        </x-ui.card>

        <!-- In Progress -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">2</h4>
                    <p class="text-sm text-gray-600">Sedang Dikerjakan</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Pending -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">1</h4>
                    <p class="text-sm text-gray-600">Menunggu</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- My Tasks Table -->
    <x-ui.card title="Tugas Saya" description="Daftar tugas yang perlu diselesaikan">
        <x-ui.responsive-table 
            :headers="['Task ID', 'Jenis Pekerjaan', 'Material', 'Target', 'Progress', 'Deadline', 'Status', 'Aksi']"
            :rows="[
                [
                    'TSK-001',
                    'Assembly',
                    'Rangka Kursi Type-A',
                    '25 Unit',
                    '<div class=\"w-full bg-gray-200 rounded-full h-2.5\"><div class=\"bg-blue-600 h-2.5 rounded-full\" style=\"width: 80%\"></div></div><span class=\"text-xs text-gray-600 mt-1 block\">80%</span>',
                    '2025-08-20',
                    '<span class=\"bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">In Progress</span>',
                    '<button class=\"bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded-md transition-colors\">Update</button>'
                ],
                [
                    'TSK-002',
                    'Quality Check',
                    'Panel Pintu Finishing',
                    '50 Unit',
                    '<div class=\"w-full bg-gray-200 rounded-full h-2.5\"><div class=\"bg-green-600 h-2.5 rounded-full\" style=\"width: 100%\"></div></div><span class=\"text-xs text-gray-600 mt-1 block\">100%</span>',
                    '2025-08-18',
                    '<span class=\"bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">Completed</span>',
                    '<button class=\"bg-gray-400 text-white text-xs px-3 py-1 rounded-md cursor-not-allowed\" disabled>Done</button>'
                ],
                [
                    'TSK-003',
                    'Packaging',
                    'Meja Kerja Eksekutif',
                    '10 Unit',
                    '<div class=\"w-full bg-gray-200 rounded-full h-2.5\"><div class=\"bg-yellow-600 h-2.5 rounded-full\" style=\"width: 30%\"></div></div><span class=\"text-xs text-gray-600 mt-1 block\">30%</span>',
                    '2025-08-22',
                    '<span class=\"bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">In Progress</span>',
                    '<button class=\"bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded-md transition-colors\">Update</button>'
                ],
                [
                    'TSK-004',
                    'Raw Material Check',
                    'Kayu Jati Premium',
                    '100 Kubik',
                    '<div class=\"w-full bg-gray-200 rounded-full h-2.5\"><div class=\"bg-gray-400 h-2.5 rounded-full\" style=\"width: 0%\"></div></div><span class=\"text-xs text-gray-600 mt-1 block\">0%</span>',
                    '2025-08-25',
                    '<span class=\"bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">Pending</span>',
                    '<button class=\"bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-md transition-colors\">Mulai</button>'
                ]
            ]"
            hoverable
            striped />
    </x-ui.card>

    <!-- Recent Activity -->
    <x-ui.card title="Aktivitas Terbaru" description="Log aktivitas terbaru Anda">
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900"><strong>Task TSK-002</strong> telah diselesaikan</p>
                    <p class="text-xs text-gray-500">2 jam yang lalu</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">Progress <strong>TSK-001</strong> diperbarui ke 80%</p>
                    <p class="text-xs text-gray-500">4 jam yang lalu</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900"><strong>TSK-003</strong> dimulai</p>
                    <p class="text-xs text-gray-500">1 hari yang lalu</p>
                </div>
            </div>
        </div>
    </x-ui.card>
</x-layouts.app>