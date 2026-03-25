<?php
/**
 * Shared data layer for Booking Reports.
 * Included by booking_reports_api.php and booking_reports_stream.php.
 * MySQLi only — no PDO.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'propsight_db');

function br_connect() {
    $c = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$c) {
        http_response_code(500);
        echo json_encode(['error' => 'DB connection failed: ' . mysqli_connect_error()]);
        exit;
    }
    return $c;
}

function br_qi($c, $sql) {
    $r = mysqli_query($c, $sql);
    return ($r && mysqli_num_rows($r)) ? mysqli_fetch_assoc($r) : null;
}

function br_qa($c, $sql) {
    $r = mysqli_query($c, $sql);
    if (!$r) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
    return $rows;
}

function br_scalar($c, $sql) {
    $r = mysqli_query($c, $sql);
    if (!$r || !mysqli_num_rows($r)) return 0;
    $row = mysqli_fetch_row($r);
    return $row[0] ?? 0;
}

/**
 * Build the full payload for the given range (days).
 */
function br_build($c, $range) {
    $df = mysqli_real_escape_string($c, date('Y-m-d', strtotime("-{$range} days")));
    $pf = mysqli_real_escape_string($c, date('Y-m-d', strtotime('-' . ($range * 2) . ' days')));
    $pt = $df;

    // ── Stat cards ────────────────────────────────────────
    $total     = (int) br_scalar($c, "SELECT COUNT(*) FROM bookings WHERE created_at >= '$df'");
    $confirmed = (int) br_scalar($c, "SELECT COUNT(*) FROM bookings WHERE status = 'confirmed' AND created_at >= '$df'");
    $cancelled = (int) br_scalar($c, "SELECT COUNT(*) FROM bookings WHERE status = 'cancelled' AND created_at >= '$df'");
    $pending   = (int) br_scalar($c, "SELECT COUNT(*) FROM bookings WHERE status = 'pending'   AND created_at >= '$df'");

    $as_row   = br_qi($c, "SELECT AVG(DATEDIFF(check_out, check_in)) AS a FROM bookings WHERE created_at >= '$df' AND status != 'cancelled'");
    $avg_stay = $as_row ? round((float)($as_row['a'] ?? 0), 1) : 0;

    $prev      = (int) br_scalar($c, "SELECT COUNT(*) FROM bookings WHERE created_at >= '$pf' AND created_at < '$pt'");
    $trend     = $prev > 0 ? round(($total - $prev) / $prev * 100, 1) : 0;
    $conv_rate = $total > 0 ? round($confirmed / $total * 100, 1) : 0;
    $cx_rate   = $total > 0 ? round($cancelled / $total * 100, 1) : 0;

    // ── Monthly volume (last 8 months, ignores range filter by design) ──
    $m_rows  = br_qa($c,
        "SELECT DATE_FORMAT(created_at,'%b') AS lbl,
                DATE_FORMAT(created_at,'%Y-%m') AS mk,
                COUNT(*) AS nb,
                SUM(status='confirmed') AS cc,
                SUM(status='cancelled') AS xc
         FROM bookings
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 8 MONTH)
         GROUP BY mk, lbl
         ORDER BY mk ASC
         LIMIT 8"
    );
    $monthly = [
        'labels'    => array_column($m_rows, 'lbl'),
        'new'       => array_map('intval', array_column($m_rows, 'nb')),
        'confirmed' => array_map('intval', array_column($m_rows, 'cc')),
        'cancelled' => array_map('intval', array_column($m_rows, 'xc')),
    ];

    // ── Bookings by source ────────────────────────────────
    $s_rows  = br_qa($c,
        "SELECT COALESCE(NULLIF(source,''), 'Unknown') AS sl, COUNT(*) AS cnt
         FROM bookings
         WHERE created_at >= '$df'
         GROUP BY sl
         ORDER BY cnt DESC
         LIMIT 6"
    );
    $sources = [
        'labels' => array_column($s_rows, 'sl'),
        'data'   => array_map('intval', array_column($s_rows, 'cnt')),
    ];

    // ── Top booked units ──────────────────────────────────
    $u_rows = br_qa($c,
        "SELECT u.unit_number,
                p.name AS pn,
                COUNT(b.id) AS bc,
                ROUND(COUNT(b.id) / GREATEST(1, (
                    SELECT COUNT(*) FROM bookings WHERE created_at >= '$df'
                )) * 100) AS rp
         FROM bookings b
         JOIN units u ON b.unit_id = u.id
         JOIN properties p ON u.property_id = p.id
         WHERE b.created_at >= '$df'
         GROUP BY b.unit_id, u.unit_number, p.name
         ORDER BY bc DESC
         LIMIT 5"
    );
    $units = [];
    foreach ($u_rows as $i => $r) {
        $rate    = (int)($r['rp'] ?? 0);
        $units[] = [
            'rank'     => $i + 1,
            'unit'     => $r['unit_number'],
            'property' => $r['pn'],
            'bookings' => (int)$r['bc'],
            'rate'     => $rate . '%',
            'badge'    => $rate >= 80 ? 'success' : ($rate >= 60 ? 'pending' : 'gray'),
        ];
    }

    return [
        'stats' => [
            'total'         => $total,
            'confirmed'     => $confirmed,
            'cancelled'     => $cancelled,
            'pending'       => $pending,
            'avg_stay'      => $avg_stay,
            'booking_trend' => $trend,
            'conv_rate'     => $conv_rate,
            'cancel_rate'   => $cx_rate,
        ],
        'monthly' => $monthly,
        'sources' => $sources,
        'units'   => $units,
    ];
}

/**
 * A cheap fingerprint to detect any change in the bookings table.
 * Does NOT require an updated_at column — uses MAX(id) + COUNT(*).
 */
function br_fingerprint($c) {
    $r = br_qi($c, "SELECT MAX(id) AS mx, COUNT(*) AS cnt FROM bookings");
    return $r ? ($r['mx'] . '|' . $r['cnt']) : '0|0';
}