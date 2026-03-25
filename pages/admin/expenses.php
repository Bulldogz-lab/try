<?php
$page_title = 'Expenses';
$active_page = 'expenses';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

function db_query($conn, string $sql, array $params = []): array
{
  $st = $conn->prepare($sql);
  if (!$st)
    return [];

  if (!empty($params)) {
    $types = '';
    $values = [];
    foreach ($params as $param) {
      if (is_int($param))
        $types .= 'i';
      elseif (is_float($param))
        $types .= 'd';
      else
        $types .= 's';
      $values[] = $param;
    }
    $st->bind_param($types, ...$values);
  }

  $st->execute();
  $result = $st->get_result();
  $rows = [];
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  $st->close();
  return $rows;
}

$month_filter = $_GET['month'] ?? date('Y-m');
$search = trim($_GET['q'] ?? '');
$cat_filter = $_GET['category'] ?? '';

[$yr, $mo] = explode('-', $month_filter . '-00');
$date_from = "$yr-$mo-01";
$date_to = date('Y-m-t', strtotime($date_from));

$total_sql = "SELECT COALESCE(SUM(amount),0) AS total FROM expenses e WHERE e.expense_date BETWEEN ? AND ?";
$total_row = db_query($conn, $total_sql, [$date_from, $date_to])[0] ?? ['total' => 0];
$total_this_month = (float) $total_row['total'];

$prev_from = date('Y-m-01', strtotime('-1 month', strtotime($date_from)));
$prev_to = date('Y-m-t', strtotime($prev_from));
$prev_sql = "SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE expense_date BETWEEN ? AND ?";
$prev_row = db_query($conn, $prev_sql, [$prev_from, $prev_to])[0] ?? ['total' => 0];
$prev_total = (float) $prev_row['total'];
$pct_change = $prev_total > 0 ? round((($total_this_month - $prev_total) / $prev_total) * 100) : 0;

$cat_sql = "SELECT expense_category, COALESCE(SUM(amount),0) AS cat_total FROM expenses e WHERE e.expense_date BETWEEN ? AND ? GROUP BY expense_category ORDER BY cat_total DESC";
$by_cat = db_query($conn, $cat_sql, [$date_from, $date_to]);
$cat_map = array_column($by_cat, 'cat_total', 'expense_category');

$trend_labels = [];
$trend_data = [];
for ($i = 5; $i >= 0; $i--) {
  $ts = strtotime("-$i months", strtotime($date_from));
  $lbl = date('M', $ts);
  $mf = date('Y-m-01', $ts);
  $mt = date('Y-m-t', $ts);
  $trend_labels[] = $lbl;
  $trend_sql = "SELECT COALESCE(SUM(amount),0) AS t FROM expenses WHERE expense_date BETWEEN ? AND ?";
  $row = db_query($conn, $trend_sql, [$mf, $mt])[0] ?? ['t' => 0];
  $trend_data[] = (float) $row['t'];
}

$donut_sql = "SELECT expense_category, COALESCE(SUM(amount),0) AS cat_total FROM expenses e WHERE e.expense_date BETWEEN ? AND ? GROUP BY expense_category ORDER BY cat_total DESC LIMIT 6";
$donut_rows = db_query($conn, $donut_sql, [$date_from, $date_to]);

$prop_sql = "SELECT property_id, property_name FROM properties ORDER BY property_name";
$properties = db_query($conn, $prop_sql, []);

$unit_sql = "SELECT unit_id, unit_name FROM units ORDER BY unit_name";
$units = db_query($conn, $unit_sql, []);

$table_sql = "SELECT e.*, p.property_name, u.unit_name FROM expenses e LEFT JOIN properties p ON e.property_id = p.property_id LEFT JOIN units u ON e.unit_id = u.unit_id WHERE e.expense_date BETWEEN ? AND ?";
$table_params = [$date_from, $date_to];

