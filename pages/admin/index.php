<?php
$page_title = 'Dashboard';
$active_page = 'dashboard';
include '../../includes/session.php';

if ($_SESSION['role'] !== 'admin') {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized access!']);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — PropSight</title>
  <link rel="stylesheet" href="../../assets/css/admin-css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>
  <?php include '../../includes/sidebar.php'; ?>
  <div class="main">
    <div class="topbar">
      <div class="search-bar">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        Search anything here
      </div>
    </div>
    <div class="topbar-divider"></div>
    <div class="content">
      <div class="welcome-inline">
        <div class="welcome-avatar-img"></div>
        <div class="welcome-text">
          <p class="welcome-greeting">Welcome back,
            <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?>!</strong>
          </p>
          <p class="welcome-sub">Here's what's happening with your properties today.</p>
        </div>
      </div>
      <div class="page-inner">
        <div class="cards-area">

          <div class="stat-row">
            <div class="stat-card sc-green">
              <div class="stat-card-left">
                <div class="stat-label">Total Revenue (YTD)</div>
                <div class="stat-value">₱ 5.86M <span class="stat-trend up">↑ 14%</span> </div>
                <div class="stat-sub">vs ₱ 5.13M last year</div>
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
                <div class="stat-value">78.6% <span class="stat-trend up">↑ 3%</span></div>
                <div class="stat-sub">66 of 84 units occupied</div>
              </div>
              <div class="stat-icon-wrap blue">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
                </svg>
              </div>
            </div>
            <div class="stat-card sc-gold">
              <div class="stat-card-left">
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value">284 <span class="stat-trend up">↑ 22%</span></div>
                <div class="stat-sub">This year · 12 pending</div>

              </div>
              <div class="stat-icon-wrap gold">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="4" width="18" height="18" rx="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
              </div>
            </div>
            <div class="stat-card sc-red">
              <div class="stat-card-left">
                <div class="stat-label">Cancellation Rate</div>
                <div class="stat-value">4.2% <span class="stat-trend down">↑ 0.8%</span></div>
                <div class="stat-sub">18 cancelled this month</div>
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
            <div class="card" style="flex:2;">
              <div class="card-header">
                <span class="card-title">Monthly Revenue vs Expenses</span>
                <div class="chart-controls">
                  <div class="toggle">
                    <div class="dot income"></div> Revenue
                  </div>
                  <div class="toggle">
                    <div class="dot expense"></div> Expenses
                  </div>
                  <select class="period-select">
                    <option>2024</option>
                    <option>2023</option>
                  </select>
                </div>
              </div>
              <div class="chart-wrap" style="height:200px;"><canvas id="revenueChart"></canvas></div>
            </div>
            <div class="card" style="flex:1;">
              <div class="card-header"><span class="card-title">Occupancy Split</span></div>
              <div style="display:flex;flex-direction:column;align-items:center;gap:16px;">
                <div class="chart-wrap" style="height:150px;width:150px;"><canvas id="occupancyDonut"></canvas></div>
                <div class="legend-list" style="width:100%;">
                  <div class="legend-item">
                    <div class="legend-dot" style="background:var(--blue-400);"></div><span
                      class="legend-label">Occupied</span><span class="legend-val">66</span><span
                      class="legend-pct">(78%)</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-dot" style="background:var(--blue-100);"></div><span
                      class="legend-label">Vacant</span><span class="legend-val">14</span><span
                      class="legend-pct">(17%)</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-dot" style="background:var(--danger);"></div><span
                      class="legend-label">Maintenance</span><span class="legend-val">4</span><span
                      class="legend-pct">(5%)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="two-col">
            <div class="card">
              <div class="card-header"><span class="card-title">Properties</span><a href="properties_list.php"
                  style="font-size:13px;color:var(--blue-400);font-weight:600;text-decoration:none;">View all →</a>
              </div>
              <div class="prop-list">
                <div class="prop-item">
                  <div class="prop-thumb" style="background:var(--blue-50);">🏢</div>
                  <div class="prop-info">
                    <div class="name">Skyline Apartments</div>
                    <div class="addr">12 Oak Street, NYC</div>
                    <div class="prop-bar-wrap">
                      <div class="prop-bar" style="width:75%;background:var(--blue-400);"></div>
                    </div>
                  </div>
                  <div class="prop-score">8/10</div>
                </div>
                <div class="prop-item">
                  <div class="prop-thumb" style="background:#f0fdf4;">🏠</div>
                  <div class="prop-info">
                    <div class="name">Green Valley Homes</div>
                    <div class="addr">45 Palm Ave, LA</div>
                    <div class="prop-bar-wrap">
                      <div class="prop-bar" style="width:60%;background:var(--gold);"></div>
                    </div>
                  </div>
                  <div class="prop-score">6/10</div>
                </div>
                <div class="prop-item">
                  <div class="prop-thumb" style="background:var(--blue-50);">🏬</div>
                  <div class="prop-info">
                    <div class="name">Downtown Lofts</div>
                    <div class="addr">88 Main Blvd, Chicago</div>
                    <div class="prop-bar-wrap">
                      <div class="prop-bar" style="width:90%;background:var(--success);"></div>
                    </div>
                  </div>
                  <div class="prop-score">9/10</div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="task-header"><span class="card-title">Task Summary</span><a href="#" class="see-all">See all
                  ›</a></div>
              <div class="task-list">
                <div class="task-item">
                  <div class="task-dot" style="background:var(--danger);"></div>
                  <div class="task-info">
                    <div class="tname">Fix HVAC Unit</div>
                    <div class="tprop">Skyline Apartments</div>
                  </div>
                  <div class="task-status" style="background:var(--danger-light);color:var(--danger);">Urgent</div>
                </div>
                <div class="task-item">
                  <div class="task-dot" style="background:var(--blue-400);"></div>
                  <div class="task-info">
                    <div class="tname">Monthly Landscaping</div>
                    <div class="tprop">Green Valley Homes</div>
                  </div>
                  <div class="task-status" style="background:var(--blue-50);color:var(--blue-500);">Scheduled</div>
                </div>
                <div class="task-item">
                  <div class="task-dot" style="background:var(--gold);"></div>
                  <div class="task-info">
                    <div class="tname">Quarterly Inspection</div>
                    <div class="tprop">Downtown Lofts</div>
                  </div>
                  <div class="task-status" style="background:var(--pending-light);color:var(--accent-dk);">Pending</div>
                </div>
                <div class="task-item">
                  <div class="task-dot" style="background:var(--success);"></div>
                  <div class="task-info">
                    <div class="tname">Lease Renewal – B-201</div>
                    <div class="tprop">Green Valley Homes</div>
                  </div>
                  <div class="task-status" style="background:var(--success-light);color:var(--success);">Done</div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <?php include '../../includes/right_panel.php'; ?>
  <script>
    new Chart(document.getElementById('revenueChart'), { type: 'bar', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'], datasets: [{ label: 'Revenue', data: [612, 598, 645, 620, 680, 710, 695, 728], backgroundColor: 'rgba(37,99,196,0.15)', borderColor: '#2563c4', borderWidth: 2, borderRadius: 6 }, { label: 'Expenses', data: [248, 231, 270, 255, 275, 298, 302, 316], borderColor: '#deaf37', borderWidth: 2.5, type: 'line', tension: .4, fill: false, pointBackgroundColor: '#deaf37', pointRadius: 4, pointHoverRadius: 6 }] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => `₱ ${(ctx.parsed.y * 1000).toLocaleString()}` } } }, scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: v => '₱' + v + 'K', font: { size: 11 } } } } } });
    new Chart(document.getElementById('occupancyDonut'), { type: 'doughnut', data: { labels: ['Occupied', 'Vacant', 'Maintenance'], datasets: [{ data: [66, 14, 4], backgroundColor: ['#2563c4', '#dbeafe', '#E74C3C'], borderWidth: 0, hoverOffset: 6 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false } } } });
  </script>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Unauthorized',
          text: 'You do not have permission to access that page.',
          confirmButtonColor: '#3085d6'
        });
      });
    </script>
    <?php endif; ?>
  <?php include '../../includes/layout_close_noclose.php'; ?>