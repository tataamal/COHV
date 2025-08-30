@props([
    'chartId',
    'labels' => [],
    'datasets' => [],
    'height' => 'h-64 sm:h-72',
    'title' => null,
    'type' => 'bar'
])

<div {{ $attributes->merge(['class' => 'relative ' . $height]) }}>
    @if($title)
        <h4 class="text-lg font-medium text-gray-700 mb-4">{{ $title }}</h4>
    @endif
    <canvas id="{{ $chartId }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gunakan variabel unik untuk setiap chart berdasarkan chartId
        const ctx_{{ $chartId }} = document.getElementById('{{ $chartId }}').getContext('2d');
        
        const chartData_{{ $chartId }} = {
            labels: @json($labels),
            datasets: @json($datasets)
        };
        
        // Siapkan opsi dasar yang berlaku untuk semua tipe chart
        const chartOptions_{{ $chartId }} = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        font: { size: window.innerWidth < 640 ? 10 : 12 },
                        padding: window.innerWidth < 640 ? 10 : 20
                    }
                },
                tooltip: { 
                    mode: 'index', 
                    intersect: false 
                }
            },
            interaction: { 
                mode: 'nearest', 
                axis: 'x', 
                intersect: false 
            }
        };
        
        // Tambahkan opsi 'scales' hanya jika tipe chart adalah bar atau line
        if ('{{ $type }}' === 'bar' || '{{ $type }}' === 'line') {
            chartOptions_{{ $chartId }}.scales = {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#e5e7eb' },
                    ticks: { font: { size: window.innerWidth < 640 ? 10 : 12 } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { 
                        font: { size: window.innerWidth < 640 ? 10 : 12 },
                        // Callback untuk memotong teks label di layar kecil (jika perlu)
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            if (window.innerWidth < 640 && label.length > 10) {
                                return label.substring(0, 8) + '...';
                            }
                            return label;
                        }
                    }
                }
            };
        }

        // Buat chart HANYA SATU KALI dengan konfigurasi yang sudah disiapkan
        const chartInstance_{{ $chartId }} = new Chart(ctx_{{ $chartId }}, {
            type: '{{ $type }}',
            data: chartData_{{ $chartId }},
            options: chartOptions_{{ $chartId }}
        });
        
        // Handle window resize untuk chart responsiveness (sudah diperbaiki)
        window.addEventListener('resize', function() {
            if (chartInstance_{{ $chartId }}) {
                const newSize = window.innerWidth < 640 ? 10 : 12;
                const newPadding = window.innerWidth < 640 ? 10 : 20;

                chartInstance_{{ $chartId }}.options.plugins.legend.labels.font.size = newSize;
                chartInstance_{{ $chartId }}.options.plugins.legend.labels.padding = newPadding;
                
                if (chartInstance_{{ $chartId }}.options.scales) {
                    chartInstance_{{ $chartId }}.options.scales.x.ticks.font.size = newSize;
                    chartInstance_{{ $chartId }}.options.scales.y.ticks.font.size = newSize;
                }

                chartInstance_{{ $chartId }}.update();
            }
        });
    });
</script>
@endpush