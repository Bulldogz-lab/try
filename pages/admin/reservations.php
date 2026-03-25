<?php
$page_title = 'Reservations';
$active_page = 'reservations';
include '../../includes/session.php';
include '../../includes/db.php';
if ($_SESSION['role'] !== 'admin') {
    echo '<!DOCTYPE html>
<html>
<head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
<body>
<script>
    Swal.fire({
        icon: "error", title: "Unauthorized",
        text: "You do not have permission to access this page.",
        timer: 1500, showConfirmButton: false, allowOutsideClick: false
    }).then(() => { history.back(); });
</script>
</body>
</html>';
    exit;
}
include '../../includes/layout_open.php';

$statusFilter = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$whereClause = "WHERE 1=1";
if ($statusFilter !== 'all') {
    $statusEsc = mysqli_real_escape_string($conn, $statusFilter);
    $whereClause .= " AND b.status = '$statusEsc'";
}
if ($search !== '') {
    $searchEsc = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND (
        u2.first_name LIKE '%$searchEsc%' OR
        u2.last_name  LIKE '%$searchEsc%' OR
        u2.email      LIKE '%$searchEsc%' OR
        un.unit_name   LIKE '%$searchEsc%' OR
        un.unit_number LIKE '%$searchEsc%' OR
        p.property_name LIKE '%$searchEsc%' OR
        b.booking_id LIKE '%$searchEsc%'
    )";
}

