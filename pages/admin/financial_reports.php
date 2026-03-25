<?php
$page_title = 'Financial Reports';
$active_page = 'financial_reports';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

// Get selected year from query parameter or use current year
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

/**
 * Fetch available years from database
 */
function getAvailableYears($conn)
{
  $query = "SELECT DISTINCT year FROM financial_records ORDER BY year DESC LIMIT 10";
  $result = $conn->query($query);

  if (!$result) {
    error_log('Error fetching years: ' . $conn->error);
    return [date('Y')];
  }

  $years = [];
  while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
  }

  return !empty($years) ? $years : [date('Y')];
}

function getFinancialDataFromDB($conn, $year)
{
  // Get monthly data
  $query = "
        SELECT 
            month,
            SUM(revenue) as total_revenue,
            SUM(maintenance + utilities + salaries + admin) as total_expenses,
            SUM(maintenance) as maintenance,
            SUM(utilities) as utilities,
            SUM(salaries) as salaries,
            SUM(admin) as admin
        FROM financial_records
        WHERE year = ?
        GROUP BY month
        ORDER BY month ASC
    ";

  $stmt = $conn->prepare($query);
  if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    return null;
  }

  $stmt->bind_param('i', $year);
  if (!$stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    $stmt->close();
    return null;
  }

  $result = $stmt->get_result();
  $monthly_data = [];
  while ($row = $result->fetch_assoc()) {
    $monthly_data[] = $row;
  }
  $stmt->close();

  // Get total revenue for percentage calculation
  $query = "SELECT SUM(revenue) as total_revenue FROM financial_records WHERE year = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $year);
  $stmt->execute();
  $total_result = $stmt->get_result();
  $total_revenue_row = $total_result->fetch_assoc();
  $total_revenue = (float) ($total_revenue_row['total_revenue'] ?? 1);
  $stmt->close();

  // Get revenue mix by property
  $query = "
        SELECT 
            p.property_name,
            SUM(fr.revenue) as total,
            ROUND(SUM(fr.revenue) / ? * 100, 0) as percentage
        FROM financial_records fr
        JOIN properties p ON fr.property_id = p.property_id
        WHERE fr.year = ?
        GROUP BY fr.property_id, p.property_name
        ORDER BY total DESC
    ";

  $stmt = $conn->prepare($query);
  if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    return null;
  }

  $stmt->bind_param('di', $total_revenue, $year);
  if (!$stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    $stmt->close();
    return null;
  }

  $result = $stmt->get_result();
  $revenue_mix = [];
  while ($row = $result->fetch_assoc()) {
    $revenue_mix[$row['property_name']] = (int) $row['percentage'];
  }
  $stmt->close();

  // Format data for charts and table
  $revenue = [];
  $expenses = [];
  $maintenance = [];
  $utilities = [];
  $salaries = [];
  $admin = [];
  $pnl_summary = [];

  $month_names = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  $prev_profit = null;

  foreach ($monthly_data as $row) {
    $month = (int) $row['month'];
    $rev = (int) $row['total_revenue'] / 1000; // Convert to thousands
    $exp = (int) $row['total_expenses'] / 1000;
    $profit = $rev - $exp;
    $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0;

    $revenue[] = $rev;
    $expenses[] = $exp;
    $maintenance[] = (int) $row['maintenance'] / 1000;
    $utilities[] = (int) $row['utilities'] / 1000;
    $salaries[] = (int) $row['salaries'] / 1000;
    $admin[] = (int) $row['admin'] / 1000;

    // Format for table display
    $rev_formatted = '₱ ' . number_format($row['total_revenue'], 0);
    $exp_formatted = '₱ ' . number_format($row['total_expenses'], 0);
    $profit_formatted = '₱ ' . number_format($profit * 1000, 0);

    // Calculate vs prior month
    $vs_prior = '—';
    if ($prev_profit !== null) {
      $prev_profit_amount = $prev_profit * 1000;
      $current_profit_amount = $profit * 1000;
      $change_pct = $prev_profit_amount > 0 ? round((($current_profit_amount - $prev_profit_amount) / $prev_profit_amount) * 100, 1) : 0;

      if ($change_pct > 0) {
        $vs_prior = '▲ ' . $change_pct . '%';
      } elseif ($change_pct < 0) {
        $vs_prior = '▼ ' . abs($change_pct) . '%';
      } else {
        $vs_prior = '—';
      }
    }

    $pnl_summary[] = [
      $month_names[$month],
      $rev_formatted,
      $exp_formatted,
      $profit_formatted,
      $margin . '%',
      $vs_prior
    ];

    $prev_profit = $profit;
  }

  return [
    'revenue' => $revenue,
    'expenses' => $expenses,
    'maintenance' => $maintenance,
    'utilities' => $utilities,
    'salaries' => $salaries,
    'admin' => $admin,
    'revenue_mix' => $revenue_mix,
    'pnl_summary' => $pnl_summary
  ];
}

