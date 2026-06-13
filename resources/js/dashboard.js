// resources/js/dashboard.js

(function() {
    if (window.__DASHBOARD_CHARTS_READY) return;
    
    const dataDiv = document.getElementById('dashboard-data');
    if (!dataDiv) return;

    // --- Leer datos ---
    let deptLabels = [];
    let deptCounts = [];
    let statusCounts = { Activo: 0, Inactivo: 0, Reposo: 0 };
    let attendanceLabels = [];
    let attendanceData = [];

    try {
        deptLabels = JSON.parse(dataDiv.dataset.deptLabels || '[]');
        deptCounts = JSON.parse(dataDiv.dataset.deptCounts || '[]');
        statusCounts = JSON.parse(dataDiv.dataset.statusCounts || '{"Activo":0,"Inactivo":0,"Reposo":0}');
        attendanceLabels = JSON.parse(dataDiv.dataset.attendanceLabels || '[]');
        attendanceData = JSON.parse(dataDiv.dataset.attendanceData || '[]');
    } catch (e) {
        console.error('Error parseando datos del dashboard', e);
    }

    // Calcular total de empleados para el centro del donut
    const totalEmployees = (statusCounts['Activo'] || 0) + 
                           (statusCounts['Inactivo'] || 0) + 
                           (statusCounts['Reposo'] || 0);

    // Destruir gráficos previos
    ['deptChart', 'statusChart', 'attendanceChart'].forEach(id => {
        const canvas = document.getElementById(id);
        if (canvas) {
            const existing = Chart.getChart(canvas);
            if (existing) existing.destroy();
        }
    });

    // ========== GRÁFICO DE DEPARTAMENTOS (BARRAS HORIZONTALES) ==========
    const deptCanvas = document.getElementById('deptChart');
    if (deptCanvas) {
        new Chart(deptCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: deptLabels.length ? deptLabels : ['Sin datos'],
                datasets: [{
                    label: 'Empleados',
                    data: deptLabels.length ? deptCounts : [0],
                    backgroundColor: '#0B3B5E',
                    borderColor: '#0B3B5E',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#E5E7EB' }
                    },
                    y: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ========== GRÁFICO DE ESTADO (DONUT CON TOTAL EN EL CENTRO) ==========
    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        // Plugin corregido para texto centrado perfectamente
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                const { ctx, width, height } = chart;
                
                // Guardar estado del contexto
                ctx.save();
                
                // Calcular tamaño de fuente proporcional al canvas
                const baseSize = Math.min(width, height) * 0.1;
                const fontSizeMain = Math.max(20, Math.min(40, baseSize));
                const fontSizeSub = fontSizeMain * 0.45;
                
                // Configurar texto principal (total)
                ctx.font = `600 ${fontSizeMain}px 'Figtree', sans-serif`;
                ctx.textBaseline = 'middle';
                ctx.textAlign = 'center';
                ctx.fillStyle = '#0B3B5E';
                ctx.fillText(totalEmployees, width / 2, height / 2 - fontSizeMain * 0.15);
                
                // Configurar texto secundario ("Empleados")
                ctx.font = `400 ${fontSizeSub}px 'Figtree', sans-serif`;
                ctx.fillStyle = '#6B7280';
                ctx.fillText('Empleados', width / 2, height / 2 + fontSizeMain * 0.4);
                
                // Restaurar estado del contexto
                ctx.restore();
            }
        };

        new Chart(statusCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Inactivos', 'Reposo'],
                datasets: [{
                    data: [
                        statusCounts['Activo'] || 0,
                        statusCounts['Inactivo'] || 0,
                        statusCounts['Reposo'] || 0
                    ],
                    backgroundColor: [
                        '#0B3B5E',   // Azul institucional (activos)
                        '#6B7280',   // Gris (inactivos)
                        '#C1272D'    // Rojo institucional (reposo)
                    ],
                    borderWidth: 0,
                    borderRadius: 4,
                    spacing: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            font: { family: "'Figtree', sans-serif", size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = totalEmployees > 0 ? ((value / totalEmployees) * 100).toFixed(1) : '0.0';
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [centerTextPlugin]
        });
    }

    // ========== GRÁFICO DE ASISTENCIAS (LÍNEA) ==========
    const attCanvas = document.getElementById('attendanceChart');
    if (attCanvas) {
        new Chart(attCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: attendanceLabels.length ? attendanceLabels : ['Sin datos'],
                datasets: [{
                    label: 'Asistencias',
                    data: attendanceLabels.length ? attendanceData : [0],
                    borderColor: '#F97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#F97316',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E5E7EB' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: '#0B3B5E',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF'
                    }
                }
            }
        });
    }

    window.__DASHBOARD_CHARTS_READY = true;
})();