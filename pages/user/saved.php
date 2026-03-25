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
$last_name  = htmlspecialchars($_SESSION['last_name']  ?? '');
$full_name  = trim($first_name . ' ' . $last_name);
$email      = htmlspecialchars($_SESSION['email'] ?? '');
$initials   = strtoupper(mb_substr($first_name,0,1) . mb_substr($last_name,0,1));
$hour       = (int)date('G');
$greeting   = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$page_title     = 'Saved Rooms';
$page_hero_html = '<em>Saved</em> Rooms';
$page_hero_sub  = 'Your personal wishlist of favorite rooms and suites.';
$page_hero_icon = '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>';
$active_nav     = 'saved';
require '../../includes/_layout.php';

$saved_rooms = [
    ['name'=>'Ocean View Suite',   'view'=>'Sea View',     'bed'=>'1 King Bed',   'guests'=>2,'price'=>4200,'rating'=>'5.0','avail'=>true, 'img'=>'../../assets/images/OGA 4H.jpg','fallback'=>'background:linear-gradient(145deg,#b8d4f0,#2563c4,#0f2447)','badge'=>'Popular',  'saved_date'=>'Feb 20, 2026'],
    ['name'=>'Sampaguita Premiere','view'=>'City View',    'bed'=>'1 King Bed',   'guests'=>2,'price'=>4800,'rating'=>'4.8','avail'=>true, 'img'=>'../../assets/images/unit5.jpg','fallback'=>'background:linear-gradient(145deg,#93c5fd,#1e50a2,#0a1628)',  'badge'=>'Premium',   'saved_date'=>'Jan 15, 2026'],
    ['name'=>'Bahay Kubo Suite',   'view'=>'Courtyard',   'bed'=>'1 King Bed',   'guests'=>2,'price'=>3800,'rating'=>'4.9','avail'=>false,'img'=>'../../assets/images/unit2.jpg','fallback'=>'background:linear-gradient(145deg,#c8d8f0,#2563c4,#153060)',  'badge'=>'Heritage',  'saved_date'=>'Dec 5, 2025'],
    ['name'=>'Family Loft Room',   'view'=>'Mountain View','bed'=>'2 Queen Beds', 'guests'=>4,'price'=>5500,'rating'=>'4.7','avail'=>true, 'img'=>'../../assets/images/unit4.jpg','fallback'=>'background:linear-gradient(145deg,#dbeafe,#3b82f6,#1a3d7c)',  'badge'=>'Family',    'saved_date'=>'Nov 28, 2025'],
    ['name'=>'Deluxe Nipa Suite',  'view'=>'Garden View', 'bed'=>'1 King Bed',   'guests'=>2,'price'=>3500,'rating'=>'4.9','avail'=>true, 'img'=>'../../assets/images/unit1.jpg','fallback'=>'background:linear-gradient(145deg,#93c5fd,#2563c4,#153060)',  'badge'=>'Featured',  'saved_date'=>'Oct 10, 2025'],
];
?>

<link rel="stylesheet" href="../../assets/css/user-css/saved.css"/>

<div class="saved-header-bar reveal">
    <div class="saved-count">Showing <strong><?php echo count($saved_rooms); ?></strong> saved rooms</div>
    <select class="sort-select" onchange="showToast('Sorted!')">
        <option>Sort by: Date Saved</option>
        <option>Sort by: Price (Low → High)</option>
        <option>Sort by: Price (High → Low)</option>
        <option>Sort by: Rating</option>
    </select>
</div>

<div class="saved-grid">
<?php foreach($saved_rooms as $i => $r):
    $d = $i < 3 ? " rd{$i}" : '';
?>
<div class="saved-card reveal<?php echo $d; ?>">
    <div class="sc-img">
        <img src="<?php echo htmlspecialchars($r['img']); ?>"
             alt="<?php echo htmlspecialchars($r['name']); ?>"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <div class="sc-img-fallback" style="display:none;<?php echo $r['fallback']; ?>"></div>
        <span class="sc-badge"><?php echo $r['badge']; ?></span>
        <span class="sc-avail <?php echo $r['avail']?'yes':'no'; ?>"><?php echo $r['avail']?'Available':'Booked'; ?></span>
        <button class="sc-heart" onclick="unsaveRoom(this)" title="Remove from saved">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </button>
    </div>
    <div class="sc-body">
        <div class="sc-name"><?php echo $r['name']; ?></div>
        <div class="sc-meta">
            <span><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg><?php echo $r['view']; ?></span>
            <span><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg><?php echo $r['bed']; ?></span>
            <span><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><?php echo $r['guests']; ?> Guests</span>
        </div>
        <div class="sc-saved-on"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Saved on <?php echo $r['saved_date']; ?></div>
        <div class="sc-foot">
            <div>
                <div class="sc-price">₱<?php echo number_format($r['price']); ?> <sub>/ night</sub></div>
                <div class="sc-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><?php echo $r['rating']; ?></div>
            </div>
            <button class="btn-book-sc" <?php echo !$r['avail']?'disabled':''; ?>
                <?php if($r['avail']): ?>onclick="openBookModal('<?php echo addslashes($r['name']); ?>',<?php echo $r['price']; ?>)"<?php endif; ?>>
                <?php echo $r['avail']?'Book Now':'Unavailable'; ?>
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="modal-overlay" id="bookModal">
    <div class="modal-box" style="max-width:440px;">
        <button class="modal-close-btn" onclick="closeModal('bookModal')">✕</button>
        <div class="modal-title" id="bookModalTitle"></div>
        <div class="modal-sub" id="bookModalPrice"></div>
        <div class="form-grid" style="margin-bottom:14px;">
            <div class="form-field">
                <label>Check-in Date</label>
                <input type="date" id="book_checkin">
            </div>
            <div class="form-field">
                <label>Check-out Date</label>
                <input type="date" id="book_checkout">
            </div>
        </div>
        <div class="form-field" style="margin-bottom:8px;">
            <label>Guests</label>
            <select id="book_guests">
                <option value="1">1 Guest</option>
                <option value="2" selected>2 Guests</option>
                <option value="3">3 Guests</option>
                <option value="4">4 Guests</option>
            </select>
        </div>
        <div id="bookTotal" style="background:var(--blue-50);border:1px solid var(--blue-100);border-radius:12px;padding:12px 16px;margin:14px 0;font-size:0.84rem;color:var(--text-mid);"></div>
        <div id="bookError" style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-secondary" onclick="closeModal('bookModal')">Cancel</button>
            <button class="btn-primary" id="bookConfirmBtn" onclick="confirmBook()">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Confirm Booking
            </button>
        </div>
    </div>
</div>

<script src="../../assets/js/user-js/saved.js"></script>

<?php require '../../includes/_layout_end.php'; ?>