<?php
$page_title = 'Booking Reports';
$active_page = 'booking_reports';
include '../../includes/session.php';
include '../../includes/layout_open.php';
?>

<style>
  .live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #16a34a;
    background: #dcfce7;
    border-radius: 20px;
    padding: 3px 10px;
    letter-spacing: .03em;
    user-select: none;
  }

  .live-badge .dot {
    width: 7px;
    height: 7px;
    background: #16a34a;
    border-radius: 50%;
    animation: pulse-dot 1.6s ease-in-out infinite;
  }

  @keyframes pulse-dot {

    0%,
    100% {
      opacity: 1;
      transform: scale(1);
    }

    50% {
      opacity: .4;
      transform: scale(.7);
    }
  }

  .filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .range-btn {
    padding: 7px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 600;
    background: var(--white);
    color: var(--text-soft);
    cursor: pointer;
    transition: all .18s;
  }

  .range-btn:hover {
    border-color: #2563c4;
    color: #2563c4;
  }

  .range-btn.active {
    background: #2563c4;
    border-color: #2563c4;
    color: #fff;
  }

  @keyframes val-flash {
    0% {
      color: #2563c4;
    }

    100% {
      color: inherit;
    }
  }

  .flash {
    animation: val-flash .9s ease-out;
  }

  @keyframes row-flash {
    0% {
      background: #dbeafe;
    }

    100% {
      background: transparent;
    }
  }

  .row-new {
    animation: row-flash 1s ease-out;
  }

  #last-updated {
    font-size: 11px;
    color: var(--text-soft);
    white-space: nowrap;
  }
</style>

<div class="page-header">
  <div class="top-header">
    <h2>Booking Reports <span class="live-badge"><span class="dot"></span>LIVE</span></h2>
    <div class="page-header-sub">Reservation trends, cancellations, and booking analytics</div>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <span id="last-updated"></span>
    <div class="filter-bar" id="range-filter">
      <button class="range-btn active" data-range="30">Last 30 days</button>
      <button class="range-btn" data-range="60">Last 60 days</button>
      <button class="range-btn" data-range="365">This year</button>
    </div>
    <button class="btn btn-secondary" id="export-btn">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="7 10 12 15 17 10" />
        <line x1="12" y1="15" x2="12" y2="3" />
      </svg>Export
    </button>
  </div>
</div>

