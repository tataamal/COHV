<x-layouts.app title="Dashboard Admin">
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Jumlah T-Data 1 -->
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
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData1 }}</h4>
                    <p class="text-sm text-gray-600">Jumlah T-Data 1</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Jumlah T-Data 2 -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData2 }}</h4>
                    <p class="text-sm text-gray-600">Jumlah T-Data 2</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Pending Orders -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData3 }}</h4>
                    <p class="text-sm text-gray-600">Jumlah T-Data 3</p>
                </div>
            </div>
        </x-ui.card>

        <!-- T-DATA 4 -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData4 }}</h4>
                    <p class="text-sm text-gray-600">Jumlah T-Data 4</p>
                </div>
            </div>
        </x-ui.card>
    </div>
    <!-- Chart Section -->
    <x-ui.card title="Analisis Kuantitas Order (T3 Data)" description="Menampilkan perbandingan target vs konfirmasi order">
        <x-charts.bar-chart 
            chart-id="t3Chart"
            :labels="['Order 1002345', 'Order 1002346', 'Order 1002347']"
            :datasets="[
                [
                    'label' => 'Qty Target (PSMNG)',
                    'data' => [150, 200, 50],
                    'backgroundColor' => 'rgba(148, 163, 184, 0.6)',
                    'borderColor' => 'rgba(71, 85, 105, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 4
                ],
                [
                    'label' => 'Qty Konfirmasi (WEMNG)',
                    'data' => [150, 120, 0],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgba(37, 99, 235, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 4
                ]
            ]" />
    </x-ui.card>

    <!-- Table Section -->
    <x-ui.card title="Data Table T3" description="Menampulkan data order manufaktur terkini">
        <x-ui.responsive-table 
            :headers="['Order', 'Material', 'Deskripsi', 'Qty Target', 'Qty Konfirmasi', 'Sisa', 'Status']"
            :rows="[
                [
                    '1002345',
                    'RM-00123',
                    'Rangka Kayu Jati Utama',
                    '150.000',
                    '<span class=\"text-green-600 font-semibold\">150.000</span>',
                    '0.000',
                    '<span class=\"bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">Completed</span>'
                ],
                [
                    '1002346',
                    'SF-00567',
                    'Panel Pintu Finishing',
                    '200.000',
                    '<span class=\"text-blue-600 font-semibold\">120.000</span>',
                    '<span class=\"text-red-600 font-semibold\">80.000</span>',
                    '<span class=\"bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">In Progress</span>'
                ],
                [
                    '1002347',
                    'FG-00890',
                    'Kursi Rakit Siap Jual',
                    '50.000',
                    '0.000',
                    '<span class=\"text-red-600 font-semibold\">50.000</span>',
                    '<span class=\"bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full\">Pending</span>'
                ]
            ]"
            hoverable
            striped />
    </x-ui.card>
</x-layouts.app>