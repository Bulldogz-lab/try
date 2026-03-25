<?php
include '../../includes/session.php';

if ($_SESSION['role'] !== 'user') {
    echo '<!DOCTYPE html>
<html>
<head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
<body>
<script>
    Swal.fire({
        icon: "error", title: "Unauthorized",
        text: "You do not have permission to access this page.",
        timer: 1500, showConfirmButton: false
    }).then(() => { history.back(); });
</script>
</body>
</html>';
    exit;
}

include '../../includes/fetch_units.php';
include '../../includes/fetch_bookings.php';

function statusBadgeClass($status)
{
    return match ($status) {
        'completed' => 'st-completed',
        'cancelled' => 'st-cancelled',
        'active', 'confirmed' => 'st-active',
        default => 'st-pending'
    };
}
function statusLabel($status)
{
    return match ($status) {
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'active' => 'Active',
        'confirmed' => 'Confirmed',
        default => ucfirst($status)
    };
}
function nightsBetween($in, $out)
{
    $d1 = new DateTime($in);
    $d2 = new DateTime($out);
    return $d1->diff($d2)->days;
}
function formatDate($d)
{
    return date('M j, Y', strtotime($d));
}
function unitTypeToCategory($type)
{
    $type = strtolower($type ?? '');
    $cats = [];
    if (str_contains($type, 'sea') || str_contains($type, 'ocean'))
        $cats[] = 'sea';
    if (str_contains($type, 'family') || str_contains($type, 'loft'))
        $cats[] = 'family';
    if (str_contains($type, 'premium') || str_contains($type, 'suite'))
        $cats[] = 'premium';
    $cats[] = 'available';
    return implode(' ', $cats);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filipino Homes — My Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="../../assets/css/user-css/styles.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap"
        rel="stylesheet">

</head>

<body>
    <header id="hdr">
        <a href="#" class="logo">
            <img src="../../assets/images/logo.png" alt="Filipino Homes Logo" class="logo-icon">
            <span>
                <span
                    style="display:block;line-height:1.1;font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:700;">Filipino
                    Homes</span>
                <small>Investment Properties &amp; Services</small>
            </span>
        </a>
        <nav>
            <a href="#">Home</a>
            <a href="#browse">Browse Rooms</a>
            <a href="#history">My Bookings</a>
            <a href="#">Support</a>
        </nav>
        <div class="header-right">
            <button class="btn-book-header"
                onclick="document.querySelector('#browse').scrollIntoView({behavior:'smooth'})">Browse Rooms</button>
            <div class="btn-profile-wrap">
                <button class="btn-profile" id="profileBtn" aria-label="My Profile">
                    <span
                        class="profile-initials"><?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?></span>
                </button>
                <span class="profile-dot"></span>
            </div>
            <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav">
        <a href="#" onclick="closeMob()">Home</a>
        <a href="#browse" onclick="closeMob()">Browse Rooms</a>
        <a href="#history" onclick="closeMob()">My Bookings</a>
        <a href="#" onclick="closeMob()">Support</a>
        <a href="#" onclick="openSidebar()" style="color:var(--gold);font-weight:600;">My Profile</a>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <aside class="profile-sidebar" id="profileSidebar">
        <div class="sidebar-header">
            <button class="sidebar-close" id="sidebarClose">✕</button>
            <div class="sidebar-avatar"></div>
            <div class="sidebar-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
            <div class="sidebar-email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
            <div id="sidebarBadge" class="sidebar-badge">
                <span class="badge-dot" id="badgeDot"></span>
                <span id="badgeText"><?php echo htmlspecialchars($_SESSION['verification_status']); ?></span>
                <a href="profile.php" id="verifyBtn" class="btn-primary" style="text-decoration:none;">Verify Now</a>
            </div>
        </div>
        <div class="sidebar-body">
            <div class="sidebar-section-label">Account</div>
            <div class="sidebar-item">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg></div>
                <div class="sidebar-item-text" onclick="window.location.href='profile.php';">
                    <div class="sidebar-item-title">View Profile</div>
                    <div class="sidebar-item-sub">Personal details &amp; preferences</div>
                </div>
                <div class="sidebar-item-arrow"><svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg></div>
            </div>
            <div class="sidebar-item" onclick="window.location.href='bookings.php';">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg></div>
                <div class="sidebar-item-text">
                    <div class="sidebar-item-title">My Bookings</div>
                    <div class="sidebar-item-sub">View and manage reservations</div>
                </div>
                <span class="sidebar-item-badge"><?php echo $bookingCount; ?></span>
            </div>
            <div class="sidebar-item" onclick="window.location.href='saved.php';">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg></div>
                <div class="sidebar-item-text">
                    <div class="sidebar-item-title">Saved Rooms</div>
                    <div class="sidebar-item-sub">Rooms on your wishlist</div>
                </div>
            </div>
            <div class="sidebar-item" onclick="window.location.href='loyalty.php';">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="6" />
                        <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />
                    </svg></div>
                <div class="sidebar-item-text">
                    <div class="sidebar-item-title">Loyalty Points</div>
                    <div class="sidebar-item-sub">Gold tier</div>
                </div>
                <div class="sidebar-item-arrow"><svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg></div>
            </div>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section-label">Preferences</div>
            <div class="sidebar-item" onclick="window.location.href='settings.php';">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14" />
                    </svg></div>
                <div class="sidebar-item-text">
                    <div class="sidebar-item-title">Settings</div>
                    <div class="sidebar-item-sub">Notifications, privacy, security</div>
                </div>
                <div class="sidebar-item-arrow"><svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg></div>
            </div>
            <div class="sidebar-item">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <path d="M18 8h1a4 4 0 010 8h-1" />
                        <path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                        <line x1="6" y1="1" x2="6" y2="4" />
                        <line x1="10" y1="1" x2="10" y2="4" />
                        <line x1="14" y1="1" x2="14" y2="4" />
                    </svg></div>
                <div class="sidebar-item-text" onclick="window.location.href='payment.php';">
                    <div class="sidebar-item-title">Payment Methods</div>
                    <div class="sidebar-item-sub">Cards, e-wallets &amp; billing</div>
                </div>
                <div class="sidebar-item-arrow"><svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg></div>
            </div>
            <div class="sidebar-item">
                <div class="sidebar-item-icon"><svg viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                    </svg></div>
                <div class="sidebar-item-text" onclick="window.location.href='support.php';">
                    <div class="sidebar-item-title">Support &amp; Help</div>
                    <div class="sidebar-item-sub">FAQs and contact staff</div>
                </div>
                <div class="sidebar-item-arrow"><svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg></div>
            </div>
        </div>
        <div class="sidebar-footer">
            <form action="../../process/logout.php" method="POST">
                <button type="submit" class="btn-logout">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <section class="user-hero">
        <div class="user-hero-inner">
            <div>
                <div class="user-hero-greeting"><span></span>Welcome back</div>
                <h1>Welcome back, <em><?php echo htmlspecialchars($_SESSION['first_name']); ?></em>!</h1>
                <p class="user-hero-sub">Ready to plan your next Boracay getaway? Browse our rooms below.</p>
            </div>
            <div class="user-stats-strip">
                <div class="ustat reveal">
                    <div class="ustat-num"><?php echo $bookingCount; ?></div>
                    <div class="ustat-lbl">Bookings</div>
                </div>
                <div class="ustat reveal rd1">
                    <div class="ustat-num">1,240</div>
                    <div class="ustat-lbl">Points</div>
                </div>
                <div class="ustat reveal rd2">
                    <div class="ustat-num">Gold</div>
                    <div class="ustat-lbl">Tier</div>
                </div>
            </div>
        </div>
    </section>

    <section class="quick-actions">
        <div class="qa-grid">
            <div class="qa-card reveal" onclick="document.querySelector('#browse').scrollIntoView({behavior:'smooth'})">
                <div class="qa-icon gold"><svg viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg></div>
                <div>
                    <div class="qa-title">Browse Rooms</div>
                    <div class="qa-sub"><?php echo count($units); ?> available now</div>
                </div>
            </div>
            <div class="qa-card reveal rd1"
                onclick="document.querySelector('#history').scrollIntoView({behavior:'smooth'})">
                <div class="qa-icon blue"><svg viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg></div>
                <div>
                    <div class="qa-title">My Bookings</div>
                    <div class="qa-sub"><?php echo $activeBooking ? '1 active stay' : 'No active stay'; ?></div>
                </div>
            </div>
            <div class="qa-card reveal rd2">
                <div class="qa-icon teal"><svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="6" />
                        <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />
                    </svg></div>
                <div>
                    <div class="qa-title">My Rewards</div>
                    <div class="qa-sub">1,240 points</div>
                </div>
            </div>
            <div class="qa-card reveal rd3">
                <div class="qa-icon rose"><svg viewBox="0 0 24 24">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg></div>
                <div>
                    <div class="qa-title">Saved Rooms</div>
                    <div class="qa-sub">Wishlist items</div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($activeBooking): ?>
        <div style="padding: 0 5vw 48px; background: var(--white);">
            <div class="booking-banner-inner reveal">
                <div class="bbl">
                    <div class="bb-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div>
                        <div class="bb-label">Active Reservation</div>
                        <div class="bb-room">
                            <?php echo htmlspecialchars($activeBooking['unit_name'] ?? $activeBooking['unit_number']); ?>
                            — <?php echo htmlspecialchars($activeBooking['property_name']); ?>
                        </div>
                        <div class="bb-dates">
                            Check-in: <?php echo formatDate($activeBooking['checkin_date']); ?>
                            &nbsp;·&nbsp;
                            Check-out: <?php echo formatDate($activeBooking['checkout_date']); ?>
                        </div>
                    </div>
                </div>
                <div class="bb-right">
                    <div class="bb-status"><?php echo statusLabel($activeBooking['status']); ?></div>
                    <?php
                    $activeImgSrc = !empty($activeBooking['image_path'])
                        ? '../../' . ltrim($activeBooking['image_path'], '/')
                        : '';
                    $manageData = json_encode([
                        'booking_id' => $activeBooking['booking_id'],
                        'unit_name' => $activeBooking['unit_name'] ?? $activeBooking['unit_number'] ?? 'Unit',
                        'property_name' => $activeBooking['property_name'] ?? '',
                        'checkin' => formatDate($activeBooking['checkin_date']),
                        'checkout' => formatDate($activeBooking['checkout_date']),
                        'nights' => nightsBetween($activeBooking['checkin_date'], $activeBooking['checkout_date']),
                        'status' => statusLabel($activeBooking['status']),
                        'total_amount' => 'PHP ' . number_format((float) ($activeBooking['total_amount'] ?? 0), 0),
                        'guests' => (int) ($activeBooking['guests'] ?? 2),
                        'image' => $activeImgSrc,
                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    ?>
                    <button class="btn-manage"
                        onclick="openManageModal(<?php echo htmlspecialchars($manageData, ENT_QUOTES); ?>)">Manage
                        Stay</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <section class="rooms-section" id="browse">
        <div class="section-header-row">
            <div>
                <div class="eyebrow">Available Rooms</div>
                <h2 class="section-heading">Find Your <em>Perfect</em> Stay</h2>
            </div>
        </div>

        <div class="filter-bar reveal">
            <button class="filter-pill active" onclick="filterRooms('all',this)">All Rooms</button>
            <button class="filter-pill" onclick="filterRooms('available',this)">Available Now</button>
            <button class="filter-pill" onclick="filterRooms('sea',this)">Sea View</button>
            <button class="filter-pill" onclick="filterRooms('family',this)">Family</button>
            <button class="filter-pill gold-pill" onclick="filterRooms('premium',this)">✦ Premium</button>
            <div class="filter-spacer"></div>
            <div class="search-bar">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" placeholder="Search rooms…" id="roomSearch" oninput="searchRooms(this.value)">
            </div>
        </div>

        <div class="carousel-container" id="roomsCarousel">
            <button class="carousel-btn carousel-btn-prev" id="roomsPrev" onclick="scrollCarousel('rooms', -1)"
                aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </button>
            <div class="rooms-grid" id="roomsGrid">
                <?php if (empty($units)): ?>
                    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--gray-400);">
                        <svg viewBox="0 0 24 24"
                            style="width:48px;height:48px;margin-bottom:12px;stroke:currentColor;fill:none;stroke-width:1.5;">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <p>No units available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($units as $i => $unit):
                        $isVacant = $unit['status'] === 'vacant';
                        $cats = unitTypeToCategory($unit['unit_type']);
                        $rawUnitNum = trim(preg_replace('/^unit\s*/i', '', $unit['unit_number'] ?? ''));

                        if (!empty($unit['unit_name'])) {
                            $rawName = $unit['unit_name'];
                        } elseif (!empty($unit['property_name']) && !empty($rawUnitNum)) {
                            $rawName = $unit['property_name'] . ' — Unit ' . $rawUnitNum;
                        } elseif (!empty($unit['unit_number'])) {
                            $rawName = $unit['unit_number'];
                        } elseif (!empty($unit['property_name'])) {
                            $rawName = $unit['property_name'];
                        } else {
                            $rawName = 'Unit #' . $unit['unit_id'];
                        }

                        $unitName = htmlspecialchars($rawName);
                        $propName = htmlspecialchars($unit['property_name'] ?? '');
                        $cityPart = !empty($unit['city']) ? ', ' . $unit['city'] : '';
                        $price = '₱' . number_format((float) $unit['rent_amount'], 0);
                        $amenities = $amenitiesMap[$unit['unit_id']] ?? [];
                        $delayClass = ['', 'rd1', 'rd2', 'rd3'][$i % 4];
                        $imgSrc = $unit['image_path']
                            ? '../../' . ltrim($unit['image_path'], '/')
                            : '../../assets/images/placeholder.jpg';

                        $roomJs = json_encode([
                            'id' => $unit['unit_id'],
                            'name' => $rawName,
                            'location' => ($unit['property_name'] ?? '') . $cityPart,
                            'price' => $price,
                            'rating' => '4.8',
                            'guests' => 2,
                            'view' => $unit['unit_type'] ?? 'Standard',
                            'desc' => $unit['description'] ?? 'A comfortable and well-appointed unit.',
                            'amenities' => array_values($amenities),
                            'image' => $imgSrc,
                            'grad' => 'g' . (($i % 6) + 1),
                        ]);
                        ?>
                        <div class="room-card reveal <?php echo $delayClass; ?>"
                            data-cat="<?php echo htmlspecialchars($cats); ?>"
                            data-name="<?php echo strtolower($unitName . ' ' . $propName); ?>">

                            <div class="room-card-img">
                                <img src="<?php echo $imgSrc; ?>" alt="<?php echo $unitName; ?>" class="room-img-placeholder"
                                    onerror="this.src='../../assets/images/placeholder.jpg'">
                                <span class="room-badge-img <?php echo $isVacant ? 'badge-gold' : 'badge-blue'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($unit['unit_type'] ?? 'Standard')); ?>
                                </span>
                                <span class="room-avail <?php echo $isVacant ? 'avail-yes' : 'avail-no'; ?>">
                                    <?php echo $isVacant ? 'Available' : ($unit['status'] === 'maintenance' ? 'Maintenance' : 'Booked'); ?>
                                </span>
                            </div>

                            <div class="room-card-body">
                                <div class="room-card-top">
                                    <div class="room-name"><?php echo $unitName; ?></div>
                                    <div class="room-rating">
                                        <svg viewBox="0 0 24 24">
                                            <polygon
                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                        4.8
                                    </div>
                                </div>
                                <div class="room-meta">
                                    <span>
                                        <svg viewBox="0 0 24 24">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        <?php echo htmlspecialchars($unit['unit_type'] ?? 'Standard'); ?>
                                    </span>
                                    <?php if ($unit['floor']): ?>
                                        <span>
                                            <svg viewBox="0 0 24 24">
                                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                                            </svg>
                                            Floor <?php echo (int) $unit['floor']; ?>
                                        </span>
                                    <?php endif; ?>
                                    <span>
                                        <svg viewBox="0 0 24 24">
                                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                        2 Guests
                                    </span>
                                </div>
                                <?php if (!empty($amenities)): ?>
                                    <div class="room-features">
                                        <?php foreach (array_slice($amenities, 0, 4) as $am):
                                            $chipLabel = is_array($am) ? ($am['name'] ?? '') : $am;
                                            ?>
                                            <span class="feature-chip"><?php echo htmlspecialchars($chipLabel); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="room-divider"></div>
                                <div class="room-price-row">
                                    <div class="room-price"><?php echo $price; ?> <sub>/ night</sub></div>
                                    <?php if ($isVacant): ?>
                                        <button class="btn-rent" onclick='openRoomModal(<?php echo $roomJs; ?>)'>Book Now</button>
                                    <?php else: ?>
                                        <button class="btn-rent" disabled>Unavailable</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="carousel-btn carousel-btn-next" id="roomsNext" onclick="scrollCarousel('rooms', 1)"
                aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </button>
        </div>
        <div class="carousel-dots" id="roomsDots"></div>
    </section>

    <section class="history-section" id="history">
        <div class="history-inner">
            <div class="section-header-row" style="margin-bottom:0;">
                <div>
                    <div class="eyebrow">Past Stays</div>
                    <h2 class="section-heading">Booking <em>History</em></h2>
                </div>
                <button class="btn-book-header" style="font-size:0.78rem;padding:9px 20px;">Download All</button>
            </div>

            <div class="carousel-container" id="historyCarousel">
                <button class="carousel-btn carousel-btn-prev" id="historyPrev" onclick="scrollCarousel('history', -1)"
                    aria-label="Previous">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                </button>
                <div class="history-list" id="historyList">
                    <?php if (empty($bookingHistory)): ?>
                        <div style="text-align:center;padding:48px 20px;color:var(--gray-400);">
                            <svg viewBox="0 0 24 24"
                                style="width:40px;height:40px;margin-bottom:12px;stroke:currentColor;fill:none;stroke-width:1.5;">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <p>No booking history yet. Book your first stay above!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($bookingHistory as $bi => $bk):
                            $nights = nightsBetween($bk['checkin_date'], $bk['checkout_date']);
                            $bkImgSrc = $bk['image_path']
                                ? '../../' . ltrim($bk['image_path'], '/')
                                : '../../assets/images/placeholder.jpg';
                            $rawBkNum = trim(preg_replace('/^unit\s*/i', '', $bk['unit_number'] ?? ''));
                            if (!empty($bk['unit_name'])) {
                                $bkUnitName = $bk['unit_name'];
                            } elseif (!empty($bk['property_name']) && !empty($rawBkNum)) {
                                $bkUnitName = $bk['property_name'] . ' — Unit ' . $rawBkNum;
                            } elseif (!empty($bk['unit_number'])) {
                                $bkUnitName = $bk['unit_number'];
                            } elseif (!empty($bk['property_name'])) {
                                $bkUnitName = $bk['property_name'];
                            } else {
                                $bkUnitName = 'Booking #' . $bk['booking_id'];
                            }
                            $delayClass = ['', 'rd1', 'rd2', 'rd3'][$bi % 4];
                            ?>
                            <div class="history-item reveal <?php echo $delayClass; ?>">
                                <div class="history-img">
                                    <img src="<?php echo $bkImgSrc; ?>" alt="<?php echo htmlspecialchars($bkUnitName); ?>"
                                        class="history-img-bg" onerror="this.src='../../assets/images/placeholder.jpg'">
                                </div>
                                <div class="history-info">
                                    <div class="history-room">
                                        <?php echo htmlspecialchars($bkUnitName); ?>
                                    </div>
                                    <div class="history-dates">
                                        <svg viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        <?php echo formatDate($bk['checkin_date']); ?> –
                                        <?php echo formatDate($bk['checkout_date']); ?>
                                        &nbsp;·&nbsp; <?php echo $nights; ?> night<?php echo $nights !== 1 ? 's' : ''; ?>
                                    </div>
                                </div>
                                <div class="history-price-col">
                                    <div class="history-price">₱<?php echo number_format((float) $bk['total_amount'], 0); ?>
                                    </div>
                                    <div class="history-total">Total paid</div>
                                </div>
                                <span class="history-status <?php echo statusBadgeClass($bk['status']); ?>">
                                    <?php echo statusLabel($bk['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="carousel-btn carousel-btn-next" id="historyNext" onclick="scrollCarousel('history', 1)"
                    aria-label="Next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </div>
            <div class="carousel-dots" id="historyDots"></div>
        </div>
    </section>

    <div class="modal-overlay" id="roomModal">
        <div class="modal-box">
            <button class="modal-close" id="roomModalClose">✕</button>
            <div class="modal-room-img">
                <div class="modal-room-img-bg" id="modalImgBg"></div>
            </div>
            <div class="modal-body">
                <div class="modal-room-header">
                    <div>
                        <div class="modal-room-name" id="modalRoomName"></div>
                        <div class="modal-room-location">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span id="modalRoomLoc"></span>
                        </div>
                    </div>
                    <div class="modal-price-block">
                        <div class="modal-price" id="modalRoomPrice"></div>
                        <div class="modal-rating" id="modalRoomRating"></div>
                    </div>
                </div>
                <div class="modal-section-label">About this room</div>
                <p class="modal-desc" id="modalRoomDesc"></p>
                <div class="modal-section-label">Amenities</div>
                <div class="modal-amenities" id="modalAmenities"></div>
                <div class="modal-booking-strip">
                    <div class="mbstrip-field">
                        <div class="mbstrip-label">Check-in</div>
                        <input type="date" class="mbstrip-input" id="modalCheckin">
                    </div>
                    <div class="mbstrip-field">
                        <div class="mbstrip-label">Check-out</div>
                        <input type="date" class="mbstrip-input" id="modalCheckout">
                    </div>
                    <div class="mbstrip-field" style="max-width:100px;">
                        <div class="mbstrip-label">Guests</div>
                        <input type="number" class="mbstrip-input" min="1" max="6" value="2" id="modalGuests">
                    </div>
                    <button class="btn-book-modal" onclick="confirmBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" style="
    position:fixed;bottom:32px;left:50%;transform:translateX(-50%) translateY(80px);
    background:var(--blue-800);color:var(--white);
    padding:14px 28px;border-radius:40px;
    font-size:0.88rem;font-weight:500;
    box-shadow:0 8px 32px rgba(10,22,40,.35);
    z-index:600;transition:transform 0.4s cubic-bezier(.4,0,.2,1),opacity 0.4s;
    opacity:0;white-space:nowrap;display:flex;align-items:center;gap:10px;">
        <svg viewBox="0 0 24 24"
            style="width:16px;height:16px;stroke:var(--gold);fill:none;stroke-width:2.5;flex-shrink:0;">
            <polyline points="20 6 9 17 4 12" />
        </svg>
        <span id="toastMsg"></span>
    </div>

    <div class="manage-modal-overlay" id="manageModal">
        <div class="manage-modal-box">
            <div class="mm-hero">
                <div class="mm-hero-img" id="manageHeroImg"></div>
                <div class="mm-hero-content">
                    <div class="mm-hero-top">
                        <div class="mm-booking-ref">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Booking <span id="manageBookingRef">#—</span>
                        </div>
                        <button class="mm-close" onclick="closeManageModal()">✕</button>
                    </div>
                    <div class="mm-unit" id="manageUnitName">—</div>
                    <div class="mm-prop">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span id="manageProperty">—</span>
                    </div>
                    <div class="mm-meta-row">
                        <div class="mm-status" id="manageStatusPill">
                            <span class="mm-status-dot"></span>
                            <span id="manageStatusText">—</span>
                        </div>
                        <div class="mm-countdown">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span id="manageCountdownText">—</span>
                        </div>
                    </div>
                    <div class="mm-hero-gold-bar"></div>
                </div>
            </div>
            <div class="mm-body">
                <div class="mm-timeline">
                    <div class="mm-tl-side">
                        <div class="mm-tl-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Check-in
                        </div>
                        <div class="mm-tl-date" id="manageCheckin">—</div>
                        <div class="mm-tl-day" id="manageCheckinDay"></div>
                    </div>
                    <div class="mm-tl-mid">
                        <div class="mm-nights-num" id="manageNightsNum">—</div>
                        <div class="mm-nights-lbl">nights</div>
                    </div>
                    <div class="mm-tl-side">
                        <div class="mm-tl-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Check-out
                        </div>
                        <div class="mm-tl-date" id="manageCheckout">—</div>
                        <div class="mm-tl-day" id="manageCheckoutDay"></div>
                    </div>
                </div>
                <div class="mm-stats">
                    <div class="mm-stat">
                        <div class="mm-stat-icon ic-gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                        <div class="mm-stat-lbl">Guests</div>
                        <div class="mm-stat-val" id="manageGuests">—</div>
                    </div>
                    <div class="mm-stat">
                        <div class="mm-stat-icon ic-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                            </svg>
                        </div>
                        <div class="mm-stat-lbl">Total</div>
                        <div class="mm-stat-val" id="manageTotal">—</div>
                    </div>
                    <div class="mm-stat">
                        <div class="mm-stat-icon ic-purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <div class="mm-stat-lbl">Per Night</div>
                        <div class="mm-stat-val" id="managePerNight">—</div>
                    </div>
                </div>
                <div class="mm-progress-wrap" id="manageProgressWrap">
                    <div class="mm-progress-label">
                        <span>Stay progress</span>
                        <strong id="manageProgressText">0%</strong>
                    </div>
                    <div class="mm-progress-track">
                        <div class="mm-progress-fill" id="manageProgressFill" style="width:0%"></div>
                    </div>
                </div>
                <div class="mm-actions">
                    <button class="mm-btn mm-btn-primary" onclick="closeManageModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                        </svg>
                        Contact Support
                    </button>
                    <button class="mm-btn mm-btn-secondary"
                        onclick="closeManageModal();document.querySelector('#history').scrollIntoView({behavior:'smooth'})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        View History
                    </button>
                    <button class="mm-btn mm-btn-danger" id="manageCancelBtn" onclick="cancelBooking()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        Cancel This Reservation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/user-js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>