document.addEventListener('DOMContentLoaded', function() {
    const dataEl = document.getElementById('dashboard-data');
    if (!dataEl) return;

    // Detectar modo oscuro
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(0, 0, 0, 0.05)';

    // Colores Institucionales
    const pcBlue = '#0B3B5E';
    const pcOrange = '#F97316';
    const pcRed = '#E63946';
    const pcGreen = '#22C55E';

    // 1. Gráfico de Departamentos (Dona)
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx) {
        new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: JSON.parse(dataEl.dataset.deptLabels),
                datasets: [{
                    data: JSON.parse(dataEl.dataset.deptCounts),
                    backgroundColor: [pcBlue, pcOrange, pcGreen, pcRed, '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6'],
                    borderWidth: 2,
                    borderColor: isDark ? '#0f172a' : '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : pcBlue,
                        titleFont: { size: 10, weight: 'bold' },
                        bodyFont: { size: 10 },
                        padding: 8,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        boxPadding: 4
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 2. Gráfico de Estado (Pie)
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusData = JSON.parse(dataEl.dataset.statusCounts);
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: [pcGreen, pcRed, pcOrange],
                    borderWidth: 2,
                    borderColor: isDark ? '#0f172a' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            usePointStyle: true, 
                            padding: 20,
                            color: textColor,
                            font: { size: 9, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" } 
                        } 
                    }
                }
            }
        });
    }

    // 3. Gráfico de Asistencias (Línea suavizada con gradiente)
    const attCtx = document.getElementById('attendanceChart');
    if (attCtx) {
        const ctx = attCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, isDark ? 'rgba(249, 115, 22, 0.2)' : 'rgba(11, 59, 94, 0.2)');
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

        new Chart(attCtx, {
            type: 'line',
            data: {
                labels: JSON.parse(dataEl.dataset.attendanceLabels),
                datasets: [{
                    label: 'Personal Presente',
                    data: JSON.parse(dataEl.dataset.attendanceData),
                    borderColor: isDark ? pcOrange : pcBlue,
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: pcOrange,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor }, 
                        ticks: { color: textColor, font: { size: 9, family: "'Plus Jakarta Sans', sans-serif" } } 
                    },
                    x: { 
                        grid: { display: false }, 
                        ticks: { color: textColor, font: { size: 9, family: "'Plus Jakarta Sans', sans-serif" } } 
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#0B3B5E',
                        titleFont: { size: 10, weight: 'bold' },
                        bodyFont: { size: 10 },
                        padding: 12,
                        borderRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    }
});
