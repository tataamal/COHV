<x-layouts.app title="Dashboard Admin">
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData2 }}</h4>
                    <p class="text-sm text-gray-600">Total Outstanding Order</p>
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
                    <h4 class="text-lg font-semibold text-gray-900">{{ $TData1 }}</h4>
                    <p class="text-sm text-gray-600">Total PRO</p>
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
                    <h4 class="text-lg font-semibold text-gray-900">{{ $outstandingReservasi }}</h4>
                    <p class="text-sm text-gray-600">Total Outstanding Reservasi</p>
                </div>
            </div>
        </x-ui.card>
    </div>
    <!-- Chart Section -->
    <x-ui.card title="Data Kapasitas Workcenter" description="Jumlah PRO (AUFNR) di tiap-tiap Workcenter (ARBPL)">
        
        {{-- Hapus tag <x-charts.bar-chart> yang salah di sini --}}

        <div class="flex flex-wrap -mx-4">

            {{-- Chart pertama: Bar Chart (mengambil 2/3 atau col-8) --}}
            <div class="w-full lg:w-2/3 px-4 mb-4 lg:mb-0">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <x-charts.bar-chart
                        chartId="workcenterChart"
                        :labels="$labels"
                        :datasets="$datasets "
                        title="Jumlah PRO & Kapasitas per Workcenter"
                        height="h-96"
                    />
                </div>
            </div>

            {{-- Chart kedua: Doughnut Chart (mengambil 1/3 atau col-4) --}}
            <div class="w-full lg:w-1/3 px-4">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <x-charts.bar-chart 
                        chartId="proStatusChart"
                        type="doughnut"  {{-- Tentukan tipe chart --}}
                        :labels="$doughnutChartLabels"
                        :datasets="$doughnutChartDatasets"
                        title="Perbandingan PRO Status REL per Plant"
                        height="h-96"
                    />
                </div>
            </div>

        </div>

        {{-- Hapus tag </x-charts.bar-chart> yang salah di sini --}}

    </x-ui.card>
</x-layouts.app>