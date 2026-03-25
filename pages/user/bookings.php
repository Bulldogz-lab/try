<?php
include '../../includes/session.php';
if ($_SESSION['role'] !== 'user') {
    echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: "error",
        title: "Unauthorized",
        text: "You do not have permission to access this page.",
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        history.back();
    });
</script>
</body>
</html>';
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['name'] ?? 'Guest');
$last_name = htmlspecialchars($_SESSION['last_name'] ?? '');
$full_name = trim($first_name . ' ' . $last_name);
$email = htmlspecialchars($_SESSION['email'] ?? '');
$initials = strtoupper(mb_substr($first_name, 0, 1) . mb_substr($last_name, 0, 1));
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$page_title = 'My Bookings';
$page_hero_html = 'My <em>Bookings</em>';
$page_hero_sub = 'View, manage, and track all your reservations.';
$page_hero_icon = '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>';
$active_nav = 'bookings';
require '../../includes/_layout.php';

$bookings = [
    ['id' => 'FH-2025-0061', 'room' => 'Garden Breeze Room', 'floor' => '2nd Floor · Room 5B', 'checkin' => 'Jul 12, 2025', 'checkout' => 'Jul 14, 2025', 'nights' => 2, 'total' => 5600, 'status' => 'cancelled', 'img' => '../../assets/images/unit2.jpg', 'fallback' => 'background:linear-gradient(145deg,#dbeafe,#3b82f6,#1a3d7c)'],
];
$status_map = [
    'upcoming' => ['label' => 'Upcoming', 'class' => 'badge-blue'],
    'completed' => ['label' => 'Completed', 'class' => 'badge-green'],
    'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-red'],
];
?>

<link rel="stylesheet" href="../../assets/css/user-css/booking.css">

<div class="summary-strip reveal">
    <div class="sstat">
        <div class="sstat-num">1</div>
        <div class="sstat-lbl">Upcoming</div>
    </div>
    <div class="sstat">
        <div class="sstat-num">3</div>
        <div class="sstat-lbl">Completed</div>
    </div>
    <div class="sstat">
        <div class="sstat-num">1</div>
        <div class="sstat-lbl">Cancelled</div>
    </div>
    <div class="sstat">
        <div class="sstat-num">₱63,700</div>
        <div class="sstat-lbl">Total Spent</div>
    </div>
</div>

<div class="tab-bar reveal rd1" id="tabBar">
    <button class="tab-btn active" onclick="filterBookings('all',this)">All</button>
    <button class="tab-btn" onclick="filterBookings('upcoming',this)">Upcoming</button>
    <button class="tab-btn" onclick="filterBookings('completed',this)">Completed</button>
    <button class="tab-btn" onclick="filterBookings('cancelled',this)">Cancelled</button>
</div>

