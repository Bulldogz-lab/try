<?php
$page_title = 'Analytics';
$active_page = 'analytics';

include '../../includes/session.php';
include '../../includes/layout_open.php';
?>
<div class="page-header">
  <div class="top-header">
    <h2>Analytics</h2>
    <div class="page-header-sub">Performance insights across all properties</div>
  </div>

  <div style="display:flex;gap:8px;">
    <select
      style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;background:var(--white);">
      <option>Last 12 Months</option>
      <option>Last 6 Months</option>
      <option>This Year</option>
    </select>
    <button class="btn btn-primary">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="7 10 12 15 17 10" />
        <line x1="12" y1="15" x2="12" y2="3" />
      </svg>
      Export
    </button>
  </div>
</div>

<div class="page-inner">
  <div class="cards-area">

    <div class="stat-row">
      <div class="stat-card sc-green">
        <div class="stat-card-left">
          <div class="stat-label">Total Revenue</div>
          <div class="stat-value">₱ 1.2M <span class="stat-trend up">↑ 14%</span></div>
          <div class="stat-sub">vs last year</div>
        </div>
        <div class="stat-icon-wrap green">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="1" x2="12" y2="23" />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-blue">
        <div class="stat-card-left">
          <div class="stat-label">Avg. Occupancy</div>
          <div class="stat-value">78% <span class="stat-trend up">↑ 5%</span></div>
          <div class="stat-sub">vs last year</div>
        </div>
        <div class="stat-icon-wrap blue">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-gold">
        <div class="stat-card-left">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value">284 <span class="stat-trend up">↑ 22%</span></div>
          <div class="stat-sub">vs last year</div>
        </div>
        <div class="stat-icon-wrap gold">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-red">
        <div class="stat-card-left">
          <div class="stat-label">Cancellation Rate</div>
          <div class="stat-value">4.2% <span class="stat-trend down">↑ 0.8%</span></div>
          <div class="stat-sub">vs last year</div>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
          </svg>
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-header"><span class="card-title">Revenue by Property</span></div>
        <div class="chart-wrap" style="height:220px;"><canvas id="revByPropChart"></canvas></div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">Monthly Occupancy Rate</span></div>
        <div class="chart-wrap" style="height:220px;"><canvas id="monthlyOccChart"></canvas></div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-header"><span class="card-title">Booking Source Breakdown</span></div>
        <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
          <div class="chart-wrap" style="height:180px;width:180px;flex-shrink:0;"><canvas id="sourceDonut"></canvas>
          </div>
          <div class="legend-list" style="flex:1;min-width:140px;">
            <div class="legend-item">
              <div class="legend-dot" style="background:#2563c4;"></div><span class="legend-label">Direct
                Walk-in</span><span class="legend-val">38%</span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#2ECC71;"></div><span class="legend-label">Online
                Portal</span><span class="legend-val">29%</span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#deaf37;"></div><span class="legend-label">Phone /
                Call</span><span class="legend-val">18%</span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#1a3d7c;"></div><span class="legend-label">Agent
                Referral</span><span class="legend-val">10%</span>
            </div>
            <div class="legend-item">
              <div class="legend-dot" style="background:#93c5fd;"></div><span class="legend-label">Repeat
                Guests</span><span class="legend-val">5%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">Revenue Trend (12 months)</span></div>
        <div class="chart-wrap" style="height:180px;"><canvas id="revTrendChart"></canvas></div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const blue = '#2563c4', gold = '#deaf37', grn = '#2ECC71', red = '#E74C3C', soft = '#dbeafe';

  new Chart(document.getElementById('revByPropChart'), { type: 'bar', data: { labels: ['Skyline Apts', 'Downtown Lofts', 'Green Valley', 'Harbor View'], datasets: [{ label: 'Revenue', data: [512400, 432000, 268800, 0], backgroundColor: [blue, grn, gold, '#dbeafe'], borderRadius: 8, borderSkipped: false }] }, options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => '₱ ' + ctx.parsed.x.toLocaleString() } } }, scales: { x: { grid: { display: false }, ticks: { callback: v => '₱' + (v / 1000) + 'K', font: { size: 11 } } }, y: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' } } } } } });
  new Chart(document.getElementById('monthlyOccChart'), { type: 'line', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'], datasets: [{ label: 'Occupancy %', data: [71, 68, 74, 76, 80, 82, 79, 83], borderColor: blue, borderWidth: 2.5, backgroundColor: 'rgba(37,99,196,0.08)', fill: true, tension: .4, pointBackgroundColor: blue, pointRadius: 4, pointHoverRadius: 7 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } }, scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { min: 50, max: 100, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: v => v + '%', font: { size: 11 } } } } } });
  new Chart(document.getElementById('sourceDonut'), { type: 'doughnut', data: { labels: ['Direct', 'Online', 'Phone', 'Agent', 'Repeat'], datasets: [{ data: [38, 29, 18, 10, 5], backgroundColor: [blue, grn, gold, '#1a3d7c', soft], borderWidth: 0, hoverOffset: 8 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } } });
  new Chart(document.getElementById('revTrendChart'), { type: 'line', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], datasets: [{ label: 'Revenue', data: [612, 598, 645, 620, 680, 710, 695, 728, null, null, null, null], borderColor: grn, borderWidth: 2.5, backgroundColor: 'rgba(46,204,113,0.08)', fill: true, tension: .4, pointRadius: 3, spanGaps: false }, { label: 'Projection', data: [null, null, null, null, null, null, null, 728, 745, 760, 780, 800], borderColor: grn, borderWidth: 2, borderDash: [6, 4], backgroundColor: 'transparent', tension: .4, pointRadius: 0, spanGaps: true }] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y ? '₱ ' + ctx.parsed.y + 'K' : '—' } } }, scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: v => v ? '₱' + v + 'K' : '', font: { size: 11 } } } } } });
</script>
<?php include '../../includes/layout_close.php'; ?>