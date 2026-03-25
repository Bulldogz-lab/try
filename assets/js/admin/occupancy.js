// Expect these variables from PHP (we will define them in the page)
const {
    propNames,
    propRates,
    propColours,
    donutData,
    unitsByProp
} = window.occupancyData;

// ── bar chart ─────────────────────────────────────────
new Chart(document.getElementById('occBarChart'), {
    type: 'bar',
    data: {
        labels: propNames,
        datasets: [{
            label: 'Occupancy %',
            data: propRates,
            backgroundColor: propColours,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                min: 0,
                max: 100,
                grid: { color: 'rgba(0,0,0,.04)' },
                ticks: { callback: v => v + '%', font: { size: 11 } }
            }
        }
    }
});

// ── donut chart ───────────────────────────────────────
new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Occupied', 'Vacant', 'Maintenance'],
        datasets: [{
            data: donutData,
            backgroundColor: ['#2ECC71', '#94a3b8', '#deaf37'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});

// ── drilldown ─────────────────────────────────────────
function openDrilldown(pid, name) {
    const units = unitsByProp[pid] || [];
    document.getElementById('drillTitle').textContent = name + ' — Units';

    const occ = units.filter(u => u.status === 'occupied').length;
    const vac = units.filter(u => u.status === 'vacant').length;
    const mnt = units.filter(u => u.status === 'maintenance').length;
    const rate = units.length ? Math.round(occ / units.length * 100) : 0;

    document.getElementById('drillStats').innerHTML = `
        <div style="background:#dcfce7;border-radius:8px;padding:10px 16px;font-size:12px;">
            <div style="font-weight:700;font-size:18px;color:#16a34a;">${rate}%</div>
            <div style="color:#166534;">Occupancy</div>
        </div>
        <div style="background:#eff6ff;border-radius:8px;padding:10px 16px;font-size:12px;">
            <div style="font-weight:700;font-size:18px;color:#2563c4;">${occ}</div>
            <div style="color:#1d4ed8;">Occupied</div>
        </div>
        <div style="background:#f1f5f9;border-radius:8px;padding:10px 16px;font-size:12px;">
            <div style="font-weight:700;font-size:18px;color:#64748b;">${vac}</div>
            <div style="color:#475569;">Vacant</div>
        </div>
        ${mnt > 0 ? `<div style="background:#fef9c3;border-radius:8px;padding:10px 16px;font-size:12px;">
            <div style="font-weight:700;font-size:18px;color:#b45309;">${mnt}</div>
            <div style="color:#92400e;">Maintenance</div>
        </div>` : ''}
    `;

    document.getElementById('drillGrid').innerHTML = units.map(u => {
        const bs = u.status === 'occupied'
            ? 'background:#dcfce7;color:#16a34a;'
            : u.status === 'maintenance'
                ? 'background:#fef9c3;color:#b45309;'
                : 'background:#f1f5f9;color:#64748b;';

        const label = ((u.unit_number || '') + ' ' + (u.unit_name || '')).trim() || '—';

        return `
        <div class="unit-card">
            <div class="un">${label}</div>
            <div class="ut">${u.unit_type || '—'} · Floor ${u.floor || '—'}</div>
            <span class="us" style="${bs}">
                ${(u.status || 'vacant').charAt(0).toUpperCase() + (u.status || 'vacant').slice(1)}
            </span>
            <div class="ur">₱ ${parseInt(u.rent_amount || 0).toLocaleString()}</div>
            ${u.tenant_name ? `<div style="font-size:11px;color:#94a3b8;margin-top:3px;">👤 ${u.tenant_name}</div>` : ''}
        </div>`;
    }).join('') || '<p>No units found.</p>';

    document.getElementById('drillModal').classList.add('open');
}

function closeDrill() {
    document.getElementById('drillModal').classList.remove('open');
}

document.getElementById('drillModal').addEventListener('click', e => {
    if (e.target.id === 'drillModal') closeDrill();
});

// ── filters ───────────────────────────────────────────
function filterProperty(pid) {
    document.querySelectorAll('.property-row').forEach(row => {
        row.style.opacity = (!pid || row.dataset.pid == pid) ? '1' : '0.3';
    });
    filterTable();
}

function filterTable() {
    const status = document.getElementById('statusFilter').value.toLowerCase();
    const type = document.getElementById('typeFilter').value.toLowerCase();
    const search = document.getElementById('unitSearch').value.toLowerCase();
    const propPid = document.getElementById('propertyFilter').value;

    let visible = 0;

    document.querySelectorAll('#unitsTable tbody tr').forEach(row => {
        const ok =
            (!status || row.dataset.status === status) &&
            (!type || row.dataset.type.toLowerCase() === type) &&
            (!propPid || row.dataset.pid == propPid) &&
            (!search || row.innerText.toLowerCase().includes(search));

        row.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });

    document.getElementById('visibleCount').textContent = visible;
}

// expose to global (important for onclick in HTML)
window.openDrilldown = openDrilldown;
window.closeDrill = closeDrill;
window.filterProperty = filterProperty;
window.filterTable = filterTable;