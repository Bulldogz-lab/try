<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Property Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/admin-css/styles.css" />
</head>

<body>

    <div class="overlay" id="overlay"></div>

    <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
        <span></span><span></span><span></span>
    </button>

    <nav class="sidebar" id="sidebar" aria-label="Main navigation">

        <div class="sidebar-top">
            <div class="sidebar-logo">
                <svg viewBox="0 0 24 24">
                    <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
                </svg>
            </div>
            <div>
                <div class="brand-name">PropManager</div>
                <div class="brand-sub">Property Suite</div>
            </div>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">✕</button>
        </div>

        <div class="sidebar-nav">

            <div class="nav-section-label">Overview</div>

            <div class="nav-item active" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5" />
                        <rect x="14" y="3" width="7" height="7" rx="1.5" />
                        <rect x="3" y="14" width="7" height="7" rx="1.5" />
                        <rect x="14" y="14" width="7" height="7" rx="1.5" />
                    </svg>
                </div>
                <span class="nav-label">Dashboard</span>
            </div>

            <div class="nav-item" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                </div>
                <span class="nav-label">Analytics</span>
                <span class="nav-badge">3</span>
            </div>

            <div class="nav-divider"></div>

            <div class="nav-section-label">Operations</div>

            <div class="nav-item has-sub" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
                    </svg>
                </div>
                <span class="nav-label">Properties</span>
                <span class="nav-arrow">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </span>
            </div>
            <div class="nav-sub">
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Properties List
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Add Property
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Units / Rooms
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Amenities
                </div>
            </div>

            <div class="nav-item has-sub" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <span class="nav-label">Bookings</span>
                <span class="nav-badge">12</span>
                <span class="nav-arrow">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </span>
            </div>
            <div class="nav-sub">
                <div class="sub-item active" tabindex="0">
                    <div class="sub-dot"></div>Reservations
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Calendar / Availability
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Check-in / Check-out
                </div>
            </div>

            <div class="nav-item has-sub" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <span class="nav-label">Users</span>
                <span class="nav-arrow">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </span>
            </div>
            <div class="nav-sub">
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Guests / Clients
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Staff / Admin Roles
                </div>
            </div>

            <div class="nav-item has-sub" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <span class="nav-label">Financial</span>
                <span class="nav-arrow">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </span>
            </div>
            <div class="nav-sub">
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Payments
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Transactions
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Invoices / Billing
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Expenses
                </div>
            </div>

            <div class="nav-item has-sub" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <span class="nav-label">Reports</span>
                <span class="nav-arrow">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </span>
            </div>
            <div class="nav-sub">
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Financial Reports
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Occupancy Reports
                </div>
                <div class="sub-item" tabindex="0">
                    <div class="sub-dot"></div>Booking Reports
                </div>
            </div>

            <div class="nav-divider"></div>

            <div class="nav-section-label">System</div>

            <div class="nav-item" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <span class="nav-label">Messages</span>
                <span class="nav-badge">5</span>
            </div>

            <div class="nav-item" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                </div>
                <span class="nav-label">Settings</span>
            </div>

            <div class="nav-item logout" tabindex="0">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </div>
                <span class="nav-label">Logout</span>
            </div>

        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="sidebar-avatar"></div>
                <div>
                    <div class="sidebar-user-name">Myra Jonson</div>
                    <div class="sidebar-user-role">Property Manager</div>
                </div>
            </div>
        </div>

    </nav>

    <div class="main">
        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="search-bar">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Search anything here
            </div>
        </div>
        <div class="topbar-divider"></div>

        <div class="content">
            <div class="welcome-inline">
                <div class="welcome-avatar-img"></div>
                <div class="welcome-text">
                    <p class="welcome-greeting">Welcome, <strong>Myra!</strong></p>
                    <p class="welcome-sub">Hi Myra, don't forget to check your property today</p>
                </div>
            </div>

            <div class="cards-area">

                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Earning Trends</span>
                        <div class="chart-controls">
                            <div class="toggle">
                                <div class="dot income"></div> Income
                            </div>
                            <div class="toggle">
                                <div class="dot expense"></div> Expenses
                            </div>
                            <select class="period-select">
                                <option>Monthly</option>
                                <option>Weekly</option>
                                <option>Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-area">
                        <div class="bars-wrap">
                            <div class="bars" id="barChart"></div>
                        </div>
                        <div class="chart-stats">
                            <div class="stat-item">
                                <div class="stat-icon coral">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <line x1="12" y1="1" x2="12" y2="23" />
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="value">$6,562.35</div>
                                    <div class="label">AVG. monthly income</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon dark">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path
                                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="value">$92,354.82</div>
                                    <div class="label">Total income</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon teal">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                                        <polyline points="17 6 23 6 23 12" />
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="value">$41,652.12</div>
                                    <div class="label">Total Expenses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="two-col">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Properties</span>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                style="width:15px;height:15px;color:var(--text-soft)">
                                <circle cx="12" cy="12" r="1" />
                                <circle cx="19" cy="12" r="1" />
                                <circle cx="5" cy="12" r="1" />
                            </svg>
                        </div>
                        <div class="prop-list">
                            <div class="prop-item">
                                <div class="prop-thumb" style="background:var(--blue-50);">🏢</div>
                                <div class="prop-info">
                                    <div class="name">Skyline Apartments</div>
                                    <div class="addr">12 Oak Street, NYC</div>
                                    <div class="prop-bar-wrap">
                                        <div class="prop-bar" style="width:75%;background:var(--danger);"></div>
                                    </div>
                                </div>
                                <div class="prop-score">8/10</div>
                            </div>
                            <div class="prop-item">
                                <div class="prop-thumb" style="background:#f0fdf4;">🏠</div>
                                <div class="prop-info">
                                    <div class="name">Green Valley Homes</div>
                                    <div class="addr">45 Palm Ave, LA</div>
                                    <div class="prop-bar-wrap">
                                        <div class="prop-bar" style="width:60%;background:var(--gold);"></div>
                                    </div>
                                </div>
                                <div class="prop-score">6/10</div>
                            </div>
                            <div class="prop-item">
                                <div class="prop-thumb" style="background:var(--blue-50);">🏬</div>
                                <div class="prop-info">
                                    <div class="name">Downtown Lofts</div>
                                    <div class="addr">88 Main Blvd, Chicago</div>
                                    <div class="prop-bar-wrap">
                                        <div class="prop-bar" style="width:90%;background:var(--blue-400);"></div>
                                    </div>
                                </div>
                                <div class="prop-score">9/10</div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="task-header">
                            <span class="card-title">Task Summary</span>
                            <div class="see-all">See all ›</div>
                        </div>
                        <div class="task-list">
                            <div class="task-item">
                                <div class="task-dot" style="background:var(--danger);"></div>
                                <div class="task-info">
                                    <div class="tname">Fix HVAC Unit</div>
                                    <div class="tprop">Skyline Apartments</div>
                                </div>
                                <div class="task-status" style="background:var(--danger-light);color:var(--danger);">
                                    Urgent</div>
                            </div>
                            <div class="task-item">
                                <div class="task-dot" style="background:var(--blue-400);"></div>
                                <div class="task-info">
                                    <div class="tname">Monthly Landscaping</div>
                                    <div class="tprop">Green Valley Homes</div>
                                </div>
                                <div class="task-status" style="background:var(--blue-50);color:var(--blue-500);">
                                    Scheduled</div>
                            </div>
                            <div class="task-item">
                                <div class="task-dot" style="background:var(--gold);"></div>
                                <div class="task-info">
                                    <div class="tname">Quarterly Inspection</div>
                                    <div class="tprop">Downtown Lofts</div>
                                </div>
                                <div class="task-status"
                                    style="background:var(--pending-light);color:var(--accent-dk);">Pending</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <section class="right-panel">
        <div class="right-header">
            <div class="notif-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                <div class="notif-dot"></div>
            </div>
            <div class="user-info">
                <div>
                    <div class="user-name">Myra Jonson</div>
                    <div class="user-role">Property Manager</div>
                </div>
                <div class="user-avatar"></div>
            </div>
        </div>
        <div class="right-content">
            <div class="cal-header">
                <span class="cal-month">August</span>
                <div class="cal-nav">
                    <button class="cal-nav-btn">‹</button>
                    <button class="cal-nav-btn">›</button>
                </div>
            </div>
            <div class="cal-days">
                <div class="cal-day">20</div>
                <div class="cal-day">21</div>
                <div class="cal-day">22</div>
                <div class="cal-day active has-event">23</div>
                <div class="cal-day">24</div>
                <div class="cal-day">25</div>
                <div class="cal-day">26</div>
            </div>
            <div class="topbar-divider"></div>

            <div class="schedule-list">
                <div class="schedule-slot">
                    <div class="time-col">10:30 am</div>
                    <div class="event-card coral">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        Broken Clamp
                    </div>
                </div>
                <div class="schedule-slot">
                    <div class="time-col">12:00 pm</div>
                    <div style="flex:1;" class="empty-slot"></div>
                </div>
                <div class="schedule-slot">
                    <div class="time-col">2:00 pm</div>
                    <div class="event-card teal">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Monthly – Landscaping
                    </div>
                </div>
                <div class="schedule-slot">
                    <div class="time-col">4:30 pm</div>
                    <div class="event-card dark">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        Generate Report
                    </div>
                </div>
            </div>

            <div class="topbar-divider"></div>

            <div class="section-title">Recently Activity</div>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-avatar"></div>
                    <div class="activity-info">
                        <div class="activity-name">Zaldy Co</div>
                        <div class="activity-date">22 August 2021</div>
                    </div>
                    <div class="activity-amount">+₱ 8,537.09</div>
                </div>
                <div class="activity-item">
                    <div class="activity-avatar"></div>
                    <div class="activity-info">
                        <div class="activity-name">Bongbong Marcos</div>
                        <div class="activity-date">21 August 2021</div>
                    </div>
                    <div class="activity-amount">+₱ 3,200.00</div>
                </div>
                <div class="activity-item">
                    <div class="activity-avatar"></div>
                    <div class="activity-info">
                        <div class="activity-name">Sarah Duterte</div>
                        <div class="activity-date">20 August 2021</div>
                    </div>
                    <div class="activity-amount" style="color:var(--danger);">−₱ 1,500.00</div>
                </div>
            </div>
        </div>
    </section>

    <script src="../../assets/js/admin/script.js"></script>
</body>

</html>