<div id="bookingsList">
    <?php foreach ($bookings as $i => $b):
        $s = $status_map[$b['status']];
        $delay = $i < 3 ? " rd{$i}" : '';
        ?>
        <div class="booking-card reveal<?php echo $delay; ?><?php echo $b['status'] === 'cancelled' ? ' cancelled' : ''; ?>"
            data-status="<?php echo $b['status']; ?>">
            <div class="bc-top">
                <div class="bc-img">
                    <img src="<?php echo htmlspecialchars($b['img']); ?>" alt="<?php echo htmlspecialchars($b['room']); ?>"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    <div class="bc-img-fallback" style="display:none;<?php echo $b['fallback']; ?>"></div>
                </div>
                <div class="bc-body">
                    <div class="bc-head">
                        <div>
                            <div class="bc-room"><?php echo $b['room']; ?></div>
                            <div class="bc-floor"><?php echo $b['floor']; ?></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
                            <span class="bc-id"><?php echo $b['id']; ?></span>
                        </div>
                    </div>
                    <div class="bc-dates">
                        <div class="bc-date-item">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Check-in: <strong><?php echo $b['checkin']; ?></strong>
                        </div>
                        <div class="bc-date-sep"></div>
                        <div class="bc-date-item">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Check-out: <strong><?php echo $b['checkout']; ?></strong>
                        </div>
                        <span class="bc-nights"><?php echo $b['nights']; ?> nights</span>
                    </div>
                </div>
            </div>
            <div class="bc-foot">
                <div class="bc-price">₱<?php echo number_format($b['total']); ?> <sub>total</sub></div>
                <div class="bc-actions">
                    <?php if ($b['status'] === 'upcoming'): ?>
                        <button class="bc-btn-ghost" onclick="openDetailsModal(<?php echo $i; ?>)">View Details</button>
                        <button class="bc-btn-primary" onclick="downloadInvoice('<?php echo $b['id']; ?>')">Download
                            Invoice</button>
                    <?php elseif ($b['status'] === 'completed'): ?>
                        <button class="bc-btn-ghost" onclick="openReviewModal('<?php echo addslashes($b['room']); ?>')">Leave a
                            Review</button>
                        <button class="bc-btn-primary" onclick="openRebookModal('<?php echo addslashes($b['room']); ?>')">Book
                            Again</button>
                    <?php else: ?>
                        <button class="bc-btn-ghost" style="cursor:default;opacity:0.45;" disabled>Cancelled</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="modal-overlay" id="detailsModal">
    <div class="modal-box" style="max-width:560px;">
        <button class="modal-close-btn" onclick="closeModal('detailsModal')">✕</button>
        <div class="modal-title" id="detailsRoomName"></div>
        <div class="modal-sub" id="detailsBookingId"></div>
        <div id="detailsBody" style="margin-bottom:22px;"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-secondary" onclick="closeModal('detailsModal')">Close</button>
            <button class="btn-primary" onclick="downloadInvoiceFromModal()">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download Invoice
            </button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="reviewModal">
    <div class="modal-box" style="max-width:480px;">
        <button class="modal-close-btn" onclick="closeModal('reviewModal')">✕</button>
        <div class="modal-title">Leave a Review</div>
        <div class="modal-sub" id="reviewRoomName"></div>
        <div style="margin-bottom:18px;">
            <div
                style="font-size:0.72rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-mid);margin-bottom:10px;">
                Your Rating</div>
            <div id="starRating" style="display:flex;gap:6px;">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                    <svg data-val="<?php echo $s; ?>" onclick="setRating(<?php echo $s; ?>)" viewBox="0 0 24 24"
                        style="width:32px;height:32px;fill:var(--blue-100);stroke:var(--blue-200);stroke-width:1.5;cursor:pointer;transition:fill 0.15s,transform 0.15s;"
                        onmouseover="hoverRating(<?php echo $s; ?>)" onmouseout="resetHover()">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                <?php endfor; ?>
            </div>
        </div>
        <div class="form-field" style="margin-bottom:18px;">
            <label>Your Review</label>
            <textarea id="reviewText" placeholder="Share your experience staying at Filipino Homes..."></textarea>
        </div>
        <div id="reviewError"
            style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-secondary" onclick="closeModal('reviewModal')">Cancel</button>
            <button class="btn-primary" id="submitReviewBtn" onclick="submitReview()">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                Submit Review
            </button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="rebookModal">
    <div class="modal-box" style="max-width:460px;">
        <button class="modal-close-btn" onclick="closeModal('rebookModal')">✕</button>
        <div class="modal-title">Book Again</div>
        <div class="modal-sub" id="rebookRoomName"></div>
        <div class="form-grid" style="margin-bottom:14px;">
            <div class="form-field">
                <label>Check-in Date</label>
                <input type="date" id="rebook_checkin">
            </div>
            <div class="form-field">
                <label>Check-out Date</label>
                <input type="date" id="rebook_checkout">
            </div>
        </div>
        <div class="form-field" style="margin-bottom:18px;">
            <label>Guests</label>
            <select id="rebook_guests">
                <option value="1">1 Guest</option>
                <option value="2" selected>2 Guests</option>
                <option value="3">3 Guests</option>
                <option value="4">4 Guests</option>
            </select>
        </div>
        <div id="rebookError"
            style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-secondary" onclick="closeModal('rebookModal')">Cancel</button>
            <button class="btn-primary" id="rebookConfirmBtn" onclick="confirmRebook()">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                Confirm Booking
            </button>
        </div>
    </div>
</div>

