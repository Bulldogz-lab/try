<?php
$page_title  = 'Check-in / Check-out';
$active_page = 'checkin_checkout';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

if ($_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

// ── AJAX: monthly activity for calendar dots ──────────────
if (isset($_GET['ajax_activity'])) {
    include '../../includes/db.php';
    header('Content-Type: application/json');
    $y = (int)($_GET['year']  ?? date('Y'));
    $m = (int)($_GET['month'] ?? date('m'));
    $start = sprintf('%04d-%02d-01', $y, $m);
    $end   = date('Y-m-t', strtotime($start));
    $res   = mysqli_query($conn, "
        SELECT DATE(checkin_date) AS ci, DATE(checkout_date) AS co
        FROM bookings WHERE status NOT IN ('cancelled')
        AND (checkin_date BETWEEN '$start' AND '$end'
          OR checkout_date BETWEEN '$start' AND '$end')
    ");
    $ci = []; $co = [];
    while ($r = mysqli_fetch_assoc($res)) {
        if ($r['ci'] >= $start && $r['ci'] <= $end) $ci[] = (int)date('j', strtotime($r['ci']));
        if ($r['co'] >= $start && $r['co'] <= $end) $co[] = (int)date('j', strtotime($r['co']));
    }
    echo json_encode(['ci' => array_values(array_unique($ci)), 'co' => array_values(array_unique($co))]);
    exit;
}

// ── Selected date ─────────────────────────────────────────
$selected_date = $_GET['date'] ?? date('Y-m-d');
if (!strtotime($selected_date)) $selected_date = date('Y-m-d');
$dateEsc   = mysqli_real_escape_string($conn, $selected_date);
$dateLabel = date('F j, Y', strtotime($selected_date));
$isToday   = ($selected_date === date('Y-m-d'));

// ── Check-ins (checkin_date = selected date) ──────────────
$ci_sql = "
    SELECT b.booking_id, b.checkin_date, b.checkout_date, b.status, b.guests,
           CONCAT(u.first_name,' ',u.last_name) AS guest_name,
           u.email,
           COALESCE(un.unit_name, CONCAT(p.property_name,' — ',un.unit_number)) AS unit_label,
           p.property_name,
           IF(b.status = 'active', 'done', 'pending') AS checkin_status
    FROM   bookings b
    JOIN   users u  ON u.user_id  = b.user_id
    JOIN   units un ON un.unit_id = b.unit_id
    LEFT JOIN properties p ON p.property_id = un.property_id
    WHERE  b.checkin_date = '$dateEsc'
      AND  b.status NOT IN ('cancelled')
    ORDER  BY b.checkin_date ASC
";
$ci_res   = mysqli_query($conn, $ci_sql);
$checkins = [];
while ($row = mysqli_fetch_assoc($ci_res)) $checkins[] = $row;

// ── Check-outs (checkout_date = selected date) ────────────
$co_sql = "
    SELECT b.booking_id, b.checkin_date, b.checkout_date, b.status, b.guests,
           CONCAT(u.first_name,' ',u.last_name) AS guest_name,
           u.email,
           COALESCE(un.unit_name, CONCAT(p.property_name,' — ',un.unit_number)) AS unit_label,
           p.property_name,
           IF(b.status = 'completed', 'done', 'pending') AS checkout_status
    FROM   bookings b
    JOIN   users u  ON u.user_id  = b.user_id
    JOIN   units un ON un.unit_id = b.unit_id
    LEFT JOIN properties p ON p.property_id = un.property_id
    WHERE  b.checkout_date = '$dateEsc'
      AND  b.status NOT IN ('cancelled')
    ORDER  BY b.checkout_date ASC
";
$co_res    = mysqli_query($conn, $co_sql);
$checkouts = [];
while ($row = mysqli_fetch_assoc($co_res)) $checkouts[] = $row;

// ── Currently staying ─────────────────────────────────────
$stay_res = mysqli_query($conn, "
    SELECT COUNT(*) AS cnt FROM bookings
    WHERE  status NOT IN ('cancelled','completed')
      AND  checkin_date  <= '$dateEsc'
      AND  checkout_date >= '$dateEsc'
");
$staying = (int)mysqli_fetch_assoc($stay_res)['cnt'];

// ── Stats ─────────────────────────────────────────────────
$ci_done    = count(array_filter($checkins,  fn($r) => ($r['checkin_status']  ?? '') === 'done'));
$co_done    = count(array_filter($checkouts, fn($r) => ($r['checkout_status'] ?? '') === 'done'));
$overdue    = count(array_filter($checkouts, fn($r) => ($r['checkout_status'] ?? '') !== 'done' && $selected_date > date('Y-m-d')));
// overdue = past checkout date and not yet done
$today_str  = date('Y-m-d');
$overdue    = count(array_filter($checkouts, fn($r) =>
    ($r['checkout_status'] ?? '') !== 'done' && $selected_date < $today_str
));

// ── Monthly activity for calendar dots ───────────────────
$cal_year  = date('Y', strtotime($selected_date));
$cal_month = date('m', strtotime($selected_date));
$cal_start = "$cal_year-$cal_month-01";
$cal_end   = date('Y-m-t', strtotime($selected_date));

$act_sql = "
    SELECT
        DATE(checkin_date)  AS ci_date,
        DATE(checkout_date) AS co_date
    FROM bookings
    WHERE status NOT IN ('cancelled')
      AND (
          (checkin_date  BETWEEN '$cal_start' AND '$cal_end') OR
          (checkout_date BETWEEN '$cal_start' AND '$cal_end')
      )
";
$act_res    = mysqli_query($conn, $act_sql);
$ci_days    = []; $co_days = [];
while ($row = mysqli_fetch_assoc($act_res)) {
    if ($row['ci_date'] >= $cal_start && $row['ci_date'] <= $cal_end)
        $ci_days[] = (int)date('j', strtotime($row['ci_date']));
    if ($row['co_date'] >= $cal_start && $row['co_date'] <= $cal_end)
        $co_days[] = (int)date('j', strtotime($row['co_date']));
}
$ci_days = array_unique($ci_days);
$co_days = array_unique($co_days);
function ciStatusLabel($row) {
    $s = $row['checkin_status'] ?? '';
    return match($s) {
        'done'  => ['Done',     'success'],
        default => ['Expected', 'pending'],
    };
}
function coStatusLabel($row, $selectedDate) {
    $s = $row['checkout_status'] ?? '';
    if ($s === 'done') return ['Done', 'success'];
    if ($selectedDate < date('Y-m-d')) return ['Overdue', 'danger'];
    return ['Pending', 'pending'];
}
?>

<link rel="stylesheet" href="../../assets/css/admin-css/checkin_checkout.css">

<div class="page-header">
    <div class="top-header">
        <h2>Check-in / Check-out</h2>
        <div class="page-header-sub">
            <?= $isToday ? "Today's" : htmlspecialchars($dateLabel) ?> guest arrivals and departures
        </div>
    </div>
    <!-- Calendar date picker -->
    <div class="date-nav">
        <?php
        $prev = date('Y-m-d', strtotime($selected_date . ' -1 day'));
        $next = date('Y-m-d', strtotime($selected_date . ' +1 day'));
        ?>
        <a href="?date=<?= $prev ?>" class="date-nav-btn" title="Previous day">‹</a>

        <div class="cal-picker-wrap">
            <div class="cal-picker-trigger" id="calTrigger" onclick="toggleCalPicker()">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= date('M j, Y', strtotime($selected_date)) ?>
                <span class="cal-trigger-arrow">▼</span>
            </div>
            <div class="cal-dropdown" id="calDropdown">
                <div class="cal-drop-header">
                    <div class="cal-drop-month" id="calDropMonth"></div>
                    <div class="cal-drop-nav">
                        <button onclick="calNavMonth(-1)">‹</button>
                        <button onclick="calNavMonth(1)">›</button>
                    </div>
                </div>
                <div class="cal-drop-dow">
                    <?php foreach (['S','M','T','W','T','F','S'] as $d): ?>
                        <span><?= $d ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="cal-drop-grid" id="calDropGrid"></div>
            </div>
        </div>

        <a href="?date=<?= $next ?>" class="date-nav-btn" title="Next day">›</a>
        <a href="?" class="date-today-btn <?= $isToday ? 'active' : '' ?>">Today</a>
    </div>
</div>

<div class="page-inner">
    <div class="cards-area">

        <!-- ── STATS ── -->
        <div class="stat-row">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Check-ins</div>
                    <div class="stat-value"><?= count($checkins) ?></div>
                    <div class="stat-sub"><?= $ci_done ?> completed</div>
                </div>
                <div class="stat-icon-wrap green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Check-outs</div>
                    <div class="stat-value"><?= count($checkouts) ?></div>
                    <div class="stat-sub"><?= $co_done ?> completed</div>
                </div>
                <div class="stat-icon-wrap gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Currently Staying</div>
                    <div class="stat-value"><?= $staying ?></div>
                </div>
                <div class="stat-icon-wrap blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Overdue Check-outs</div>
                    <div class="stat-value"><?= $overdue ?></div>
                </div>
                <div class="stat-icon-wrap red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ── TWO COLUMN ── -->
        <div class="two-col">

            <!-- Check-ins -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Check-ins</span>
                    <span class="badge" style="background:#dcfce7;color:#166534;">
                        <?= count($checkins) ?> arrival<?= count($checkins) !== 1 ? 's' : '' ?>
                    </span>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                <?php if (empty($checkins)): ?>
                    <div class="section-empty">
                        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        No check-ins for <?= htmlspecialchars($dateLabel) ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($checkins as $row):
                        [$label, $cls] = ciStatusLabel($row);
                        $isDone = ($row['checkin_status'] ?? '') === 'done';
                        $nights = (int)((strtotime($row['checkout_date']) - strtotime($row['checkin_date'])) / 86400);
                    ?>
                    <div class="guest-row" id="ci-row-<?= $row['booking_id'] ?>">
                        <div class="guest-avatar-lg">
                            <?= strtoupper(substr($row['guest_name'], 0, 1)) ?>
                        </div>
                        <div class="guest-info">
                            <div class="guest-name"><?= htmlspecialchars($row['guest_name']) ?></div>
                            <div class="guest-meta">
                                <?= htmlspecialchars($row['unit_label']) ?>
                                · <?= $nights ?> night<?= $nights !== 1 ? 's' : '' ?>
                                · <?= $row['guests'] ?> guest<?= $row['guests'] > 1 ? 's' : '' ?>
                            </div>
                        </div>
                        <div class="guest-actions">
                            <?php if (!$isDone): ?>
                            <button class="act-btn act-btn-checkin"
                                onclick="processAction(<?= $row['booking_id'] ?>, 'checkin')"
                                id="ci-btn-<?= $row['booking_id'] ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Check In
                            </button>
                            <?php else: ?>
                            <span class="badge badge-success">✓ Done</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>

            <!-- Check-outs -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Check-outs</span>
                    <span class="badge" style="background:#fef9c3;color:#854d0e;">
                        <?= count($checkouts) ?> departure<?= count($checkouts) !== 1 ? 's' : '' ?>
                    </span>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                <?php if (empty($checkouts)): ?>
                    <div class="section-empty">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        No check-outs for <?= htmlspecialchars($dateLabel) ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($checkouts as $row):
                        [$label, $cls] = coStatusLabel($row, $selected_date);
                        $isDone = ($row['checkout_status'] ?? '') === 'done';
                        $nights = (int)((strtotime($row['checkout_date']) - strtotime($row['checkin_date'])) / 86400);
                    ?>
                    <div class="guest-row" id="co-row-<?= $row['booking_id'] ?>">
                        <div class="guest-avatar-lg">
                            <?= strtoupper(substr($row['guest_name'], 0, 1)) ?>
                        </div>
                        <div class="guest-info">
                            <div class="guest-name"><?= htmlspecialchars($row['guest_name']) ?></div>
                            <div class="guest-meta">
                                <?= htmlspecialchars($row['unit_label']) ?>
                                · <?= $nights ?> night<?= $nights !== 1 ? 's' : '' ?>
                                · <?= $row['guests'] ?> guest<?= $row['guests'] > 1 ? 's' : '' ?>
                            </div>
                        </div>
                        <div class="guest-actions">
                            <?php if (!$isDone): ?>
                            <span class="badge badge-<?= $cls ?>"><?= $label ?></span>
                            <button class="act-btn act-btn-checkout"
                                onclick="processAction(<?= $row['booking_id'] ?>, 'checkout')"
                                id="co-btn-<?= $row['booking_id'] ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Check Out
                            </button>
                            <?php else: ?>
                            <span class="badge badge-success">✓ Done</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>

        </div><!-- /two-col -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function processAction(bookingId, type) {
    const isCi    = type === 'checkin';
    const label   = isCi ? 'Check In' : 'Check Out';
    const color   = isCi ? '#16a34a' : '#b45309';
    const rowId   = (isCi ? 'ci' : 'co') + '-row-' + bookingId;
    const btnId   = (isCi ? 'ci' : 'co') + '-btn-' + bookingId;

    Swal.fire({
        title: `Confirm ${label}?`,
        text:  `Mark booking #BK-${String(bookingId).padStart(4,'0')} as ${label.toLowerCase()}?`,
        icon:  'question',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${label}`,
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Processing…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch('../../process/admin-process/process_checkin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ booking_id: bookingId, action: type })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Done!', text: data.message, timer: 1400, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Could not process.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Server unreachable.' }));
    });
}

const selectedDate = '<?= $selected_date ?>';
const ciDays       = <?= json_encode(array_values($ci_days)) ?>;
const coDays       = <?= json_encode(array_values($co_days)) ?>;
const todayStr     = '<?= date('Y-m-d') ?>';

let calYear  = <?= $cal_year ?>;
let calMonth = <?= (int)$cal_month ?>;

function toggleCalPicker() {
    const trigger  = document.getElementById('calTrigger');
    const dropdown = document.getElementById('calDropdown');
    const isOpen   = dropdown.classList.contains('open');
    if (isOpen) {
        dropdown.classList.remove('open');
        trigger.classList.remove('open');
    } else {
        renderCalDrop(calYear, calMonth);
        dropdown.classList.add('open');
        trigger.classList.add('open');
    }
}

function calNavMonth(dir) {
    calMonth += dir;
    if (calMonth < 1)  { calMonth = 12; calYear--; }
    if (calMonth > 12) { calMonth = 1;  calYear++; }
    fetchMonthActivity(calYear, calMonth, () => renderCalDrop(calYear, calMonth));
}

const activityCache = {
    '<?= "$cal_year-$cal_month" ?>': { ci: ciDays, co: coDays }
};

function fetchMonthActivity(year, month, cb) {
    const key = `${year}-${month}`;
    if (activityCache[key]) { cb(); return; }
    fetch(`?ajax_activity=1&year=${year}&month=${month}`)
        .then(r => r.json())
        .then(data => {
            activityCache[key] = data;
            cb();
        })
        .catch(() => { activityCache[key] = {ci:[], co:[]}; cb(); });
}

function renderCalDrop(year, month) {
    const key      = `${year}-${month}`;
    const activity = activityCache[key] || { ci: [], co: [] };
    const months   = ['January','February','March','April','May','June',
                      'July','August','September','October','November','December'];
    document.getElementById('calDropMonth').textContent = `${months[month-1]} ${year}`;

    const firstDow    = new Date(year, month - 1, 1).getDay();
    const daysInMonth = new Date(year, month, 0).getDate();
    const grid        = document.getElementById('calDropGrid');
    let html = '';

    for (let i = 0; i < firstDow; i++) {
        html += `<div class="cal-drop-day empty"></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const padM    = String(month).padStart(2,'0');
        const padD    = String(d).padStart(2,'0');
        const dateStr = `${year}-${padM}-${padD}`;
        const isToday    = dateStr === todayStr;
        const isSel      = dateStr === selectedDate;
        const hasCi      = activity.ci.includes(d);
        const hasCo      = activity.co.includes(d);

        let cls = 'cal-drop-day';
        if (isToday) cls += ' today';
        if (isSel)   cls += ' selected';

        let dots = '';
        if (hasCi || hasCo) {
            dots = `<div class="cal-drop-dots">
                ${hasCi ? '<span class="cal-drop-dot ci"></span>' : ''}
                ${hasCo ? '<span class="cal-drop-dot co"></span>' : ''}
            </div>`;
        }

        html += `<div class="${cls}" onclick="pickDate('${dateStr}')">${d}${dots}</div>`;
    }

    grid.innerHTML = html;
}

function pickDate(dateStr) {
    window.location = '?date=' + dateStr;
}

document.addEventListener('click', e => {
    const wrap = document.querySelector('.cal-picker-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('calDropdown').classList.remove('open');
        document.getElementById('calTrigger').classList.remove('open');
    }
});
</script>

<?php include '../../includes/layout_close.php'; ?>