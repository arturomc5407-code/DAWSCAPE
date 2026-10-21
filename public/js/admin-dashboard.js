let myChart = null;

// Buscador reactivo
function filterTable() {
    let filter = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll(".team-row");

    rows.forEach(row => {
        let textContent = row.innerText.toLowerCase();
        row.style.display = textContent.includes(filter) ? "" : "none";
    });
}

async function refreshDashboard() {
    try {
        const response = await fetch('/admin/api/stats');
        const data = await response.json();

        // 1. Actualizar KPIs
        updateKPIs(data.metrics);

        // 2. Actualizar Tabla
        updateTable(data.partidas);

        // 3. Actualizar Gráfica
        updateChart(data.chart);

        // 4. Actualizar Inscripciones
        const inscripcionesRes = await fetch('/admin/api/inscripciones');
        const inscripcionesData = await inscripcionesRes.json();
        updateKeysInscritos(inscripcionesData);

    } catch (error) {
        console.error("Error refrescando dashboard:", error);
    }
}

function updateKeysInscritos(data) {
    // Actualizar KPIs de inscripción
    const cards = document.querySelectorAll('.kpi-card');
    if (cards.length >= 2) {
        cards[0].querySelector('p').innerText = data.total_inscritos;
        cards[1].querySelector('p').innerText = data.no_iniciados;
    }

    // Actualizar panel de inscripciones pendientes
    const inscripcionesPanel = document.getElementById('inscripciones-panel');
    if (inscripcionesPanel) {
        if (data.no_iniciados > 0) {
            const listContainer = inscripcionesPanel.querySelector('.podium-list');
             if (listContainer) {
                let html = '';
                data.inscripciones_pendientes.forEach(insc => {
                    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
                    html += `
                    <div class="podium-item" style="background: linear-gradient(135deg, #f0f9ff, #ffffff); border-left: 3px solid #3b82f6; display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #e0e7ff; margin: 0;">
                        <div class="pos-badge" style="background: #dbeafe; color: #3b82f6; box-shadow: none;">
                            ${insc.avatar}
                        </div>
                        <div style="flex: 1; min-width: 0; margin: 0 12px;">
                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${insc.nombre_equipo}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                ${insc.correo} | ${insc.clase}
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; white-space: nowrap; margin-right: 8px;">
                            ${insc.tiempo_inscripcion}
                        </div>
                        <form action="/admin/inscripcion/${insc.id}" method="POST" onsubmit="return confirm('¿Eliminar inscripción de ${insc.nombre_equipo}?');" style="margin:0;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-action" title="Eliminar inscripción" style="background: #fee2e2; color: #dc2626;">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>`;
                });
                listContainer.innerHTML = html;
                inscripcionesPanel.style.display = 'block';
             }
        } else {
            // Ocultar panel si no hay inscritos pendientes
            inscripcionesPanel.style.display = 'none';
        }
    }
}

function updateKPIs(metrics) {
    // Buscamos los nodos por su estructura de KPI
    const cards = document.querySelectorAll('.kpi-card');
    // Actualizar solo los que corresponden de las métricas en tiempo real
    if (cards.length >= 4) {
        cards[2].querySelector('p').innerText = metrics.activos;
        cards[3].querySelector('p').innerText = metrics.finalizados;
    }
}