/**
 * Calculate summary statistics from database
 */
function calculateStatsFromDB($conn, $year)
{
  // Get current year totals
  $query = "
        SELECT 
            SUM(revenue) as total_revenue,
            SUM(maintenance + utilities + salaries + admin) as total_expenses
        FROM financial_records
        WHERE year = ?
    ";

  $stmt = $conn->prepare($query);
  if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    return getDefaultStats();
  }

  $stmt->bind_param('i', $year);
  $stmt->execute();
  $result = $stmt->get_result();
  $totals = $result->fetch_assoc();
  $stmt->close();

  $total_revenue = (float) ($totals['total_revenue'] ?? 0);
  $total_expenses = (float) ($totals['total_expenses'] ?? 0);
  $net_profit = $total_revenue - $total_expenses;
  $roi = $total_revenue > 0 ? round(($net_profit / $total_revenue) * 100, 1) : 0;

  // Calculate YoY growth
  $prev_year = $year - 1;
  $query = "
        SELECT 
            SUM(revenue) as prev_revenue,
            SUM(maintenance + utilities + salaries + admin) as prev_expenses
        FROM financial_records
        WHERE year = ?
    ";

  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $prev_year);
  $stmt->execute();
  $result = $stmt->get_result();
  $prev_data = $result->fetch_assoc();
  $stmt->close();

  $revenue_growth = 0;
  $expense_growth = 0;
  $profit_growth = 0;

  $prev_revenue = (float) ($prev_data['prev_revenue'] ?? 0);
  $prev_expenses = (float) ($prev_data['prev_expenses'] ?? 0);
  $prev_profit = $prev_revenue - $prev_expenses;

  if ($prev_revenue > 0) {
    $revenue_growth = round((($total_revenue - $prev_revenue) / $prev_revenue) * 100, 1);
  }
  if ($prev_expenses > 0) {
    $expense_growth = round((($total_expenses - $prev_expenses) / $prev_expenses) * 100, 1);
  }
  if ($prev_profit > 0) {
    $profit_growth = round((($net_profit - $prev_profit) / $prev_profit) * 100, 1);
  }

  return [
    'total_revenue' => $total_revenue,
    'total_expenses' => $total_expenses,
    'net_profit' => $net_profit,
    'roi' => $roi,
    'revenue_growth' => $revenue_growth,
    'expense_growth' => $expense_growth,
    'profit_growth' => $profit_growth
  ];
}

/**
 * Get default stats when no data is available
 */
function getDefaultStats()
{
  return [
    'total_revenue' => 0,
    'total_expenses' => 0,
    'net_profit' => 0,
    'roi' => 0,
    'revenue_growth' => 0,
    'expense_growth' => 0,
    'profit_growth' => 0
  ];
}

/**
 * Format currency
 */
function formatCurrency($amount)
{
  if ($amount >= 1000000) {
    return '₱ ' . number_format($amount / 1000000, 2) . 'M';
  } elseif ($amount >= 1000) {
    return '₱ ' . number_format($amount / 1000, 2) . 'K';
  }
  return '₱ ' . number_format($amount, 0);
}

// Fetch data from database
$available_years = getAvailableYears($conn);
$financial_data = getFinancialDataFromDB($conn, $selected_year);
$stats = calculateStatsFromDB($conn, $selected_year);

