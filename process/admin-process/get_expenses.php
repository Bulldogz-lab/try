<?php
header('Content-Type: application/json');

include '../../includes/session.php';
include '../../includes/db.php';

function db_query($conn, string $sql, array $params = []): array {
  $st = $conn->prepare($sql);
  if (!$st) return [];
  
  if (!empty($params)) {
    $types = '';
    $values = [];
    foreach ($params as $param) {
      if (is_int($param)) $types .= 'i';
      elseif (is_float($param)) $types .= 'd';
      else $types .= 's';
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

// Get expenses
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

// Get stats
$stat_sql = "SELECT expense_category, COALESCE(SUM(amount),0) AS cat_total FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY expense_category";
$stat_rows = db_query($conn, $stat_sql, [$date_from, $date_to]);
$stat_map = array_column($stat_rows, 'cat_total', 'expense_category');

$total = array_sum(array_column($expenses, 'amount'));
$stats = [
  'total' => (float)$total,
  'maintenance' => (float)($stat_map['Maintenance'] ?? 0),
  'utilities' => (float)($stat_map['Utilities'] ?? 0),
  'admin' => (float)($stat_map['Admin'] ?? 0),
];

// Get trends
$trends = [];
for ($i = 5; $i >= 0; $i--) {
  $ts = strtotime("-$i months", strtotime($date_from));
  $lbl = date('M', $ts);
  $mf = date('Y-m-01', $ts);
  $mt = date('Y-m-t', $ts);
  $trend_sql = "SELECT COALESCE(SUM(amount),0) AS t FROM expenses WHERE expense_date BETWEEN ? AND ?";
  $row = db_query($conn, $trend_sql, [$mf, $mt])[0] ?? ['t' => 0];
  $trends[] = [
    'label' => $lbl,
    'amount' => (float)$row['t']
  ];
}

// Get categories
$cat_sql = "SELECT expense_category, COALESCE(SUM(amount),0) AS cat_total FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY expense_category ORDER BY cat_total DESC LIMIT 6";
$cat_rows = db_query($conn, $cat_sql, [$date_from, $date_to]);
$categories = array_map(function($row) {
  return [
    'category' => $row['expense_category'],
    'total' => (float)$row['cat_total']
  ];
}, $cat_rows);

echo json_encode([
  'expenses' => $expenses,
  'stats' => $stats,
  'trends' => $trends,
  'categories' => $categories
]);