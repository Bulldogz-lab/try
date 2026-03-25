<?php
$page_title = 'Occupancy Reports';
$active_page = 'occupancy_reports';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

function db_query($conn, string $sql): array
{
    $result = mysqli_query($conn, $sql);
    if (!$result)
        return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result))
        $rows[] = $row;
    mysqli_free_result($result);
    return $rows;
}

function db_row($conn, string $sql): array
{
    $result = mysqli_query($conn, $sql);
    if (!$result)
        return [];
    $row = mysqli_fetch_assoc($result) ?? [];
    mysqli_free_result($result);
    return $row;
}

$export_csv = isset($_GET['export']) && $_GET['export'] === 'csv';

$properties = db_query(
    $conn,
    "SELECT p.property_id, p.property_name,
                COUNT(u.unit_id)                                                    AS total_units,
                SUM(u.status = 'occupied')                                          AS occupied,
                SUM(u.status = 'vacant')                                            AS vacant,
                SUM(u.status = 'maintenance')                                       AS maintenance,
                ROUND(SUM(u.status='occupied') / NULLIF(COUNT(u.unit_id),0)*100,1)  AS occ_rate,
                AVG(u.rent_amount)                                                  AS avg_rent,
                SUM(u.rent_amount)                                                  AS total_rent_potential
        FROM properties p
        LEFT JOIN units u ON u.property_id = p.property_id
        GROUP BY p.property_id, p.property_name
        ORDER BY occ_rate DESC"
);

$totals = db_row(
    $conn,
    "SELECT COUNT(*)                                                AS total_units,
                SUM(status = 'occupied')                                AS occupied,
                SUM(status = 'vacant')                                  AS vacant,
                SUM(status = 'maintenance')                             AS maintenance,
                ROUND(SUM(status='occupied')/NULLIF(COUNT(*),0)*100,1)  AS occ_rate,
                SUM(rent_amount)                                        AS rent_potential,
                SUM(CASE WHEN status='occupied' THEN rent_amount END)   AS rent_actual
        FROM units"
);

$total_units = (int) ($totals['total_units'] ?? 0);
$occupied = (int) ($totals['occupied'] ?? 0);
$vacant = (int) ($totals['vacant'] ?? 0);
$maintenance_cnt = (int) ($totals['maintenance'] ?? 0);
$occ_rate = (float) ($totals['occ_rate'] ?? 0);
$rent_potential = (float) ($totals['rent_potential'] ?? 0);
$rent_actual = (float) ($totals['rent_actual'] ?? 0);

$by_type = db_query(
    $conn,
    "SELECT unit_type,
                COUNT(*)                                                       AS total,
                SUM(status='occupied')                                         AS occupied,
                ROUND(SUM(status='occupied')/NULLIF(COUNT(*),0)*100,1)         AS occ_rate
        FROM units
        WHERE unit_type IS NOT NULL AND unit_type != ''
        GROUP BY unit_type ORDER BY total DESC"
);

$all_units = db_query(
    $conn,
    "SELECT u.*, p.property_name
        FROM units u
        LEFT JOIN properties p ON p.property_id = u.property_id
        ORDER BY p.property_name, u.floor, u.unit_number"
);

$units_by_property = [];
foreach ($all_units as $u) {
    $units_by_property[$u['property_id']][] = $u;
}

if ($export_csv) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="occupancy_report_' . date('Y-m') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Property', 'Total Units', 'Occupied', 'Vacant', 'Maintenance', 'Occupancy Rate', 'Avg Rent', 'Rent Potential']);
    foreach ($properties as $p) {
        fputcsv($out, [
            $p['property_name'],
            $p['total_units'],
            $p['occupied'],
            $p['vacant'],
            $p['maintenance'],
            $p['occ_rate'] . '%',
            number_format((float) $p['avg_rent'], 2),
            number_format((float) $p['total_rent_potential'], 2),
        ]);
    }
    fclose($out);
    exit;
}

function occ_colour(float $rate): string
{
    if ($rate >= 80)
        return '#2ECC71';
    if ($rate >= 60)
        return '#2563c4';
    return '#deaf37';
}
function status_badge(string $s): string
{
    return match (strtolower($s)) {
        'occupied' => 'background:#dcfce7;color:#16a34a;',
        'maintenance' => 'background:#fef9c3;color:#b45309;',
        default => 'background:#f1f5f9;color:#64748b;',
    };
}

$chart_colours = ['#2563c4', '#2ECC71', '#deaf37', '#E74C3C', '#8B5CF6', '#06B6D4', '#F97316', '#EC4899'];
?>

<link rel="stylesheet" href="../../assets/css/admin-css/occupancy.css">