if ($search !== '') {
  $table_sql .= " AND (e.description LIKE ? OR p.property_name LIKE ?)";
  $search_term = "%$search%";
  $table_params[] = $search_term;
  $table_params[] = $search_term;
}
if ($cat_filter !== '') {
  $table_sql .= " AND e.expense_category = ?";
  $table_params[] = $cat_filter;
}

$table_sql .= " ORDER BY e.expense_date DESC, e.expense_id DESC";
$expenses = db_query($conn, $table_sql, $table_params);

$cat_sql = "SELECT DISTINCT expense_category FROM expenses ORDER BY expense_category";
$all_cats = db_query($conn, $cat_sql, []);

$cat_colours = [
  'Maintenance' => '#E74C3C',
  'Utilities' => '#2563c4',
  'Salaries' => '#2ECC71',
  'Admin' => '#deaf37',
  'Insurance' => '#8B5CF6',
  'Other' => '#94a3b8',
];

function cat_colour(string $c, array $map): string
{
  return $map[$c] ?? '#94a3b8';
}

$maintenance_total = (float) ($cat_map['Maintenance'] ?? 0);
$utilities_total = (float) ($cat_map['Utilities'] ?? 0);
$admin_total = (float) ($cat_map['Admin'] ?? 0);
?>

<link rel="stylesheet" href="../../assets/css/admin-css/expenses.css">

<div class="page-header">
  <div class="top-header">
    <h2>Expenses</h2>
    <div class="page-header-sub">Monitor all property-related operational costs</div>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <button class="btn-outline" id="btnExportCSV">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="7 10 12 15 17 10" />
        <line x1="12" y1="15" x2="12" y2="3" />
      </svg>
      Export CSV
    </button>
    <button class="btn btn-primary" id="btnOpenAdd">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Log Expense
    </button>
  </div>
</div>