$statsRes = mysqli_query($conn, "
    SELECT
        COUNT(*)                                      AS total,
        SUM(status = 'pending')                       AS pending,
        SUM(status IN ('confirmed','active'))         AS confirmed,
        SUM(status = 'completed')                     AS completed,
        SUM(status = 'cancelled')                     AS cancelled
    FROM bookings
");
$stats = mysqli_fetch_assoc($statsRes);

$bookingsSql = "
    SELECT
        b.booking_id,
        b.checkin_date,
        b.checkout_date,
        b.guests,
        b.total_amount,
        b.status,
        b.created_at,
        DATEDIFF(b.checkout_date, b.checkin_date) AS nights,
        CONCAT(u2.first_name, ' ', u2.last_name)  AS user_name,
        u2.email                                   AS user_email,
        un.unit_name,
        un.unit_number,
        p.property_name
    FROM   bookings b
    JOIN   users      u2 ON u2.user_id      = b.user_id
    JOIN   units      un ON un.unit_id      = b.unit_id
    LEFT JOIN properties p ON p.property_id = un.property_id
    $whereClause
    ORDER  BY b.created_at DESC
";
$bookingsRes = mysqli_query($conn, $bookingsSql);
$bookings = [];
while ($row = mysqli_fetch_assoc($bookingsRes))
    $bookings[] = $row;

function badgeClass($s)
{
    return match ($s) {
        'confirmed', 'active' => 'success',
        'pending' => 'pending',
        'completed' => 'info',
        'cancelled' => 'danger',
        default => 'pending',
    };
}
function badgeLabel($s)
{
    return match ($s) {
        'active' => 'Active',
        'confirmed' => 'Confirmed',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        default => ucfirst($s),
    };
}
function fmtDate($d)
{
    return $d ? date('M j, Y', strtotime($d)) : '—';
}
?>

<link rel="stylesheet" href="../../assets/css/admin-css/reservation.css">

<div class="page-header">
    <div class="top-header">
        <h2>Reservations</h2>
        <div class="page-header-sub">Track all current and upcoming booking requests</div>
    </div>
</div>

<div class="page-inner res-page">
    <div class="cards-area">

        <div class="res-stats" id="statsRow">
            <div class="res-stat">
                <div>
                    <div class="res-stat-label">Total Reservations</div>
                    <div class="res-stat-value" id="stat-total"><?= (int) $stats['total'] ?></div>
                </div>
                <div class="res-stat-icon si-blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
            </div>
            <div class="res-stat">
                <div>
                    <div class="res-stat-label">Pending</div>
                    <div class="res-stat-value" id="stat-pending"><?= (int) $stats['pending'] ?></div>
                </div>
                <div class="res-stat-icon si-gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>
            <div class="res-stat">
                <div>
                    <div class="res-stat-label">Confirmed</div>
                    <div class="res-stat-value" id="stat-confirmed"><?= (int) $stats['confirmed'] ?></div>
                </div>
                <div class="res-stat-icon si-green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
            </div>
            <div class="res-stat">
                <div>
                    <div class="res-stat-label">Cancelled</div>
                    <div class="res-stat-value" id="stat-cancelled"><?= (int) $stats['cancelled'] ?></div>
                </div>
                <div class="res-stat-icon si-red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="15" y1="9" x2="9" y2="15" />
                        <line x1="9" y1="9" x2="15" y2="15" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="res-card">
            <div class="res-card-header">
                <div class="res-card-title">
                    All Reservations
                </div>
                <div class="res-controls">
                    <form method="GET" style="display:contents;">
                        <div class="res-search">
                            <svg viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                placeholder="Search guest, unit…" id="searchInput">
                        </div>
                        <select name="status" class="res-select" onchange="this.form.submit()">
                            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed
                            </option>
                            <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed
                            </option>
                            <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled
                            </option>
                        </select>
                        <?php if ($search): ?>
                            <a href="?status=<?= htmlspecialchars($statusFilter) ?>" class="res-clear-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    style="width:12px;height:12px;">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                                Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="new-booking-banner" id="newBookingBanner" onclick="refreshTable()">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 01-3.46 0" />
                </svg>
                <span id="newBookingText">New booking received! Click to refresh.</span>
                <button class="banner-refresh">Refresh Now</button>
            </div>

            <div class="res-table-wrap">
                <table class="res-table" id="reservationsTable">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Unit</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th style="text-align:center;">Nights</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reservationsTbody">
                        <?php if (empty($bookings)): ?>
                            <tr id="emptyRow">
                                <td colspan="9">
                                    <div class="res-empty">
                                        <svg viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        No reservations found<?= $search ? ' for "' . htmlspecialchars($search) . '"' : '' ?>.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $b):
                                $unitLabel = !empty($b['unit_name'])
                                    ? $b['unit_name']
                                    : (($b['property_name'] ?? '') . ' — Unit ' . ($b['unit_number'] ?? ''));
                                ?>
                                <tr data-id="<?= $b['booking_id'] ?>">
                                    <td><span
                                            class="booking-id">#BK-<?= str_pad($b['booking_id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td>
                                        <div class="guest-cell">
                                            <div class="guest-avatar"><?= strtoupper(substr($b['user_name'], 0, 1)) ?></div>
                                            <div>
                                                <div class="guest-name"><?= htmlspecialchars($b['user_name']) ?></div>
                                                <div class="guest-email"><?= htmlspecialchars($b['user_email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="unit-name"><?= htmlspecialchars($unitLabel) ?></div>
                                        <div class="unit-prop"><?= htmlspecialchars($b['property_name'] ?? '') ?></div>
                                    </td>
                                    <td><?= fmtDate($b['checkin_date']) ?></td>
                                    <td><?= fmtDate($b['checkout_date']) ?></td>
                                    <td style="text-align:center;font-weight:700;"><?= (int) $b['nights'] ?></td>
                                    <td><span class="amount-cell">₱<?= number_format((float) $b['total_amount'], 0) ?></span>
                                    </td>
                                    <td>
                                        <span class="res-badge res-badge-<?= badgeClass($b['status']) ?>">
                                            <?= badgeLabel($b['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                            <?php if ($b['status'] === 'pending'): ?>
                                                <button class="action-btn btn-confirm"
                                                    onclick="updateStatus(<?= $b['booking_id'] ?>, 'confirmed', this)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                    Confirm
                                                </button>
                                                <button class="action-btn btn-cancel"
                                                    onclick="updateStatus(<?= $b['booking_id'] ?>, 'cancelled', this)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <line x1="18" y1="6" x2="6" y2="18" />
                                                        <line x1="6" y1="6" x2="18" y2="18" />
                                                    </svg>
                                                    Cancel
                                                </button>
                                            <?php elseif ($b['status'] === 'confirmed'): ?>
                                                <button class="action-btn btn-complete"
                                                    onclick="updateStatus(<?= $b['booking_id'] ?>, 'completed', this)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                    Complete
                                                </button>
                                                <button class="action-btn btn-cancel"
                                                    onclick="updateStatus(<?= $b['booking_id'] ?>, 'cancelled', this)">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <line x1="18" y1="6" x2="6" y2="18" />
                                                        <line x1="6" y1="6" x2="18" y2="18" />
                                                    </svg>
                                                    Cancel
                                                </button>
                                            <?php else: ?>
                                                <span style="font-size:12px;color:#cbd5e1;">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="res-pagination" id="paginationBar">
                <span class="res-pagination-info" id="footerCount">
                    Showing <strong><?= count($bookings) ?></strong> reservation<?= count($bookings) !== 1 ? 's' : '' ?>
                    <?= $statusFilter !== 'all' ? '· filtered by <strong>' . htmlspecialchars(ucfirst($statusFilter)) . '</strong>' : '' ?>
                    <?= $search ? '· search: <strong>' . htmlspecialchars($search) . '</strong>' : '' ?>
                </span>
                <div class="res-pagination-btns" id="paginationBtns"></div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const currentStatus = '<?= $statusFilter ?>';
    const currentSearch = '<?= addslashes($search) ?>';

    let knownIds = new Set(
        [...document.querySelectorAll('#reservationsTbody tr[data-id]')]
            .map(r => r.dataset.id)
    );
    let lastKnownCount = knownIds.size;

    function updateStatus(bookingId, newStatus, btn) {
        const labels = { confirmed: 'confirm', cancelled: 'cancel', completed: 'mark as completed' };
        const colors = { confirmed: '#16a34a', cancelled: '#dc2626', completed: '#2563eb' };

        Swal.fire({
            title: `${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)} Booking?`,
            text: `Are you sure you want to ${labels[newStatus]} booking #BK-${String(bookingId).padStart(4, '0')}?`,
            icon: newStatus === 'cancelled' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: colors[newStatus],
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Yes, ${labels[newStatus]}`,
            cancelButtonText: 'No, go back'
        }).then(result => {
            if (!result.isConfirmed) return;
            Swal.fire({ title: 'Updating…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('../../process/admin-process/update_booking_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ booking_id: bookingId, status: newStatus })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, timer: 1200, showConfirmButton: false })
                            .then(() => refreshTable());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Could not update booking.' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Server unreachable. Please try again.' }));
        });
    }

    function refreshTable() {
        const banner = document.getElementById('newBookingBanner');
        banner.classList.remove('show');
        const params = new URLSearchParams({ ajax: '1', status: currentStatus, search: currentSearch });
        fetch('../../process/admin-process/fetch_reservations.php?' + params)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                renderRows(data.bookings);
                updateStats(data.stats);
            })
            .catch(() => { });
    }

    function rowHtml(b) {
        const unitLabel = b.unit_name || ((b.property_name || '') + ' — Unit ' + (b.unit_number || ''));
        const initials = (b.user_name || '?').charAt(0).toUpperCase();
        const padId = String(b.booking_id).padStart(4, '0');
        const isNew = !knownIds.has(String(b.booking_id));

        let actions = '<span style="font-size:12px;color:#cbd5e1;">—</span>';
        if (b.status === 'pending') {
            actions = `
            <button class="action-btn btn-confirm" onclick="updateStatus(${b.booking_id},'confirmed',this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Confirm
            </button>
            <button class="action-btn btn-cancel" onclick="updateStatus(${b.booking_id},'cancelled',this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Cancel
            </button>`;
        } else if (b.status === 'confirmed') {
            actions = `
            <button class="action-btn btn-complete" onclick="updateStatus(${b.booking_id},'completed',this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Complete
            </button>
            <button class="action-btn btn-cancel" onclick="updateStatus(${b.booking_id},'cancelled',this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Cancel
            </button>`;
        }

        const badgeMap = { confirmed: 'success', active: 'success', pending: 'pending', completed: 'info', cancelled: 'danger' };
        const badgeLabelMap = { active: 'Active', confirmed: 'Confirmed', pending: 'Pending', completed: 'Completed', cancelled: 'Cancelled' };
        const badgeCls = badgeMap[b.status] || 'pending';
        const badgeTxt = badgeLabelMap[b.status] || b.status;

        return `<tr data-id="${b.booking_id}"${isNew ? ' class="row-new"' : ''}>
        <td><span class="booking-id">#BK-${padId}</span></td>
        <td>
            <div class="guest-cell">
                <div class="guest-avatar">${initials}</div>
                <div>
                    <div class="guest-name">${escHtml(b.user_name)}</div>
                    <div class="guest-email">${escHtml(b.user_email)}</div>
                </div>
            </div>
        </td>
        <td>
            <div class="unit-name">${escHtml(unitLabel)}</div>
            <div class="unit-prop">${escHtml(b.property_name || '')}</div>
        </td>
        <td>${escHtml(b.checkin_date)}</td>
        <td>${escHtml(b.checkout_date)}</td>
        <td style="text-align:center;font-weight:700;">${b.nights}</td>
        <td><span class="amount-cell">₱${Number(b.total_amount).toLocaleString('en-PH', { maximumFractionDigits: 0 })}</span></td>
        <td><span class="res-badge res-badge-${badgeCls}">${badgeTxt}</span></td>
        <td><div style="display:flex;gap:6px;flex-wrap:wrap;">${actions}</div></td>
    </tr>`;
    }

    function renderRows(bookings) {
        knownIds = new Set(bookings.map(b => String(b.booking_id)));
        paginateRows(bookings);
    }

    function updateStats(stats) {
        if (!stats) return;
        animateCount('stat-total', parseInt(stats.total) || 0);
        animateCount('stat-pending', parseInt(stats.pending) || 0);
        animateCount('stat-confirmed', parseInt(stats.confirmed) || 0);
        animateCount('stat-cancelled', parseInt(stats.cancelled) || 0);
    }

    function animateCount(id, target) {
        const el = document.getElementById(id);
        if (!el) return;
        const start = parseInt(el.textContent) || 0;
        if (start === target) return;
        const duration = 600, step = 16;
        const steps = duration / step;
        const inc = (target - start) / steps;
        let current = start, count = 0;
        const timer = setInterval(() => {
            count++;
            current += inc;
            el.textContent = Math.round(current);
            if (count >= steps) { el.textContent = target; clearInterval(timer); }
        }, step);
    }

    function pollForNewBookings() {
        fetch('../../process/admin-process/fetch_reservations.php?ajax=1&status=' + currentStatus + '&search=' + encodeURIComponent(currentSearch))
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const incoming = new Set(data.bookings.map(b => String(b.booking_id)));
                let newCount = 0;
                incoming.forEach(id => { if (!knownIds.has(id)) newCount++; });

                if (newCount > 0) {
                    const banner = document.getElementById('newBookingBanner');
                    document.getElementById('newBookingText').textContent =
                        `${newCount} new booking${newCount > 1 ? 's' : ''} received! Click to refresh.`;
                    banner.classList.add('show');
                    updateStats(data.stats);
                } else {
                    renderRows(data.bookings, false);
                    updateStats(data.stats);
                }
            })
            .catch(() => { });
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    const searchInput = document.getElementById('searchInput');
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => searchInput.closest('form').submit(), 500);
    });

    const PER_PAGE = 10;
    let currentPage = 1;
    let allRows = [];

    function paginateRows(rows) {
        allRows = rows;
        currentPage = 1;
        renderPage();
    }

    function renderPage() {
        const tbody = document.getElementById('reservationsTbody');
        const total = allRows.length;
        const pages = Math.max(1, Math.ceil(total / PER_PAGE));
        currentPage = Math.min(currentPage, pages);
        const start = (currentPage - 1) * PER_PAGE;
        const slice = allRows.slice(start, start + PER_PAGE);

        if (total === 0) {
            tbody.innerHTML = `<tr><td colspan="9">
            <div class="res-empty">
                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <p>No reservations found.</p>
            </div>
        </td></tr>`;
            document.getElementById('footerCount').innerHTML = 'Showing <strong>0</strong> reservations';
            document.getElementById('paginationBtns').innerHTML = '';
            return;
        }
        tbody.innerHTML = slice.map(rowHtml).join('');

        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(start + PER_PAGE, total);
        document.getElementById('footerCount').innerHTML =
            `Showing <strong>${from}–${to}</strong> of <strong>${total}</strong> reservation${total !== 1 ? 's' : ''}`;

        const btns = document.getElementById('paginationBtns');
        if (pages <= 1) { btns.innerHTML = ''; return; }

        let html = `<button class="pg-btn" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </button>`;

        let pagesToShow = [];
        if (pages <= 5) {
            pagesToShow = Array.from({ length: pages }, (_, i) => i + 1);
        } else {
            pagesToShow = [1];
            if (currentPage > 3) pagesToShow.push('…');
            for (let i = Math.max(2, currentPage - 1); i <= Math.min(pages - 1, currentPage + 1); i++) {
                pagesToShow.push(i);
            }
            if (currentPage < pages - 2) pagesToShow.push('…');
            pagesToShow.push(pages);
        }

        pagesToShow.forEach(p => {
            if (p === '…') {
                html += `<span class="pg-btn" style="cursor:default;border:none;">…</span>`;
            } else {
                html += `<button class="pg-btn ${p === currentPage ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
            }
        });

        html += `<button class="pg-btn" onclick="goPage(${currentPage + 1})" ${currentPage === pages ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    </button>`;

        btns.innerHTML = html;
    }

    function goPage(p) {
        const pages = Math.ceil(allRows.length / PER_PAGE);
        if (p < 1 || p > pages) return;
        currentPage = p;
        renderPage();
        document.getElementById('reservationsTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    allRows = <?= json_encode(array_map(function ($b) {
        return [
            'booking_id' => $b['booking_id'],
            'checkin_date' => fmtDate($b['checkin_date']),
            'checkout_date' => fmtDate($b['checkout_date']),
            'nights' => $b['nights'],
            'guests' => $b['guests'],
            'total_amount' => $b['total_amount'],
            'status' => $b['status'],
            'user_name' => $b['user_name'],
            'user_email' => $b['user_email'],
            'unit_name' => $b['unit_name'] ?? null,
            'unit_number' => $b['unit_number'] ?? null,
            'property_name' => $b['property_name'] ?? null,
        ];
    }, $bookings), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    knownIds = new Set(allRows.map(b => String(b.booking_id)));
    renderPage();

    setInterval(pollForNewBookings, 10000);
</script>

<?php include '../../includes/layout_close.php'; ?>