// Handle missing data
if (!$financial_data) {
  $financial_data = [
    'revenue' => [],
    'expenses' => [],
    'maintenance' => [],
    'utilities' => [],
    'salaries' => [],
    'admin' => [],
    'revenue_mix' => [],
    'pnl_summary' => []
  ];
}
?>

<div class="page-header">
  <div class="left-header">
    <h2>Financial Reports</h2>
    <div class="page-header-sub">Income, expenses and profitability overview</div>
  </div>

  <div style="display:flex;gap:8px;">
    <select id="yearSelect" onchange="handleYearChange(this.value)"
      style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;background:var(--white);">
      <?php foreach ($available_years as $year): ?>
        <option value="<?php echo $year; ?>" <?php echo $selected_year === (int) $year ? 'selected' : ''; ?>>
          <?php echo $year; ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-secondary" onclick="exportPDF()">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="7 10 12 15 17 10" />
        <line x1="12" y1="15" x2="12" y2="3" />
      </svg>Export PDF
    </button>
  </div>
</div>

<div class="page-inner">
  <div class="cards-area">

    <!-- Statistics Cards -->
    <div class="stat-row">
      <div class="stat-card sc-green">
        <div class="stat-card-left">
          <div class="stat-label">Total Revenue (YTD)</div>
          <div class="stat-value" id="totalRevenue"><?php echo formatCurrency($stats['total_revenue']); ?></div>
          <span class="stat-trend up" id="revenueGrowth">↑ <?php echo $stats['revenue_growth']; ?>%</span>
        </div>
        <div class="stat-icon-wrap green">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            <polyline points="17 6 23 6 23 12" />
          </svg>
        </div>
      </div>

      <div class="stat-card sc-red">
        <div class="stat-card-left">
          <div class="stat-label">Total Expenses (YTD)</div>
          <div class="stat-value" id="totalExpenses"><?php echo formatCurrency($stats['total_expenses']); ?></div>
          <span class="stat-trend down" id="expenseGrowth">↑ <?php echo $stats['expense_growth']; ?>%</span>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" />
            <polyline points="17 18 23 18 23 12" />
          </svg>
        </div>
      </div>

      <div class="stat-card sc-blue">
        <div class="stat-card-left">
          <div class="stat-label">Net Profit (YTD)</div>
          <div class="stat-value" id="netProfit"><?php echo formatCurrency($stats['net_profit']); ?></div>
          <span class="stat-trend up" id="profitGrowth"><?php echo $stats['profit_growth']; ?>%</span>
        </div>
        <div class="stat-icon-wrap blue">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="1" x2="12" y2="23" />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
          </svg>
        </div>
      </div>

      <div class="stat-card sc-gold">
        <div class="stat-card-left">
          <div class="stat-label">ROI</div>
          <div class="stat-value" id="roi"><?php echo $stats['roi']; ?>%</div>
          <span class="stat-trend up">↑ 3.2%</span>
        </div>
        <div class="stat-icon-wrap gold">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="two-col">
      <div class="card" style="flex:2;">
        <div class="card-header"><span class="card-title">Monthly Profit & Loss</span></div>
        <div class="chart-wrap" style="height:220px;">
          <canvas id="plChart"></canvas>
        </div>
      </div>

      <div class="card" style="flex:1;">
        <div class="card-header"><span class="card-title">Revenue Mix</span></div>
        <div class="chart-wrap" style="height:160px;">
          <canvas id="revMixDonut"></canvas>
        </div>
        <div class="legend-list" style="margin-left:12px;" id="revenueMixLegend">
          <?php
          $colors = ['#2563c4', '#2ECC71', '#deaf37'];
          $index = 0;
          if (!empty($financial_data['revenue_mix'])):
            foreach ($financial_data['revenue_mix'] as $property => $percentage):
              $color = $colors[$index % count($colors)];
              $index++;
              ?>
              <div class="legend-item">
                <div class="legend-dot" style="background:<?php echo $color; ?>;"></div>
                <span class="legend-label"><?php echo htmlspecialchars($property); ?></span>
                <span class="legend-val"><?php echo $percentage; ?>%</span>
              </div>
            <?php endforeach; endif; ?>
        </div>
      </div>
    </div>

    <!-- Expense Breakdown Chart -->
    <div class="card">
      <div class="card-header"><span class="card-title">Expense Breakdown by Category</span></div>
      <div class="chart-wrap" style="height:180px;">
        <canvas id="expBreakChart"></canvas>
      </div>
    </div>

    <!-- P&L Summary Table -->
    <div class="card">
      <div class="card-header"><span class="card-title">Monthly Profit & Loss Summary</span></div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Month</th>
              <th>Revenue</th>
              <th>Expenses</th>
              <th>Net Profit</th>
              <th>Margin</th>
              <th>vs Prior Month</th>
            </tr>
          </thead>
          <tbody id="pnlTableBody">
            <?php if (!empty($financial_data['pnl_summary'])): ?>
              <?php foreach ($financial_data['pnl_summary'] as $row): ?>
                <tr>
                  <td style="font-weight:600;"><?php echo htmlspecialchars($row[0]); ?></td>
                  <td style="color:var(--success);font-weight:600;"><?php echo $row[1]; ?></td>
                  <td style="color:var(--danger);"><?php echo $row[2]; ?></td>
                  <td style="font-weight:700;"><?php echo $row[3]; ?></td>
                  <td><?php echo $row[4]; ?></td>
                  <td
                    style="color:<?php echo str_contains($row[5], '▲') ? 'var(--success)' : (str_contains($row[5], '▼') ? 'var(--danger)' : 'var(--text-soft)'); ?>; font-weight:600;">
                    <?php echo $row[5]; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:20px;color:var(--text-soft);">
                  No data available for <?php echo $selected_year; ?>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
  // Chart data from PHP (converted to JSON)
  let chartData = <?php echo json_encode($financial_data); ?>;
  let selectedYear = <?php echo json_encode($selected_year); ?>;

  const blue = '#2563c4', gold = '#deaf37', grn = '#2ECC71', red = '#E74C3C';
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  let plChartInstance = null;
  let revMixChartInstance = null;
  let expBreakChartInstance = null;
  let autoRefreshInterval = null;

  /**
   * Format currency for display
   */
  function formatCurrency(amount) {
    if (amount >= 1000000) {
      return '₱ ' + (amount / 1000000).toFixed(2) + 'M';
    } else if (amount >= 1000) {
      return '₱ ' + (amount / 1000).toFixed(2) + 'K';
    }
    return '₱ ' + Math.round(amount).toLocaleString();
  }

  /**
   * Handle year change dynamically
   */
  function handleYearChange(year) {
    selectedYear = parseInt(year);
    loadFinancialData(selectedYear);
  }

  /**
   * Fetch data from server via AJAX
   */
  function loadFinancialData(year) {
    fetch('../../process/admin/get_financial_data.php?year=' + year, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          chartData = data.financial_data;
          updateStatistics(data.stats);
          updateCharts();
          updateTable(data.financial_data.pnl_summary);
          updateLegend(data.financial_data.revenue_mix);
        } else {
          console.error('Error loading data:', data.message);
        }
      })
      .catch(error => console.error('Fetch error:', error));
  }

  /**
   * Update statistics cards
   */
  function updateStatistics(stats) {
    document.getElementById('totalRevenue').textContent = formatCurrency(stats.total_revenue);
    document.getElementById('revenueGrowth').textContent = (stats.revenue_growth >= 0 ? '↑ ' : '↓ ') + Math.abs(stats.revenue_growth) + '%';

    document.getElementById('totalExpenses').textContent = formatCurrency(stats.total_expenses);
    document.getElementById('expenseGrowth').textContent = (stats.expense_growth >= 0 ? '↑ ' : '↓ ') + Math.abs(stats.expense_growth) + '%';

    document.getElementById('netProfit').textContent = formatCurrency(stats.net_profit);
    document.getElementById('profitGrowth').textContent = (stats.profit_growth >= 0 ? '↑ ' : '↓ ') + Math.abs(stats.profit_growth) + '%';

    document.getElementById('roi').textContent = stats.roi + '%';
  }

  /**
   * Update charts with new data
   */
  function updateCharts() {
    const rev = chartData.revenue || [];
    const exp = chartData.expenses || [];
    const profit = rev.map((r, i) => r - (exp[i] || 0));

    if (rev.length === 0) {
      console.log('No financial data available');
      return;
    }

    // Update P&L Chart
    if (plChartInstance) {
      plChartInstance.data.labels = months.slice(0, rev.length);
      plChartInstance.data.datasets[0].data = rev;
      plChartInstance.data.datasets[1].data = exp;
      plChartInstance.data.datasets[2].data = profit;
      plChartInstance.update();
    } else {
      initPLChart(rev, exp, profit);
    }

    // Update Revenue Mix Chart
    if (Object.keys(chartData.revenue_mix).length > 0) {
      if (revMixChartInstance) {
        const labels = Object.keys(chartData.revenue_mix);
        const data = Object.values(chartData.revenue_mix);
        revMixChartInstance.data.labels = labels;
        revMixChartInstance.data.datasets[0].data = data;
        revMixChartInstance.update();
      } else {
        initRevenueMixChart();
      }
    }

    // Update Expense Breakdown Chart
    if (chartData.maintenance && chartData.maintenance.length > 0) {
      if (expBreakChartInstance) {
        expBreakChartInstance.data.labels = months.slice(0, chartData.maintenance.length);
        expBreakChartInstance.data.datasets[0].data = chartData.maintenance;
        expBreakChartInstance.data.datasets[1].data = chartData.utilities;
        expBreakChartInstance.data.datasets[2].data = chartData.salaries;
        expBreakChartInstance.data.datasets[3].data = chartData.admin;
        expBreakChartInstance.update();
      } else {
        initExpenseBreakChart();
      }
    }
  }

  /**
   * Update P&L table
   */
  function updateTable(pnlSummary) {
    const tbody = document.getElementById('pnlTableBody');
    tbody.innerHTML = '';

    if (!pnlSummary || pnlSummary.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:var(--text-soft);">No data available for ' + selectedYear + '</td></tr>';
      return;
    }

    pnlSummary.forEach(row => {
      const tr = document.createElement('tr');
      const trendColor = row[5].includes('▲') ? 'var(--success)' : (row[5].includes('▼') ? 'var(--danger)' : 'var(--text-soft)');

      tr.innerHTML = `
        <td style="font-weight:600;">${row[0]}</td>
        <td style="color:var(--success);font-weight:600;">${row[1]}</td>
        <td style="color:var(--danger);">${row[2]}</td>
        <td style="font-weight:700;">${row[3]}</td>
        <td>${row[4]}</td>
        <td style="color:${trendColor};font-weight:600;">${row[5]}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  /**
   * Update revenue mix legend
   */
  function updateLegend(revenueMix) {
    const legend = document.getElementById('revenueMixLegend');
    legend.innerHTML = '';

    const colors = ['#2563c4', '#2ECC71', '#deaf37'];
    let index = 0;

    Object.entries(revenueMix).forEach(([property, percentage]) => {
      const color = colors[index % colors.length];
      const item = document.createElement('div');
      item.className = 'legend-item';
      item.innerHTML = `
        <div class="legend-dot" style="background:${color};"></div>
        <span class="legend-label">${property}</span>
        <span class="legend-val">${percentage}%</span>
      `;
      legend.appendChild(item);
      index++;
    });
  }

  /**
   * Initialize Profit & Loss Chart
   */
  function initPLChart(revenue, expenses, profit) {
    const plCtx = document.getElementById('plChart');
    if (!plCtx) return;

    plChartInstance = new Chart(plCtx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: months.slice(0, revenue.length),
        datasets: [
          {
            label: 'Revenue',
            data: revenue,
            backgroundColor: 'rgba(37,99,196,0.8)',
            borderRadius: 5,
            borderSkipped: false
          },
          {
            label: 'Expenses',
            data: expenses,
            backgroundColor: 'rgba(231,76,60,0.65)',
            borderRadius: 5,
            borderSkipped: false
          },
          {
            label: 'Profit',
            data: profit,
            type: 'line',
            borderColor: grn,
            borderWidth: 2.5,
            backgroundColor: 'rgba(46,204,113,0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: grn,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            position: 'left',
            labels: { usePointStyle: true, font: { size: 11 } }
          },
          tooltip: {
            callbacks: {
              label: ctx => `${ctx.dataset.label}: ₱ ${ctx.parsed.y}K`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { size: 11 } }
          },
          y: {
            grid: { color: 'rgba(0,0,0,.05)' },
            ticks: {
              callback: v => '₱' + v + 'K',
              font: { size: 11 }
            }
          },
          y1: {
            type: 'linear',
            display: false,
            position: 'left'
          }
        }
      }
    });
  }

  /**
   * Initialize Revenue Mix Donut Chart
   */
  function initRevenueMixChart() {
    const revMixCtx = document.getElementById('revMixDonut');
    if (!revMixCtx || !chartData.revenue_mix || Object.keys(chartData.revenue_mix).length === 0) {
      return;
    }

    const revenueMixLabels = Object.keys(chartData.revenue_mix);
    const revenueMixData = Object.values(chartData.revenue_mix);

    revMixChartInstance = new Chart(revMixCtx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: revenueMixLabels,
        datasets: [{
          data: revenueMixData,
          backgroundColor: [blue, grn, gold],
          borderWidth: 0,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  /**
   * Initialize Expense Breakdown Chart
   */
  function initExpenseBreakChart() {
    const expBreakCtx = document.getElementById('expBreakChart');
    if (!expBreakCtx || !chartData.maintenance || chartData.maintenance.length === 0) {
      return;
    }

    expBreakChartInstance = new Chart(expBreakCtx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: months.slice(0, chartData.maintenance.length),
        datasets: [
          {
            label: 'Maintenance',
            data: chartData.maintenance,
            backgroundColor: 'rgba(231,76,60,0.75)',
            borderRadius: 4,
            stack: 's'
          },
          {
            label: 'Utilities',
            data: chartData.utilities,
            backgroundColor: 'rgba(37,99,196,0.7)',
            borderRadius: 4,
            stack: 's'
          },
          {
            label: 'Salaries',
            data: chartData.salaries,
            backgroundColor: 'rgba(46,204,113,0.7)',
            borderRadius: 4,
            stack: 's'
          },
          {
            label: 'Admin',
            data: chartData.admin,
            backgroundColor: 'rgba(222,175,55,0.7)',
            borderRadius: 4,
            stack: 's'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            position: 'left',
            labels: { usePointStyle: true, font: { size: 11 } }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            stacked: true,
            ticks: { font: { size: 11 } }
          },
          y: {
            stacked: true,
            grid: { color: 'rgba(0,0,0,.05)' },
            ticks: {
              callback: v => '₱' + v + 'K',
              font: { size: 11 }
            }
          }
        }
      }
    });
  }

  /**
   * Export page to PDF
   */
  function exportPDF() {
    const element = document.querySelector('.page-inner');
    const opt = {
      margin: 10,
      filename: `Financial_Report_${selectedYear}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
    };
    html2pdf().set(opt).from(element).save();
  }

  /**
   * Auto-refresh data every 30 seconds
   */
  function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
      loadFinancialData(selectedYear);
    }, 30000); // 30 seconds
  }

  /**
   * Stop auto-refresh
   */
  function stopAutoRefresh() {
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
    }
  }

  // Initialize charts when DOM is ready
  document.addEventListener('DOMContentLoaded', () => {
    initPLChart(chartData.revenue || [], chartData.expenses || [], (chartData.revenue || []).map((r, i) => r - ((chartData.expenses || [])[i] || 0)));
    initRevenueMixChart();
    initExpenseBreakChart();

    // Start auto-refresh
    startAutoRefresh();
  });

  // Stop auto-refresh when user leaves page
  window.addEventListener('beforeunload', stopAutoRefresh);
</script>

<?php
// Close database connection
$conn->close();
include '../../includes/layout_close.php';
?>