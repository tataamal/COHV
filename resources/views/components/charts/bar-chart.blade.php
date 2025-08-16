@props([
    'chartId',
    'labels' => [],
    'datasets' => [],
    'height' => 'h-64 sm:h-72',
    'title' => null
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
    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
    
    const chartData = {
        labels: @json($labels),
        datasets: @json($datasets)
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#e5e7eb' },
                    ticks: {
                        font: { size: window.innerWidth < 640 ? 10 : 12 }
                    }
                },
                x: { 
                    grid: { display: false },
                    ticks: {
                        font: { size: window.innerWidth < 640 ? 10 : 12 },
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            if (window.innerWidth < 640) {
                                return label.replace('Order ', '');
                            }
                            return label;
                        }
                    }
                }
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        font: { size: window.innerWidth < 640 ? 10 : 12 },
                        padding: window.innerWidth < 640 ? 10 : 20
                    }
                },
                tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false }
        }
    });
    
    // Handle window resize for chart responsiveness
    window.addEventListener('resize', function() {
        const chart = Chart.getChart(ctx);
        if (chart) {
            chart.options.scales.x.ticks.font.size = window.innerWidth < 640 ? 10 : 12;
            chart.options.scales.y.ticks.font.size = window.innerWidth < 640 ? 10 : 12;
            chart.options.plugins.legend.labels.font.size = window.innerWidth < 640 ? 10 : 12;
            chart.options.plugins.legend.labels.padding = window.innerWidth < 640 ? 10 : 20;
            chart.update();
        }
    });
});
</script>
@endpush