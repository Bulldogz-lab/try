<?php
/**
 * Filipino Homes — Shared Account Layout
 * Include at the top of every account page AFTER session_start() and auth check.
 *
 * Expected variables already set by the caller:
 *   $first_name, $last_name, $full_name, $email, $initials, $greeting
 *   $page_title  — shown in <title> and page hero
 *   $active_nav  — string matching one of: profile|bookings|saved|loyalty|settings|payment|support
 */
$nav_items = [
    'profile' => ['label' => 'View Profile', 'sub' => 'Personal details & preferences', 'href' => 'profile.php', 'badge' => null, 'icon' => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    'bookings' => ['label' => 'My Bookings', 'sub' => 'View and manage reservations', 'href' => 'bookings.php', 'badge' => '3', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
    'saved' => ['label' => 'Saved Rooms', 'sub' => 'Rooms on your wishlist', 'href' => 'saved.php', 'badge' => '5', 'icon' => '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>'],
    'loyalty' => ['label' => 'Loyalty Points', 'sub' => '1,240 pts · Gold tier', 'href' => 'loyalty.php', 'badge' => null, 'icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>'],
    'settings' => ['label' => 'Settings', 'sub' => 'Notifications, privacy, security', 'href' => 'settings.php', 'badge' => null, 'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>'],
    'payment' => ['label' => 'Payment Methods', 'sub' => 'Cards, e-wallets & billing', 'href' => 'payment.php', 'badge' => null, 'icon' => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
    'support' => ['label' => 'Support & Help', 'sub' => 'FAQs and contact staff', 'href' => 'support.php', 'badge' => null, 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filipino Homes — <?php echo htmlspecialchars($page_title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="../../assets/css/user-css/layout.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>
    <header id="hdr">
        <a href="user-dashboard.php" class="logo">
            <img src="../../assets/images/logo.png" alt="Filipino Homes Logo" class="logo-icon">
            <span>
                <span
                    style="display:block;font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;line-height:1.1;">Filipino
                    Homes</span>
                <small class="logo-sub">Investment Properties &amp; Services</small>
            </span>
        </a>
        <nav>
            <a href="user-dashboard.php">My Account</a>
            <a href="bookings.php">Bookings</a>
            <a href="support.php">Support</a>
        </nav>
        <div class="header-right">
            <a href="user.php" class="btn-browse">Browse Rooms</a>
            <div class="btn-profile-wrap">
                <button class="btn-profile" id="profileBtn" aria-label="My Profile">
                    <span class="profile-initials"><?php echo $initials; ?></span>
                </button>
                <span class="profile-dot"></span>
            </div>
            <button class="hamburger" id="hamburger"><span></span><span></span><span></span></button>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav">
        <a href="user.php">My Account</a>
        <a href="bookings.php">Bookings</a>
        <a href="support.php">Support</a>
        <a href="#" onclick="openSidebar();closeMob();" style="color:var(--gold);font-weight:600;">My Profile</a>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <aside class="profile-sidebar" id="profileSidebar">
        <div class="sidebar-hdr">
            <button class="sidebar-close" id="sidebarClose">✕</button>
            <div class="sb-avatar"><?php echo $initials; ?></div>
            <div class="sb-name"><?php echo $full_name; ?></div>
            <div class="sb-email"><?php echo $email; ?></div>
            <div class="sb-badge"><span class="badge-dot"></span>Verified Guest</div>
        </div>
        <div class="sidebar-body">
            <div class="sb-section-label">Account</div>
            <?php foreach ($nav_items as $key => $item):
                $isActive = ($active_nav === $key); ?>
                <a href="<?php echo $item['href']; ?>" class="sb-item<?php echo $isActive ? ' active-item' : ''; ?>">
                    <div class="sb-icon"><svg viewBox="0 0 24 24"><?php echo $item['icon']; ?></svg></div>
                    <div class="sb-text">
                        <div class="sb-title"><?php echo $item['label']; ?></div>
                        <div class="sb-sub"><?php echo $item['sub']; ?></div>
                    </div>
                    <div class="sb-right">
                        <?php if ($item['badge']): ?>
                            <span class="sb-badge-pill"><?php echo $item['badge']; ?></span>
                        <?php else: ?>
                            <span class="sb-chevron"><svg viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php if ($key === 'loyalty'): ?>
                    <div class="sb-divider"></div>
                    <div class="sb-section-label">Preferences</div><?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="sidebar-foot">
            <a href="../../process/logout.php" class="btn-logout">
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Sign Out
            </a>
        </div>
    </aside>

    <div id="toast"><svg viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12" />
        </svg><span id="toastMsg"></span></div>

    <div class="page-shell">
        <?php
        echo '<div class="page-hero"><div class="page-hero-inner reveal">';
        echo '<div>';
        echo '<div class="breadcrumb"><a href="user.php">My Account</a><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg><span>' . htmlspecialchars($page_title) . '</span></div>';
        echo '<h1 class="page-hero-title">' . ($page_hero_html ?? htmlspecialchars($page_title)) . '</h1>';
        echo '<p class="page-hero-sub">' . ($page_hero_sub ?? '') . '</p>';
        echo '</div>';
        echo '<div class="page-hero-icon"><svg viewBox="0 0 24 24">' . ($page_hero_icon ?? '') . '</svg></div>';
        echo '</div></div>';
        ?>
        <div class="page-content">