<div class="page-inner">
  <div class="cards-area">

    <div class="stat-row">
      <div class="stat-card sc-red">
        <div class="stat-card-left">
          <div class="stat-label">Total This Month</div>
          <div class="stat-value">
            ₱ <span id="statTotal"><?= number_format($total_this_month) ?></span>
            <span class="stat-percentage" id="statPercentage"><?= abs($pct_change) ?>%</span>
          </div>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="1" x2="12" y2="23" />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
          </svg>
        </div>
      </div>
      <div class="stat-card sc-gold">
        <div class="stat-card-left">
          <div class="stat-label">Maintenance</div>
          <div class="stat-value">
            ₱ <span id="statMaintenance"><?= number_format($maintenance_total) ?></span>
            <span class="stat-percentage"
              id="statMaintenancePercent"><?= $total_this_month > 0 ? round($maintenance_total / $total_this_month * 100) : 0 ?>%</span>
          </div>
        </div>
        <div class="stat-icon-wrap gold"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
          </svg></div>
      </div>
      <div class="stat-card sc-blue">
        <div class="stat-card-left">
          <div class="stat-label">Utilities</div>
          <div class="stat-value">
            ₱ <span id="statUtilities"><?= number_format($utilities_total) ?></span>
            <span class="stat-percentage"
              id="statUtilitiesPercent"><?= $total_this_month > 0 ? round($utilities_total / $total_this_month * 100) : 0 ?>%</span>
          </div>
        </div>
        <div class="stat-icon-wrap blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
          </svg></div>
      </div>
      <div class="stat-card sc-green">
        <div class="stat-card-left">
          <div class="stat-label">Admin / Other</div>
          <div class="stat-value">
            ₱ <span id="statAdmin"><?= number_format($admin_total) ?></span>
            <span class="stat-percentage"
              id="statAdminPercent"><?= $total_this_month > 0 ? round($admin_total / $total_this_month * 100) : 0 ?>%</span>
          </div>
        </div>
        <div class="stat-icon-wrap green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
          </svg></div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-header"><span class="card-title">Expense Trend (6 months)</span></div>
        <div class="chart-wrap" style="height:200px;"><canvas id="expTrendChart"></canvas></div>
      </div>
      <div class="card">
        <div class="card-header"><span class="card-title">By Category</span></div>
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
          <div class="chart-wrap" style="height:160px;width:160px;flex-shrink:0;"><canvas id="catDonut"></canvas></div>
          <div class="legend-list" style="flex:1;min-width:130px;" id="legendContainer">
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header-with-filters">
        <div class="header-left">
          Expenses — <?= date('F Y', strtotime($date_from)) ?>
        </div>

        <div class="header-right">
          <div class="month-nav">
            <?php
            $prev_month = date('Y-m', strtotime('-1 month', strtotime($date_from)));
            $next_month = date('Y-m', strtotime('+1 month', strtotime($date_from)));
            $next_disabled = $next_month > date('Y-m') ? true : false;
            ?>
            <a href="?month=<?= $prev_month ?>" style="text-decoration:none;">
              <button title="Previous month">‹</button>
            </a>
            <span><?= date('M Y', strtotime($date_from)) ?></span>
            <a href="?month=<?= $next_month ?>" style="text-decoration:none;">
              <button <?= $next_disabled ? 'disabled' : '' ?> title="Next month">›</button>
            </a>
          </div>

          <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search…" value="">
            <select id="categoryFilter">
              <option value="">All Categories</option>
              <?php foreach ($all_cats as $ac): ?>
                <option value="<?= htmlspecialchars($ac['expense_category']) ?>">
                  <?= htmlspecialchars($ac['expense_category']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div id="tableContainer" style="margin-top: 15px;">
        <table id="expensesTable">
          <thead>
            <tr>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">DESCRIPTION</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">PROPERTY</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">UNIT</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">DATE</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">CATEGORY</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">AMOUNT</th>
              <th style="color:#999;font-weight:600;font-size:11px;text-transform:uppercase;">ACTIONS</th>
            </tr>
          </thead>
          <tbody id="expensesBody">
          </tbody>
        </table>
        <div id="emptyState" class="table-empty" style="display:none;">
          <div class="table-empty-icon">📅</div>
          <div class="table-empty-text">No expenses found.</div>
        </div>
      </div>

      <div id="tableFooter" class="table-footer" style="display:none;">
        Showing <strong id="recordCount">0</strong> record(s) | Total: <strong style="color:var(--danger);">₱ <span id="footerTotal">0.00</span></strong>
      </div>
    </div>

  </div>
</div>

<div class="modal-overlay" id="expenseModal">
  <div class="modal">
    <button class="modal-close" onclick="ExpenseModal.close()">&times;</button>
    <div class="modal-title">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="2" y="7" width="20" height="14" rx="2" />
        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
      </svg>
      <span id="modalTitle">Log Expense</span>
    </div>
    <input type="hidden" id="editId">

    <div class="form-row">
      <div class="form-group">
        <label>Property</label>
        <select id="fProperty">
          <option value="">— Select Property —</option>
          <?php foreach ($properties as $p): ?>
            <option value="<?= $p['property_id'] ?>"><?= htmlspecialchars($p['property_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Unit (Optional)</label>
        <select id="fUnit">
          <option value="">— Select Unit —</option>
          <?php foreach ($units as $u): ?>
            <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Category</label>
        <select id="fCategory">
          <option value="">— Select —</option>
          <option>Maintenance</option>
          <option>Utilities</option>
          <option>Salaries</option>
          <option>Admin</option>
          <option>Insurance</option>
          <option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Date</label>
        <input type="date" id="fDate">
      </div>
    </div>

    <div class="form-group">
      <label>Description</label>
      <input type="text" id="fDescription" placeholder="e.g. HVAC Repair — Unit 3B">
    </div>

    <div class="form-group">
      <label>Amount (₱)</label>
      <input type="number" id="fAmount" min="0" step="0.01" placeholder="0.00">
    </div>

    <div class="modal-actions">
      <button class="btn-outline" onclick="ExpenseModal.close()">Cancel</button>
      <button class="btn btn-primary" id="btnSave" onclick="ExpenseForm.save()">Save Expense</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../../assets/js/admin/expenses.js"></script>

<?php include '../../includes/layout_close.php'; ?>