<div class="page-inner">
  <div class="cards-area">

    <div class="stat-row">
      <div class="stat-card sc-blue">
        <div class="stat-card-left">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value" id="s-total">—</div>
          <span class="stat-trend" id="s-trend">—</span>
          <div class="stat-sub" id="s-period">Last 30 days</div>
        </div>
        <div class="stat-icon-wrap blue">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-green">
        <div class="stat-card-left">
          <div class="stat-label">Confirmed</div>
          <div class="stat-value" id="s-confirmed">—</div>
          <span class="stat-trend" id="s-conv">—</span>
          <div class="stat-sub">Conversion rate</div>
        </div>
        <div class="stat-icon-wrap green">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-red">
        <div class="stat-card-left">
          <div class="stat-label">Cancelled</div>
          <div class="stat-value" id="s-cancelled">—</div>
          <span class="stat-trend" id="s-cxrate">—</span>
          <div class="stat-sub">Cancellation rate</div>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-gold">
        <div class="stat-card-left">
          <div class="stat-label">Avg. Stay</div>
          <div class="stat-value" id="s-avgstay">—</div>
          <span class="stat-trend neutral">–</span>
          <div class="stat-sub">Nights per booking</div>
        </div>
        <div class="stat-icon-wrap gold">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
          </svg>
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card" style="flex:2;">
        <div class="card-header"><span class="card-title">Monthly Booking Volume</span></div>
        <div class="chart-wrap" style="height:220px;"><canvas id="bookingVolChart"></canvas></div>
      </div>
      <div class="card" style="flex:1;">
        <div class="card-header"><span class="card-title">Booking Status</span></div>
        <div style="display:flex;flex-direction:column;align-items:center;gap:14px;">
          <div class="chart-wrap" style="height:160px;width:160px;"><canvas id="statusDonut"></canvas></div>
          <div class="legend-list" style="width:100%;">
            <div class="legend-item">
              <div class="legend-dot" style="background:#2ECC71;"></div>
              <span class="legend-label">Confirmed</span>
              <span class="legend-val" id="leg-confirmed">—</span>
              <span class="legend-pct" id="leg-confirmed-pct"></span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#deaf37;"></div>
              <span class="legend-label">Pending</span>
              <span class="legend-val" id="leg-pending">—</span>
              <span class="legend-pct" id="leg-pending-pct"></span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#E74C3C;"></div>
              <span class="legend-label">Cancelled</span>
              <span class="legend-val" id="leg-cancelled">—</span>
              <span class="legend-pct" id="leg-cancelled-pct"></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-header"><span class="card-title">Bookings by Source</span></div>
        <div class="chart-wrap" style="height:200px;"><canvas id="sourceBar"></canvas></div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">Top Booked Units</span></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Unit</th>
                <th>Property</th>
                <th>Bookings</th>
                <th>Rate</th>
              </tr>
            </thead>
            <tbody id="units-tbody">
              <tr>
                <td colspan="5" style="text-align:center;color:var(--text-soft);padding:24px;">Loading…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  /* ═══════════════════════════════════════════════════════════════════
     BOOKING REPORTS
     • Initial load  → fetch() → booking_reports_api.php   (instant)
     • Filter change → fetch() → booking_reports_api.php   (instant)
     • Live updates  → SSE    → booking_reports_stream.php (push only)
     ═══════════════════════════════════════════════════════════════════ */

  const API = 'booking_reports_api.php';
  const STREAM = 'booking_reports_stream.php';

  let currentRange = 30;
  let es = null;   // active EventSource

  // ── Charts ────────────────────────────────────────────────
  const C = {
    blue: 'rgba(37,99,196,0.85)',
    green: 'rgba(46,204,113,0.80)',
    red: 'rgba(231,76,60,0.80)',
    src: ['#2563c4', '#2ECC71', '#deaf37', '#93c5fd', '#dbeafe', '#fca5a5'],
  };

  const volChart = new Chart(document.getElementById('bookingVolChart'), {
    type: 'bar',
    data: {
      labels: [], datasets: [
        { label: 'New Bookings', data: [], backgroundColor: C.blue, borderRadius: 5 },
        { label: 'Confirmed', data: [], backgroundColor: C.green, borderRadius: 5 },
        { label: 'Cancelled', data: [], backgroundColor: C.red, borderRadius: 5 },
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      animation: { duration: 400 },
      plugins: { legend: { position: 'left', labels: { usePointStyle: true, font: { size: 11 } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 11 } } }
      }
    }
  });

  const donutChart = new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: {
      labels: ['Confirmed', 'Pending', 'Cancelled'],
      datasets: [{ data: [0, 0, 0], backgroundColor: ['#2ECC71', '#deaf37', '#E74C3C'], borderWidth: 0, hoverOffset: 8 }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', animation: { duration: 400 }, plugins: { legend: { display: false } } }
  });

  const sourceChart = new Chart(document.getElementById('sourceBar'), {
    type: 'bar',
    data: { labels: [], datasets: [{ data: [], backgroundColor: C.src, borderRadius: 6, borderSkipped: false }] },
    options: {
      indexAxis: 'y', responsive: true, maintainAspectRatio: false, animation: { duration: 400 },
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.x + ' bookings' } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' } } }
      }
    }
  });

  // ── Fetch: used for initial load + filter changes ─────────
  async function loadData(flash) {
    try {
      const res = await fetch(`${API}?range=${currentRange}&_=${Date.now()}`);
      if (!res.ok) throw new Error(res.status);
      const data = await res.json();
      if (data.error) throw new Error(data.error);
      render(data, flash);
    } catch (e) {
      console.error('Booking Reports fetch error:', e);
    }
  }

  // ── SSE: used only for live push after initial load ───────
  function connectSSE() {
    if (es) { es.close(); es = null; }

    es = new EventSource(`${STREAM}?range=${currentRange}`);

    es.addEventListener('update', e => {
      try {
        const data = JSON.parse(e.data);
        render(data, true);   // flash = true, this is new live data
      } catch (err) {
        console.error('SSE parse error:', err);
      }
    });

    // onerror left default — browser auto-reconnects EventSource
  }

  // ── Render all widgets ────────────────────────────────────
  function render(d, flash) {
    renderStats(d.stats, flash);
    renderDonut(d.stats);
    renderVol(d.monthly);
    renderSources(d.sources);
    renderUnits(d.units, flash);

    document.getElementById('last-updated').textContent =
      'Updated ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }

  function renderStats(s, flash) {
    set('s-total', s.total, flash);
    set('s-confirmed', s.confirmed, flash);
    set('s-cancelled', s.cancelled, flash);
    set('s-avgstay', s.avg_stay + ' nights', flash);
    set('s-conv', s.conv_rate + '%');
    set('s-cxrate', s.cancel_rate + '%');
    set('s-period', { 30: 'Last 30 days', 60: 'Last 60 days', 365: 'This year' }[currentRange]);

    const t = document.getElementById('s-trend');
    const up = s.booking_trend >= 0;
    t.textContent = (up ? '↑ ' : '↓ ') + Math.abs(s.booking_trend) + '%';
    t.className = 'stat-trend ' + (up ? 'up' : 'down');
  }

  function renderDonut(s) {
    const tot = s.total || 1;
    donutChart.data.datasets[0].data = [s.confirmed, s.pending, s.cancelled];
    donutChart.update();
    set('leg-confirmed', s.confirmed);
    set('leg-pending', s.pending);
    set('leg-cancelled', s.cancelled);
    set('leg-confirmed-pct', '(' + pct(s.confirmed, tot) + '%)');
    set('leg-pending-pct', '(' + pct(s.pending, tot) + '%)');
    set('leg-cancelled-pct', '(' + pct(s.cancelled, tot) + '%)');
  }

  function renderVol(m) {
    volChart.data.labels = m.labels;
    volChart.data.datasets[0].data = m.new;
    volChart.data.datasets[1].data = m.confirmed;
    volChart.data.datasets[2].data = m.cancelled;
    volChart.update();
  }

  function renderSources(s) {
    sourceChart.data.labels = s.labels;
    sourceChart.data.datasets[0].data = s.data;
    sourceChart.data.datasets[0].backgroundColor = C.src.slice(0, s.labels.length);
    sourceChart.update();
  }

  function renderUnits(units, flash) {
    const tbody = document.getElementById('units-tbody');
    if (!units || !units.length) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-soft);padding:24px;">No data for this period</td></tr>';
      return;
    }
    tbody.innerHTML = units.map(u => `
    <tr class="${flash ? 'row-new' : ''}">
      <td style="color:var(--text-soft);font-weight:700;">${u.rank}</td>
      <td><strong>${esc(u.unit)}</strong></td>
      <td style="font-size:12px;color:var(--text-soft);">${esc(u.property)}</td>
      <td>${u.bookings}</td>
      <td><span class="badge badge-${u.badge}">${esc(u.rate)}</span></td>
    </tr>`).join('');
  }

  // ── Filter buttons ────────────────────────────────────────
  document.getElementById('range-filter').addEventListener('click', e => {
    const btn = e.target.closest('.range-btn');
    if (!btn) return;
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentRange = parseInt(btn.dataset.range, 10);

    // 1. Fetch fresh data immediately (no reload, no wait)
    loadData(false);

    // 2. Reconnect SSE for the new range so live updates use the right range
    connectSSE();
  });

  // ── Export ────────────────────────────────────────────────
  document.getElementById('export-btn').addEventListener('click', () => {
    window.location.href = `${API}?range=${currentRange}&export=1`;
  });

  // ── Helpers ───────────────────────────────────────────────
  function set(id, val, flash = false) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = val;
    if (flash) {
      el.classList.remove('flash');
      void el.offsetWidth;
      el.classList.add('flash');
    }
  }
  function pct(n, t) { return Math.round(n / t * 100); }
  function esc(s) {
    return String(s).replace(/[&<>"']/g,
      c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }

  // ── Boot ──────────────────────────────────────────────────
  loadData(false);   // instant data on page open
  connectSSE();      // open SSE stream for live push updates
</script>

<?php include '../../includes/layout_close.php'; ?>