<div class="page-header">
    <div class="top-header">
        <h2>Occupancy Reports</h2>
        <div class="page-header-sub">Live occupancy data across all properties</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <div class="filter-bar" style="margin-bottom:0;">
            <select id="propertyFilter" onchange="filterProperty(this.value)">
                <option value="">All Properties</option>
                <?php foreach ($properties as $p): ?>
                    <option value="<?= (int) $p['property_id'] ?>"><?= htmlspecialchars($p['property_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <a href="?export=csv">
            <button class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Export CSV
            </button>
        </a>
    </div>
</div>

<div class="page-inner">
    <div class="cards-area">

        <div class="stat-row">
            <div class="stat-card sc-blue">
                <div class="stat-card-left">
                    <div class="stat-label">Overall Occupancy</div>
                    <div class="stat-value"><?= $occ_rate ?>%</div>
                    <span class="stat-trend <?= $occ_rate >= 75 ? 'up' : ($occ_rate >= 50 ? 'neutral' : 'down') ?>">
                        <?= $occ_rate >= 75 ? '↑ Good' : ($occ_rate >= 50 ? '– Moderate' : '↓ Low') ?>
                    </span>
                    <div class="stat-sub"><?= $occupied ?> of <?= $total_units ?> units occupied</div>
                </div>
                <div class="stat-icon-wrap blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
                    </svg>
                </div>
            </div>
            <div class="stat-card sc-green">
                <div class="stat-card-left">
                    <div class="stat-label">Occupied Units</div>
                    <div class="stat-value"><?= $occupied ?></div>
                    <div class="stat-sub">of <?= $total_units ?> total units</div>
                </div>
                <div class="stat-icon-wrap green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
            </div>
            <div class="stat-card sc-gold">
                <div class="stat-card-left">
                    <div class="stat-label">Vacant Units</div>
                    <div class="stat-value"><?= $vacant ?></div>
                    <?php if ($maintenance_cnt > 0): ?>
                        <div class="stat-sub"><?= $maintenance_cnt ?> under maintenance</div>
                    <?php else: ?>
                        <div class="stat-sub">Available now</div>
                    <?php endif; ?>
                </div>
                <div class="stat-icon-wrap gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
            </div>
            <div class="stat-card sc-blue">
                <div class="stat-card-left">
                    <div class="stat-label">Rent Collection Potential</div>
                    <div class="stat-value" style="font-size:18px;">₱ <?= number_format($rent_actual) ?></div>
                    <div class="stat-sub">of ₱ <?= number_format($rent_potential) ?> total</div>
                </div>
                <div class="stat-icon-wrap blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="two-col">
            <div class="card" style="flex:3;">
                <div class="card-header"><span class="card-title">Occupancy % by Property</span></div>
                <div class="chart-wrap" style="height:220px;"><canvas id="occBarChart"></canvas></div>
            </div>
            <div class="card" style="flex:2;">
                <div class="card-header"><span class="card-title">Unit Status Split</span></div>
                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div class="chart-wrap" style="height:160px;width:160px;flex-shrink:0;"><canvas
                            id="statusDonut"></canvas></div>
                    <div class="legend-list" style="flex:1;min-width:110px;">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#2ECC71;"></div><span
                                class="legend-label">Occupied</span><span class="legend-val"><?= $occupied ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#94a3b8;"></div><span
                                class="legend-label">Vacant</span><span class="legend-val"><?= $vacant ?></span>
                        </div>
                        <?php if ($maintenance_cnt > 0): ?>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#deaf37;"></div><span
                                    class="legend-label">Maintenance</span><span
                                    class="legend-val"><?= $maintenance_cnt ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="propertyBreakdown">
            <div class="card-header"><span class="card-title">Occupancy by Property — Current Snapshot</span></div>
            <div style="display:flex;flex-direction:column;gap:18px;">
                <?php if (empty($properties)): ?>
                    <div style="text-align:center;padding:30px;color:var(--text-soft);">No property data found.</div>
                <?php else:
                    foreach ($properties as $idx => $p):
                        $rate = (float) $p['occ_rate'];
                        $col = $chart_colours[$idx % count($chart_colours)];
                        ?>
                        <div class="property-row" data-pid="<?= (int) $p['property_id'] ?>">
                            <div
                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px;">
                                <span
                                    style="font-size:14px;font-weight:600;"><?= htmlspecialchars($p['property_name']) ?></span>
                                <div style="display:flex;align-items:center;gap:14px;">
                                    <span style="font-size:12px;color:var(--text-soft);"><?= (int) $p['occupied'] ?> /
                                        <?= (int) $p['total_units'] ?> units</span>
                                    <span style="font-size:18px;font-weight:800;"><?= $rate ?>%</span>
                                    <button
                                        onclick="openDrilldown(<?= (int) $p['property_id'] ?>, '<?= htmlspecialchars(addslashes($p['property_name'])) ?>')"
                                        style="background:var(--blue-50,#eff6ff);color:var(--primary,#2563c4);border:none;border-radius:7px;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;">
                                        View Units
                                    </button>
                                </div>
                            </div>
                            <div class="prog-wrap" style="height:14px;">
                                <div class="prog-bar" style="width:<?= $rate ?>%;background:<?= $col ?>;"></div>
                            </div>
                            <div style="display:flex;gap:14px;margin-top:6px;flex-wrap:wrap;">
                                <span style="font-size:11px;color:var(--text-soft);">Avg rent: ₱
                                    <?= number_format((float) $p['avg_rent']) ?></span>
                                <span style="font-size:11px;color:var(--text-soft);">Potential: ₱
                                    <?= number_format((float) $p['total_rent_potential']) ?>/mo</span>
                                <?php if ((int) $p['vacant'] > 0): ?>
                                    <span style="font-size:11px;color:#ef4444;font-weight:600;"><?= (int) $p['vacant'] ?>
                                        vacant</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="flex-wrap:wrap;gap:10px;">
                <span class="card-title">All Units — Current Status</span>
                <div class="filter-bar" style="margin-bottom:0;">
                    <select id="statusFilter" onchange="filterTable()">
                        <option value="">All Statuses</option>
                        <option value="occupied">Occupied</option>
                        <option value="vacant">Vacant</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    <select id="typeFilter" onchange="filterTable()">
                        <option value="">All Types</option>
                        <?php foreach ($by_type as $t): ?>
                            <option value="<?= htmlspecialchars($t['unit_type']) ?>">
                                <?= htmlspecialchars($t['unit_type']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="unitSearch" onkeyup="filterTable()" placeholder="Search unit…"
                        style="min-width:160px;">
                </div>
            </div>
            <div class="table-wrap">
                <table id="unitsTable">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Property</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Rent / mo</th>
                            <th>Tenant</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_units as $u):
                            $bs = status_badge($u['status'] ?? 'vacant');
                            $label = trim(($u['unit_number'] ?? '') . ' ' . ($u['unit_name'] ?? ''));
                            ?>
                            <tr data-status="<?= strtolower($u['status'] ?? 'vacant') ?>"
                                data-type="<?= htmlspecialchars($u['unit_type'] ?? '') ?>"
                                data-pid="<?= (int) $u['property_id'] ?>">
                                <td style="font-weight:600;"><?= htmlspecialchars($label ?: '—') ?></td>
                                <td style="font-size:13px;color:var(--text-soft);">
                                    <?= htmlspecialchars($u['property_name'] ?? '—') ?>
                                </td>
                                <td style="font-size:13px;"><?= htmlspecialchars($u['unit_type'] ?? '—') ?></td>
                                <td style="font-size:13px;color:var(--text-soft);">Floor
                                    <?= htmlspecialchars((string) ($u['floor'] ?? '—')) ?>
                                </td>
                                <td style="font-weight:600;">₱ <?= number_format((float) $u['rent_amount']) ?></td>
                                <td style="font-size:13px;color:var(--text-soft);">
                                    <?= htmlspecialchars($u['tenant_name'] ?? '—') ?>
                                </td>
                                <td>
                                    <span
                                        style="<?= $bs ?>padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;">
                                        <?= ucfirst($u['status'] ?? 'vacant') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div
                style="padding:10px 18px;text-align:right;font-size:13px;color:var(--text-soft);border-top:1px solid var(--border,#e5e7eb);">
                Showing <strong id="visibleCount"><?= count($all_units) ?></strong> of
                <strong><?= count($all_units) ?></strong> units
            </div>
        </div>

    </div>
</div>

<!-- ── drill-down modal ───────────────────────────────────────────────────────-->
<div class="modal-overlay" id="drillModal">
    <div class="modal">
        <button class="modal-close" onclick="closeDrill()">&times;</button>
        <div class="modal-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
            </svg>
            <span id="drillTitle">Property Units</span>
        </div>
        <div style="display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap;" id="drillStats"></div>
        <div class="unit-grid" id="drillGrid"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
window.occupancyData = {
    propNames: <?= json_encode(array_column($properties, 'property_name')) ?>,
    propRates: <?= json_encode(array_map(fn($p) => (float)$p['occ_rate'], $properties)) ?>,
    propColours: <?= json_encode(array_map(fn($i) => $chart_colours[$i % count($chart_colours)], array_keys($properties))) ?>,
    donutData: <?= json_encode([$occupied, $vacant, $maintenance_cnt]) ?>,
    unitsByProp: <?= json_encode($units_by_property) ?>
};
</script>

<script src="../../assets/js/admin/occupancy.js"></script>

<?php include '../../includes/layout_close.php'; ?>