function updateTable(partidas) {
    const tbody = document.querySelector('#mainTable tbody');
    if (!tbody) return;
    const searchInput = document.getElementById("searchInput");
    const searchVal = searchInput ? searchInput.value.toLowerCase() : '';

    // Reconstruimos filas (más seguro para mantener el orden dinámico)
    let newHtml = '';

    if (partidas.length === 0) {
        newHtml = `<tr><td colspan="8" style="text-align:center; padding: 4rem 2rem; color:var(--text-muted);"><i class="ph ph-ghost" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>Sistema vacío.</td></tr>`;
    } else {
        partidas.forEach(p => {
            const isVisible = (p.nombre_equipo + p.email + p.clase).toLowerCase().includes(searchVal);
            const displayStyle = isVisible ? '' : 'style="display:none"';

            const badges = {
                'Prueba 1 Mario': { bg: '#dbeafe', color: '#1d4ed8', icon: 'ph-spinner-gap' },
                'Prueba 2 Yoseth': { bg: '#f3e8ff', color: '#6d28d9', icon: 'ph-spinner-gap' },
                'Prueba 3 Angel': { bg: '#ffedd5', color: '#c2410c', icon: 'ph-spinner-gap' },
                'Prueba 4 Arturo': { bg: '#fee2e2', color: '#b91c1c', icon: 'ph-spinner-gap' },
                'Prueba 5 Eloy': { bg: '#cffafe', color: '#0e7490', icon: 'ph-spinner-gap' },
                'Prueba 6 Migue': { bg: '#fef08a', color: '#a16207', icon: 'ph-spinner-gap' },
                'Prueba 7 Dinamica': { bg: '#dcfce7', color: '#15803d', icon: 'ph-spinner-gap' },
                'Prueba 8': { bg: '#fce7f3', color: '#be185d', icon: 'ph-spinner-gap' },
                'FINALIZADO': { bg: '#d1fae5', color: '#047857', icon: 'ph-check-circle' },
                'FINALIZADO_TIMEOUT': { bg: '#f3f4f6', color: '#374151', icon: 'ph-clock' }
            };
            const badge = badges[p.fase_actual] || { bg: '#f1f5f9', color: '#475569', icon: 'ph-spinner-gap' };
            const stageText = (p.fase_actual === 'FINALIZADO') ? 'COMPLETADO' : ((p.fase_actual === 'FINALIZADO_TIMEOUT') ? 'TIMEOUT' : p.fase_actual.toUpperCase());
            const anim = badge.icon === 'ph-spinner-gap' ? 'style="animation: spin 2s linear infinite;"' : '';
            const statusHtml = `<span class="status-pill" style="background: ${badge.bg}; color: ${badge.color}; font-weight: 600; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px; box-shadow: none;">
                <i class="ph-fill ${badge.icon}" ${anim}></i> ${stageText}
            </span>`;

            let timeHtml = '';
            if (p.fase_actual === 'FINALIZADO' || p.fase_actual === 'FINALIZADO_TIMEOUT') {
                const color = p.fase_actual === 'FINALIZADO' ? 'var(--success-color)' : 'var(--danger-color)';
                timeHtml = `<span style="color:${color}">${Math.floor(p.tiempo_segundos / 60)}m ${Math.floor(p.tiempo_segundos % 60).toString().padStart(2, '0')}s</span>`;
            } else {
                timeHtml = `<span class="text-live">${Math.floor(p.tiempo_live / 60)}m ${Math.floor(p.tiempo_live % 60).toString().padStart(2, '0')}s</span>`;
            }

            const avatarBg = p.color_hex ? p.color_hex : 'linear-gradient(135deg, #60a5fa, #3b82f6)';
            const avatarInner = p.icono
                ? `<i class="ph-fill ${p.icono}"></i>`
                : `<span style="font-weight: bold; font-size: 1.2rem;">${p.avatar}</span>`;
            const avatarHtml = `<div class="avatar" style="background: ${avatarBg}; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; color: white;">${avatarInner}</div>`;

            newHtml += `
            <tr class="team-row" ${displayStyle}>
                <td style="color:var(--text-muted); font-weight:600;">#${p.id}</td>
                <td>
                    <div class="team-cell">
                        ${avatarHtml}
                        <div class="team-meta">
                            <div class="t-name">${p.nombre_equipo}</div>
                            <div class="t-email">${p.email}</div>
                        </div>
                    </div>
                </td>
                <td><span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:0.85rem; font-weight:600; color:var(--text-muted);">${p.clase}</span></td>
                <td>${statusHtml}</td>
                <td style="text-align:center;">
                    ${p.codice
                        ? `<span title="Combinación Secreta" style="font-family:'Courier New',monospace; font-weight:700; color:#b45309; background:#fffbeb; border:1px solid #fcd34d; padding:2px 6px; border-radius:6px; font-size:0.85rem;">${p.codice}</span>`
                        : `<span style="color:var(--text-muted); font-size:1.2rem; font-weight:bold;">—</span>`}
                </td>
                <td class="time-display">${timeHtml}</td>
                <td style="text-align:center; font-weight:600;">
                    ${p.pistas_count > 0
                        ? `<span style="background:#fef08a; color:#854d0e; padding:4px 8px; border-radius:6px; font-size:0.85rem;">💡 ${p.pistas_count}</span>`
                        : `<span style="color:var(--text-muted); font-size:0.85rem;">0</span>`}
                </td>
                <td style="text-align:center;">
                    <form action="/admin/equipo/${p.uuid}" method="POST" onsubmit="return confirm('¿Borrar permanentemente?');" style="margin:0;">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn-action"><i class="ph ph-trash"></i></button>
                    </form>
                </td>
            </tr>`;
        });
    }
    tbody.innerHTML = newHtml;
}

function updateChart(chartData) {
    const chartCanvas = document.getElementById('fasesChart');
    if (!chartCanvas) return;

    if (!myChart) {
        const ctx = chartCanvas.getContext('2d');
        myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels.map(l => l.replace('FINALIZADO_TIMEOUT', 'Timeout').replace('FINALIZADO', 'Éxito')),
                datasets: [{
                    data: chartData.data,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } },
                cutout: '70%'
            }
        });
    } else {
        myChart.data.labels = chartData.labels.map(l => l.replace('FINALIZADO_TIMEOUT', 'Timeout').replace('FINALIZADO', 'Éxito'));
        myChart.data.datasets[0].data = chartData.data;
        myChart.update();
    }
}

// Inicializar y Polling
document.addEventListener('DOMContentLoaded', () => {
    refreshDashboard(); // Carga inicial
    setInterval(refreshDashboard, 8000); // Cada 8 segundos
});