<script>
    const bookingsData = <?php echo json_encode($bookings); ?>;
    let currentInvoiceId = null;
    let selectedRating = 0;

    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

    function openDetailsModal(idx) {
        const b = bookingsData[idx];
        currentInvoiceId = b.id;
        document.getElementById('detailsRoomName').textContent = b.room;
        document.getElementById('detailsBookingId').textContent = 'Booking ID: ' + b.id;
        document.getElementById('detailsBody').innerHTML = `
        <div style="width:100%;height:160px;border-radius:12px;overflow:hidden;margin-bottom:18px;background:linear-gradient(145deg,#93c5fd,#2563c4);">
            <img src="${b.img}" alt="${b.room}"
                 style="width:100%;height:100%;object-fit:cover;display:block;"
                 onerror="this.style.display='none'">
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
            <tr style="border-bottom:1px solid var(--blue-50);">
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;width:140px;">Location</td>
                <td style="padding:10px 0;color:var(--text-dark);">${b.floor}</td>
            </tr>
            <tr style="border-bottom:1px solid var(--blue-50);">
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;">Check-in</td>
                <td style="padding:10px 0;color:var(--text-dark);font-weight:600;">${b.checkin}</td>
            </tr>
            <tr style="border-bottom:1px solid var(--blue-50);">
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;">Check-out</td>
                <td style="padding:10px 0;color:var(--text-dark);font-weight:600;">${b.checkout}</td>
            </tr>
            <tr style="border-bottom:1px solid var(--blue-50);">
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;">Duration</td>
                <td style="padding:10px 0;color:var(--text-dark);">${b.nights} nights</td>
            </tr>
            <tr style="border-bottom:1px solid var(--blue-50);">
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;">Status</td>
                <td style="padding:10px 0;"><span class="badge badge-blue" style="text-transform:capitalize;">${b.status}</span></td>
            </tr>
            <tr>
                <td style="padding:10px 0;color:var(--text-soft);font-weight:600;font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;">Total Amount</td>
                <td style="padding:10px 0;font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--blue-500);">₱${Number(b.total).toLocaleString()}</td>
            </tr>
        </table>`;
        openModal('detailsModal');
    }

    function downloadInvoice(bookingId) {
        showToast('Invoice for ' + bookingId + ' downloaded!');
    }
    function downloadInvoiceFromModal() {
        closeModal('detailsModal');
        showToast('Invoice for ' + currentInvoiceId + ' downloaded!');
    }

    function openReviewModal(roomName) {
        selectedRating = 0;
        document.getElementById('reviewText').value = '';
        document.getElementById('reviewRoomName').textContent = roomName;
        document.getElementById('reviewError').style.display = 'none';
        updateStars(0);
        openModal('reviewModal');
    }
    function setRating(val) { selectedRating = val; updateStars(val); }
    function hoverRating(val) { updateStars(val); }
    function resetHover() { updateStars(selectedRating); }
    function updateStars(val) {
        document.querySelectorAll('#starRating svg').forEach((s, i) => {
            s.style.fill = i < val ? 'var(--gold)' : 'var(--blue-100)';
            s.style.stroke = i < val ? 'var(--gold-dk)' : 'var(--blue-200)';
            s.style.transform = i < val ? 'scale(1.1)' : 'scale(1)';
        });
    }
    function submitReview() {
        const errEl = document.getElementById('reviewError');
        errEl.style.display = 'none';
        if (!selectedRating) { errEl.textContent = 'Please select a star rating.'; errEl.style.display = 'block'; return; }
        if (!document.getElementById('reviewText').value.trim()) { errEl.textContent = 'Please write a short review.'; errEl.style.display = 'block'; return; }
        const btn = document.getElementById('submitReviewBtn');
        btn.disabled = true; btn.textContent = 'Submitting…';
        setTimeout(() => {
            closeModal('reviewModal');
            btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> Submit Review';
            showToast('Review submitted! Thank you 🌟');
        }, 700);
    }

    function openRebookModal(roomName) {
        document.getElementById('rebookRoomName').textContent = roomName;
        document.getElementById('rebookError').style.display = 'none';
        const today = new Date(); today.setDate(today.getDate() + 1);
        const out = new Date(today); out.setDate(out.getDate() + 3);
        document.getElementById('rebook_checkin').value = today.toISOString().split('T')[0];
        document.getElementById('rebook_checkout').value = out.toISOString().split('T')[0];
        openModal('rebookModal');
    }
    function confirmRebook() {
        const ci = document.getElementById('rebook_checkin').value;
        const co = document.getElementById('rebook_checkout').value;
        const errEl = document.getElementById('rebookError');
        errEl.style.display = 'none';
        if (!ci || !co || new Date(co) <= new Date(ci)) { errEl.textContent = 'Please select valid check-in and check-out dates.'; errEl.style.display = 'block'; return; }
        const btn = document.getElementById('rebookConfirmBtn');
        btn.disabled = true; btn.textContent = 'Processing…';
        setTimeout(() => {
            closeModal('rebookModal');
            btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg> Confirm Booking';
            showToast('Booking confirmed! Check your email for details.');
        }, 800);
    }

    function filterBookings(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.booking-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
        });
    }
    ['detailsModal', 'reviewModal', 'rebookModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', e => { if (e.target.id === id) closeModal(id); });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { ['detailsModal', 'reviewModal', 'rebookModal'].forEach(closeModal); closeSidebar(); }
    });
</script>

<?php require '../../includes/_layout_end.php'; ?>