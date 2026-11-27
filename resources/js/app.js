import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', async () => {
    const canvases = document.querySelectorAll('[data-dashboard-chart]');

    if (canvases.length === 0) {
        return;
    }

    const { default: Chart } = await import('chart.js/auto');

    canvases.forEach((canvas) => {
        const { type, labels, values, label } = JSON.parse(canvas.dataset.dashboardChart);
        const colors = ['#2E2E8F', '#6265DC', '#8A8CF4', '#B2B4FF', '#D3D4FF', '#A78BFA'];
        const isDoughnut = type === 'doughnut';
        const isBar = type === 'bar';
        const color = isBar ? colors[1] : colors[0];

        new Chart(canvas, {
            type: type === 'area' ? 'line' : type,
            data: {
                labels,
                datasets: [{
                    label,
                    data: values,
                    borderColor: color,
                    backgroundColor: isDoughnut
                        ? labels.map((_, index) => colors[index % colors.length])
                        : isBar
                            ? 'rgba(98, 101, 220, 0.75)'
                            : type === 'area'
                                ? 'rgba(46, 46, 143, 0.14)'
                                : color,
                    fill: type === 'area',
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: type === 'line' ? 3 : 0,
                    borderRadius: isBar ? 8 : 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: isDoughnut, position: 'bottom' },
                },
                scales: isDoughnut ? {} : {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } },
                },
            },
        });
    });
});
