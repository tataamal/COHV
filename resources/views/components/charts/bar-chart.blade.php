@props([
  'chartId' => 'myChart',
  'type' => 'bar',
  'labels' => [],
  'datasets' => [],
  'height' => 'h-96'
])

<div class="{{ $height }}">
  <canvas id="{{ $chartId }}" class="w-full h-full"></canvas>
</div>

@push('scripts')
<script>
(function () {
  const id = '{{ $chartId }}';
  const type = @json($type ?? 'bar');           // tidak mengubah data
  const isPieLike = ['pie','doughnut'].includes(type);

  function makeChart() {
    const canvas = document.getElementById(id);
    if (!canvas) return;

    // Bersihkan instance lama bila ada (mis. re-render)
    if (canvas._chartInstance) {
      try { canvas._chartInstance.destroy(); } catch(_) {}
    }

    const ctx = canvas.getContext('2d');

    // Opsi dasar (UI saja)
    const baseOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
            color: '#6B7280',
            padding: 20
          }
        },
        tooltip: {
          backgroundColor: '#111827',
          titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
          bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
          padding: 12,
          cornerRadius: 8,
          boxPadding: 4
        }
      }
    };

    const cartesianScales = {
      y: {
        beginAtZero: true,
        grid: { color: '#E5E7EB', borderDash: [3,3] },
        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" }, color: '#6B7280' }
      },
      x: {
        grid: { display: false },
        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" }, color: '#6B7280' }
      }
    };

    const options = isPieLike
      ? {
          ...baseOptions,
          // Pie/Doughnut tidak pakai axis/ticks/grid
          scales: undefined,
          // Hilangkan garis irisan
          elements: { arc: { borderWidth: 0 } }
          // cutout: '60%', // <- aktifkan jika ingin doughnut lebih tipis
        }
      : {
          ...baseOptions,
          scales: cartesianScales
        };

    const chart = new Chart(ctx, {
      type,
      data: {
        labels: @json($labels),     // DATA TIDAK DIUBAH
        datasets: @json($datasets)  // DATA TIDAK DIUBAH
      },
      options
    });

    canvas._chartInstance = chart;
  }

  // Pastikan Chart.js sudah siap & DOM siap
  const start = () => (window.Chart ? makeChart() : window.addEventListener('load', makeChart, { once: true }));
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
  } else {
    start();
  }
})();
</script>
@endpush