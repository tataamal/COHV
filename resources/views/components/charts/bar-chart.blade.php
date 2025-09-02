@props([
    'chartId' => 'myChart',
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => 'h-96'
])

<div class="{{ $height }}">
    <canvas id="{{ $chartId }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
        
        new Chart(ctx, {
            type: '{{ $type }}',
            data: {
                labels: @json($labels),
                datasets: @json($datasets)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12
                            },
                            color: '#6B7280',
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                        },
                        padding: 12,
                        cornerRadius: 8,
                        boxPadding: 4
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB',
                            borderDash: [3, 3]
                        },
                        ticks: {
                             font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                            },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                             font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                            },
                            color: '#6B7280'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush