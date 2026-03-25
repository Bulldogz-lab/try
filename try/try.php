<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Property Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue-900: #0a1628;
      --blue-800: #0f2447;
      --blue-700: #153060;
      --blue-600: #1a3d7c;
      --blue-500: #1e50a2;
      --blue-400: #2563c4;
      --blue-300: #3b82f6;
      --blue-200: #93c5fd;
      --blue-100: #dbeafe;
      --blue-50: #eff6ff;
      --gold: #deaf37;
      --gold-dk: #cfab57;
      --accent-dk: #c9a040;
      --white: #ffffff;
      --text-dark: #0a1628;
      --text-mid: #334155;
      --text-soft: #64748b;
      --radius: 12px;
      --radius-lg: 20px;
      --shadow-sm: 0 2px 8px rgba(10, 22, 40, .08);
      --shadow-md: 0 8px 32px rgba(10, 22, 40, .15);
      --shadow-lg: 0 20px 60px rgba(10, 22, 40, .22);
      --transition: 0.35s cubic-bezier(.4, 0, .2, 1);

      /* Semantic mappings */
      --primary:        var(--blue-700);
      --primary-light:  var(--blue-50);
      --primary-mid:    var(--blue-100);
      --secondary:      var(--blue-400);
      --secondary-light: var(--blue-50);
      --success:        #2ECC71;
      --success-light:  #e6f9f0;
      --warning:        var(--gold);
      --warning-light:  #fef9e4;
      --gray:           var(--text-soft);
      --gray-light:     #f1f5fb;
      --border:         var(--blue-100);
      --sidebar-w:      256px;
      --right-w:        310px;
      --danger:         #E74C3C;
      --danger-light:   #fdecea;
      --pending:        var(--gold-dk);
      --pending-light:  #fef6e0;
      --sidebar-bg:     var(--blue-900);
      --sidebar-hover:  rgba(59,130,246,0.14);
      --sidebar-active: var(--blue-400);
      --sidebar-text:   rgba(255,255,255,0.60);
      --sidebar-text-bright: rgba(255,255,255,0.95);
      --sidebar-label:  rgba(255,255,255,0.30);
      --sidebar-divider: rgba(255,255,255,0.07);
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--gray-light);
      display: flex;
      height: 100vh;
      overflow: hidden;
      color: var(--text-dark);
      font-size: 15px;
    }

    /* ════════════════════════════
       SIDEBAR
    ════════════════════════════ */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      overflow: hidden;
      z-index: 300;
    }

    .sidebar-top {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 22px 16px 18px;
      flex-shrink: 0;
      border-bottom: 1px solid var(--sidebar-divider);
    }

    .sidebar-logo {
      width: 38px; height: 38px;
      background: var(--blue-400);
      border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .sidebar-logo svg { width: 20px; height: 20px; fill: white; }

    .brand-name {
      font-size: 15px; font-weight: 800;
      color: var(--sidebar-text-bright);
      letter-spacing: -.3px; line-height: 1.1;
    }

    .brand-sub { font-size: 11px; color: var(--sidebar-label); font-weight: 500; }

    .sidebar-close {
      display: none;
      margin-left: auto;
      width: 30px; height: 30px;
      border-radius: 8px;
      background: rgba(255,255,255,0.08);
      border: none;
      color: var(--sidebar-text);
      align-items: center; justify-content: center;
      cursor: pointer;
      font-size: 18px;
      line-height: 1;
      flex-shrink: 0;
      transition: background var(--transition);
    }

    .sidebar-close:hover { background: rgba(255,255,255,0.14); color: white; }

    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 10px 0;
    }

    .sidebar-nav::-webkit-scrollbar { width: 3px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.10); border-radius: 3px; }

    .nav-section-label {
      font-size: 10px; font-weight: 700;
      letter-spacing: 1.1px;
      text-transform: uppercase;
      color: var(--sidebar-label);
      padding: 14px 18px 4px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px 8px 14px;
      margin: 1px 8px;
      border-radius: 10px;
      cursor: pointer;
      transition: background var(--transition), color var(--transition);
      color: var(--sidebar-text);
      user-select: none;
      white-space: nowrap;
    }

    .nav-item:hover { background: var(--sidebar-hover); color: var(--sidebar-text-bright); }
    .nav-item.active { background: var(--sidebar-active); color: #fff; }
    .nav-item.active .nav-icon { background: rgba(255,255,255,0.18); }
    .nav-item.active .nav-badge { background: rgba(255,255,255,0.25); color: #fff; }

    .nav-icon {
      width: 34px; height: 34px;
      border-radius: 9px;
      background: rgba(255,255,255,0.06);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      transition: background var(--transition);
    }

    .nav-item:hover .nav-icon { background: rgba(255,255,255,0.10); }
    .nav-icon svg { width: 16px; height: 16px; }

    .nav-label { flex: 1; font-size: 13.5px; font-weight: 500; letter-spacing: -.1px; }

    .nav-badge {
      font-size: 11px; font-weight: 700;
      padding: 2px 7px; border-radius: 20px;
      background: rgba(59,130,246,0.22);
      color: var(--blue-200);
      min-width: 20px; text-align: center;
      flex-shrink: 0;
    }

    .nav-arrow {
      color: var(--sidebar-label);
      flex-shrink: 0;
      transition: transform .25s;
    }

    .nav-item.expanded .nav-arrow { transform: rotate(90deg); }
    .nav-arrow svg { width: 13px; height: 13px; }

    .nav-sub {
      overflow: hidden;
      max-height: 0;
      transition: max-height .3s cubic-bezier(.4,0,.2,1);
    }

    .nav-sub.open { max-height: 600px; }

    .sub-item {
      display: flex; align-items: center; gap: 9px;
      padding: 7px 12px 7px 50px;
      margin: 1px 8px;
      border-radius: 9px;
      cursor: pointer;
      color: var(--sidebar-text);
      font-size: 13px; font-weight: 400;
      transition: background .15s, color .15s;
      white-space: nowrap;
    }

    .sub-item:hover { background: rgba(255,255,255,0.06); color: var(--sidebar-text-bright); }
    .sub-item.active { color: var(--blue-200); font-weight: 600; }
    .sub-item.active .sub-dot { background: var(--blue-300); }
    .sub-item:hover .sub-dot { background: rgba(255,255,255,0.5); }

    .sub-dot {
      width: 5px; height: 5px; border-radius: 50%;
      background: rgba(255,255,255,0.22);
      flex-shrink: 0; transition: background .15s;
    }

    .nav-divider {
      height: 1px;
      background: var(--sidebar-divider);
      margin: 8px 16px;
    }

    .sidebar-bottom {
      padding: 10px 8px 14px;
      border-top: 1px solid var(--sidebar-divider);
      flex-shrink: 0;
    }

    .sidebar-user {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 10px; border-radius: 11px;
      cursor: pointer; transition: background var(--transition);
    }

    .sidebar-user:hover { background: rgba(255,255,255,0.07); }

    .sidebar-avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, var(--blue-400), var(--blue-700));
      flex-shrink: 0; border: 2px solid rgba(255,255,255,0.18);
    }

    .sidebar-user-name { font-size: 13px; font-weight: 700; color: var(--sidebar-text-bright); }
    .sidebar-user-role { font-size: 11px; color: var(--sidebar-label); }

    /* ════════════════════════════
       MOBILE TOGGLE
    ════════════════════════════ */
    .menu-toggle {
      display: none;
      align-items: center; justify-content: center;
      flex-direction: column; gap: 4px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      width: 44px; height: 44px;
      position: fixed;
      left: 16px; top: 18px;
      z-index: 400;
      cursor: pointer;
      box-shadow: var(--shadow-sm);
    }

    .menu-toggle span {
      width: 18px; height: 2px;
      background: var(--text-dark);
      border-radius: 2px; display: block;
    }

    /* ════════════════════════════
       MAIN CONTENT
    ════════════════════════════ */
    .main {
      flex: 1; display: flex; flex-direction: column;
      overflow: hidden; padding: 0 26px; min-width: 0;
    }

    .topbar {
      display: flex; align-items: center;
      padding: 26px 0 18px; gap: 20px;
      justify-content: space-between; flex-shrink: 0;
    }

    .topbar h1 {
      font-size: 30px; font-weight: 800;
      color: var(--text-dark); flex-shrink: 0;
    }

    .search-bar {
      flex: 0 0 380px;
      display: flex; align-items: center; gap: 11px;
      background: var(--white); border: 1px solid var(--border);
      border-radius: 26px; padding: 10px 18px;
      color: var(--text-soft); font-size: 14px;
      font-family: 'DM Sans',sans-serif; cursor: text;
      transition: border-color var(--transition), box-shadow var(--transition);
      box-shadow: var(--shadow-sm);
    }

    .search-bar:hover { border-color: var(--blue-300); box-shadow: 0 0 0 3px var(--blue-50); }
    .search-bar svg { width: 16px; height: 16px; opacity: .45; flex-shrink: 0; }

    .topbar-divider { height: 1px; background: var(--border); flex-shrink: 0; }

    .content {
      flex: 1; display: flex; flex-direction: column;
      overflow: hidden; padding-bottom: 14px;
    }

    .welcome-inline {
      display: flex; align-items: center; gap: 14px;
      padding: 18px 0 16px; flex-shrink: 0;
    }

    .welcome-avatar-img {
      width: 62px; height: 62px; border-radius: 50%;
      flex-shrink: 0; border: 2px solid var(--blue-100);
      background: linear-gradient(135deg, var(--blue-100), var(--blue-300));
    }

    .welcome-text .welcome-greeting {
      font-size: 22px; font-weight: 500;
      color: var(--text-dark); margin-bottom: 2px;
    }

    .welcome-text .welcome-greeting strong { font-weight: 700; color: var(--blue-600); }
    .welcome-text .welcome-sub { font-size: 14px; color: var(--text-soft); }

    .cards-area {
      flex: 1; display: flex; flex-direction: column;
      gap: 20px; overflow-y: auto; padding-right: 4px;
    }

    .cards-area::-webkit-scrollbar { width: 4px; }
    .cards-area::-webkit-scrollbar-thumb { background: var(--blue-100); border-radius: 4px; }

    .card {
      background: var(--white); border-radius: var(--radius-lg);
      padding: 20px 22px; border: 1px solid var(--border);
      flex-shrink: 0; box-shadow: var(--shadow-sm);
      transition: box-shadow var(--transition);
    }

    .card:hover { box-shadow: var(--shadow-md); }

    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .card-title { font-size: 19px; font-weight: 800; color: var(--text-dark); }

    .chart-controls {
      display: flex; align-items: center; gap: 24px;
      font-size: 13px; font-weight: 500; color: var(--text-soft);
    }

    .toggle { display: flex; align-items: center; gap: 7px; }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot.income { background: var(--blue-400); }
    .dot.expense { background: var(--gold); }
    .period-select {
      background: none; border: none; font-size: 13px;
      color: var(--blue-400); font-family: 'DM Sans',sans-serif;
      cursor: pointer; font-weight: 600;
    }

    .chart-area { display: flex; gap: 22px; align-items: stretch; }
    .bars-wrap { flex: 1; display: flex; flex-direction: column; gap: 8px; min-width: 0; }
    .bars { display: flex; align-items: flex-end; gap: 7px; height: 148px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; position: relative; }

    .bar {
      width: 72%; border-radius: 7px 7px 0 0;
      background: var(--blue-50);
      transition: height .6s ease, background .2s; cursor: pointer;
    }

    .bar:hover { background: var(--blue-100); }
    .bar.active { background: var(--blue-400); }
    .bar-label { font-size: 11px; color: var(--text-soft); text-align: center; margin-top: 5px; }

    .bar-tooltip {
      position: absolute; top: -30px; left: 50%; transform: translateX(-50%);
      background: var(--blue-800); color: white;
      font-size: 11px; font-weight: 600;
      padding: 3px 8px; border-radius: 7px;
      white-space: nowrap; pointer-events: none;
    }

    .chart-stats { display: flex; flex-direction: column; justify-content: center; gap: 14px; min-width: 155px; flex-shrink: 0; }
    .stat-item { display: flex; align-items: center; gap: 11px; }

    .stat-icon {
      width: 36px; height: 36px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .stat-icon.coral { background: #fef6e0; color: var(--gold-dk); }
    .stat-icon.dark  { background: var(--blue-800); color: white; }
    .stat-icon.teal  { background: var(--success-light); color: var(--success); }
    .stat-icon svg { width: 15px; height: 15px; }
    .stat-info .value { font-size: 13px; font-weight: 700; color: var(--text-dark); }
    .stat-info .label { font-size: 11px; color: var(--text-soft); }

    .two-col { display: flex; gap: 20px; }
    .two-col .card { flex: 1; min-width: 0; }

    .prop-list { display: flex; flex-direction: column; gap: 13px; }
    .prop-item { display: flex; align-items: center; gap: 11px; }
    .prop-thumb { width: 44px; height: 44px; border-radius: 11px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .prop-info { flex: 1; min-width: 0; }
    .prop-info .name { font-size: 14px; font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .prop-info .addr { font-size: 12px; color: var(--text-soft); }
    .prop-bar-wrap { margin-top: 4px; height: 4px; background: var(--blue-50); border-radius: 3px; width: 90px; }
    .prop-bar { height: 100%; border-radius: 3px; }
    .prop-score { font-size: 14px; font-weight: 700; color: var(--blue-400); margin-left: auto; flex-shrink: 0; }

    .task-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .see-all { font-size: 13px; color: var(--blue-400); font-weight: 600; cursor: pointer; }
    .task-list { display: flex; flex-direction: column; gap: 9px; }
    .task-item { display: flex; align-items: center; gap: 11px; padding: 10px 13px; background: var(--gray-light); border-radius: var(--radius); }
    .task-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .task-info { flex: 1; min-width: 0; }
    .task-info .tname { font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .task-info .tprop { font-size: 11px; color: var(--text-soft); }
    .task-status { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; white-space: nowrap; flex-shrink: 0; }

    /* ════════════════════════════
       RIGHT PANEL
    ════════════════════════════ */
    .right-panel {
      width: var(--right-w); background: var(--white);
      border-left: 1px solid var(--border);
      display: flex; flex-direction: column;
      overflow: hidden; flex-shrink: 0;
    }

    .right-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 26px 20px 20px;
      border-bottom: 1px solid var(--border); flex-shrink: 0;
    }

    .user-info { display: flex; align-items: center; gap: 10px; }
    .user-name { font-size: 16px; font-weight: 700; color: var(--text-dark); text-align: right; }
    .user-role { font-size: 12px; color: var(--text-soft); text-align: right; }

    .user-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, var(--blue-100), var(--blue-300));
      flex-shrink: 0;
    }

    .notif-btn {
      width: 38px; height: 38px; background: var(--blue-50);
      border-radius: var(--radius); display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--blue-600); position: relative;
      transition: background var(--transition);
    }

    .notif-btn:hover { background: var(--blue-100); }
    .notif-btn svg { width: 17px; height: 17px; }

    .notif-dot {
      position: absolute; top: 8px; right: 8px;
      width: 7px; height: 7px; background: var(--gold);
      border-radius: 50%; border: 2px solid white;
    }

    .right-content { flex: 1; overflow-y: auto; padding: 16px 20px; }
    .right-content::-webkit-scrollbar { width: 4px; }
    .right-content::-webkit-scrollbar-thumb { background: var(--blue-100); border-radius: 4px; }

    .cal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .cal-month { font-size: 16px; font-weight: 700; color: var(--text-dark); }
    .cal-nav { display: flex; gap: 6px; }

    .cal-nav-btn {
      width: 28px; height: 28px; border: 1px solid var(--border); border-radius: 8px;
      background: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
      color: var(--text-soft); font-size: 14px; transition: all var(--transition);
    }

    .cal-nav-btn:hover { background: var(--blue-400); color: white; border-color: var(--blue-400); }

    .cal-days { display: flex; gap: 2px; justify-content: space-between; margin-bottom: 16px; }

    .cal-day {
      width: 34px; height: 34px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; cursor: pointer; transition: all var(--transition);
      color: var(--text-soft); font-weight: 500; border: 1px solid transparent; position: relative;
    }

    .cal-day:hover { border-color: var(--blue-300); color: var(--blue-400); }
    .cal-day.active { background: var(--blue-400); color: white; font-weight: 700; }

    .cal-day.has-event::after {
      content: ''; display: block; width: 4px; height: 4px;
      background: var(--gold); border-radius: 50%;
      position: absolute; bottom: 1px;
    }

    .schedule-list { display: flex; flex-direction: column; gap: 14px; margin: 20px 0 18px; }
    .schedule-slot { display: flex; align-items: stretch; gap: 10px; }
    .time-col { width: 54px; text-align: right; font-size: 11px; color: var(--text-soft); padding-top: 12px; flex-shrink: 0; }

    .event-card {
      flex: 1; border-radius: var(--radius); padding: 10px 14px;
      color: white; font-size: 13px; font-weight: 600;
      display: flex; align-items: center; gap: 9px;
      cursor: pointer; transition: transform var(--transition), box-shadow var(--transition);
    }

    .event-card:hover { transform: translateX(2px); box-shadow: var(--shadow-md); }
    .event-card.coral { background: linear-gradient(135deg, var(--gold), var(--accent-dk)); }
    .event-card.teal  { background: var(--success); }
    .event-card.dark  { background: linear-gradient(135deg, var(--blue-600), var(--blue-800)); }
    .event-card svg { width: 14px; height: 14px; opacity: .85; flex-shrink: 0; }
    .empty-slot { height: 16px; }

    .section-title { font-size: 15px; font-weight: 700; color: var(--text-dark); margin: 18px 0 14px; }
    .activity-list { display: flex; flex-direction: column; gap: 11px; }

    .activity-item {
      display: flex; align-items: center; gap: 11px;
      padding-bottom: 11px; border-bottom: 1px solid var(--border);
    }

    .activity-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, var(--blue-100), var(--blue-300)); flex-shrink: 0;
    }

    .activity-info { flex: 1; min-width: 0; }
    .activity-name { font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .activity-date { font-size: 11px; color: var(--text-soft); }
    .activity-amount { font-size: 13px; font-weight: 700; color: var(--success); flex-shrink: 0; }

    /* ════════════════════════════
       OVERLAY
    ════════════════════════════ */
    .overlay {
      position: fixed; inset: 0;
      background: rgba(10,22,40,0.52);
      z-index: 290;
      opacity: 0;
      pointer-events: none;
      transition: opacity .28s cubic-bezier(.4,0,.2,1);
    }

    .overlay.visible {
      opacity: 1;
      pointer-events: all;
    }

    /* Logout red hover */
    .nav-item.logout:hover { background: rgba(231,76,60,0.16); color: #f07b6f; }
    .nav-item.logout:hover .nav-icon { background: rgba(231,76,60,0.18); color: #f07b6f; }

    /* ════════════════════════════
       RESPONSIVE
    ════════════════════════════ */
    @media (max-width: 1200px) {
      :root { --right-w: 280px; }
      .main { padding: 0 18px; }
      .search-bar { flex: 0 0 260px; }
    }

    @media (max-width: 1024px) { .right-panel { display: none; } }

    @media (max-width: 860px) {
      .sidebar {
        position: fixed;
        top: 0; left: 0; height: 100%;
        transform: translateX(-100%);
        transition: transform .28s cubic-bezier(.4,0,.2,1);
        box-shadow: 6px 0 40px rgba(10,22,40,0.35);
        z-index: 310;
        /* Ensure it's fully off-screen before opening */
        visibility: hidden;
      }

      .sidebar.open {
        transform: translateX(0);
        visibility: visible;
      }

      .sidebar-close {
        display: flex;
        width: 34px; height: 34px;
        border-radius: 9px;
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.12);
        font-size: 16px;
        flex-shrink: 0;
      }

      .sidebar-close:hover {
        background: rgba(255,255,255,0.18);
        color: white;
      }

      .menu-toggle { display: flex; }
      .main { padding: 0 16px; padding-top: 68px; }
    }

    @media (max-width: 700px) {
      .two-col { flex-direction: column; }
      .search-bar { display: none; }
      .topbar { padding: 14px 0 10px; }
      .topbar h1 { font-size: 24px; }
    }

    @media (max-width: 480px) {
      .chart-area { flex-direction: column; }
      .chart-stats { flex-direction: row; flex-wrap: wrap; gap: 10px; min-width: 0; }
      .card { padding: 14px; border-radius: var(--radius); }
      .main { padding: 62px 10px 0; }
    }
  </style>
</head>
<body>

  <div class="overlay" id="overlay"></div>

  <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
    <span></span><span></span><span></span>
  </button>

  <!-- ════════ SIDEBAR ════════ -->
  <nav class="sidebar" id="sidebar" aria-label="Main navigation">

    <div class="sidebar-top">
      <div class="sidebar-logo">
        <svg viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V21H3V9.5z"/></svg>
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
            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
          </svg>
        </div>
        <span class="nav-label">Dashboard</span>
      </div>

      <div class="nav-item" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
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
            <path d="M3 9.5L12 3l9 6.5V21H3V9.5z"/>
          </svg>
        </div>
        <span class="nav-label">Properties</span>
        <span class="nav-arrow">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
      <div class="nav-sub">
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Properties List</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Add Property</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Units / Rooms</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Amenities</div>
      </div>

      <div class="nav-item has-sub" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span class="nav-label">Bookings</span>
        <span class="nav-badge">12</span>
        <span class="nav-arrow">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
      <div class="nav-sub">
        <div class="sub-item active" tabindex="0"><div class="sub-dot"></div>Reservations</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Calendar / Availability</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Check-in / Check-out</div>
      </div>

      <div class="nav-item has-sub" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <span class="nav-label">Users</span>
        <span class="nav-arrow">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
      <div class="nav-sub">
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Guests / Clients</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Staff / Admin Roles</div>
      </div>

      <div class="nav-item has-sub" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <span class="nav-label">Financial</span>
        <span class="nav-arrow">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
      <div class="nav-sub">
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Payments</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Transactions</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Invoices / Billing</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Expenses</div>
      </div>

      <div class="nav-item has-sub" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </div>
        <span class="nav-label">Reports</span>
        <span class="nav-arrow">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
      <div class="nav-sub">
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Financial Reports</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Occupancy Reports</div>
        <div class="sub-item" tabindex="0"><div class="sub-dot"></div>Booking Reports</div>
      </div>

      <div class="nav-divider"></div>

      <div class="nav-section-label">System</div>

      <div class="nav-item" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <span class="nav-label">Messages</span>
        <span class="nav-badge">5</span>
      </div>

      <div class="nav-item" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
        </div>
        <span class="nav-label">Settings</span>
      </div>

      <div class="nav-item logout" tabindex="0">
        <div class="nav-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
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

  <!-- ════════ MAIN ════════ -->
  <div class="main">
    <div class="topbar">
      <h1>Dashboard</h1>
      <div class="search-bar">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
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

        <!-- Earning Trends -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Earning Trends</span>
            <div class="chart-controls">
              <div class="toggle"><div class="dot income"></div> Income</div>
              <div class="toggle"><div class="dot expense"></div> Expenses</div>
              <select class="period-select">
                <option>Monthly</option><option>Weekly</option><option>Yearly</option>
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
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
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
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
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
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
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

        <!-- Properties + Task Summary -->
        <div class="two-col">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Properties</span>
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;color:var(--text-soft)">
                <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
              </svg>
            </div>
            <div class="prop-list">
              <div class="prop-item">
                <div class="prop-thumb" style="background:var(--blue-50);">🏢</div>
                <div class="prop-info">
                  <div class="name">Skyline Apartments</div>
                  <div class="addr">12 Oak Street, NYC</div>
                  <div class="prop-bar-wrap"><div class="prop-bar" style="width:75%;background:var(--danger);"></div></div>
                </div>
                <div class="prop-score">8/10</div>
              </div>
              <div class="prop-item">
                <div class="prop-thumb" style="background:#f0fdf4;">🏠</div>
                <div class="prop-info">
                  <div class="name">Green Valley Homes</div>
                  <div class="addr">45 Palm Ave, LA</div>
                  <div class="prop-bar-wrap"><div class="prop-bar" style="width:60%;background:var(--gold);"></div></div>
                </div>
                <div class="prop-score">6/10</div>
              </div>
              <div class="prop-item">
                <div class="prop-thumb" style="background:var(--blue-50);">🏬</div>
                <div class="prop-info">
                  <div class="name">Downtown Lofts</div>
                  <div class="addr">88 Main Blvd, Chicago</div>
                  <div class="prop-bar-wrap"><div class="prop-bar" style="width:90%;background:var(--blue-400);"></div></div>
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
                <div class="task-status" style="background:var(--danger-light);color:var(--danger);">Urgent</div>
              </div>
              <div class="task-item">
                <div class="task-dot" style="background:var(--blue-400);"></div>
                <div class="task-info">
                  <div class="tname">Monthly Landscaping</div>
                  <div class="tprop">Green Valley Homes</div>
                </div>
                <div class="task-status" style="background:var(--blue-50);color:var(--blue-500);">Scheduled</div>
              </div>
              <div class="task-item">
                <div class="task-dot" style="background:var(--gold);"></div>
                <div class="task-info">
                  <div class="tname">Quarterly Inspection</div>
                  <div class="tprop">Downtown Lofts</div>
                </div>
                <div class="task-status" style="background:var(--pending-light);color:var(--accent-dk);">Pending</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ════════ RIGHT PANEL ════════ -->
  <section class="right-panel">
    <div class="right-header">
      <div class="notif-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
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
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
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
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Monthly – Landscaping
          </div>
        </div>
        <div class="schedule-slot">
          <div class="time-col">4:30 pm</div>
          <div class="event-card dark">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
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

  <script>
    // ── BAR CHART ──
    const months = ['Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep'];
    const values = [60, 85, 45, 55, 40, 65, 90, 70];
    const activeIdx = 6;
    const maxH = 140;
    const barChart = document.getElementById('barChart');

    months.forEach((m, i) => {
      const h = Math.round((values[i] / 100) * maxH);
      const col = document.createElement('div');
      col.className = 'bar-col';

      const bar = document.createElement('div');
      bar.className = 'bar' + (i === activeIdx ? ' active' : '');
      bar.style.height = '0px';
      bar.style.width = '72%';

      if (i === activeIdx) {
        const tip = document.createElement('div');
        tip.className = 'bar-tooltip';
        tip.textContent = '$7,238.00';
        col.appendChild(tip);
      }

      bar.addEventListener('mouseenter', () => { if (i !== activeIdx) bar.classList.add('active'); });
      bar.addEventListener('mouseleave', () => { if (i !== activeIdx) bar.classList.remove('active'); });

      const lbl = document.createElement('div');
      lbl.className = 'bar-label';
      lbl.textContent = m;

      col.appendChild(bar);
      col.appendChild(lbl);
      barChart.appendChild(col);
      setTimeout(() => { bar.style.height = h + 'px'; }, 100 + i * 60);
    });

    // ── MOBILE SIDEBAR ──
    const menuToggle = document.getElementById('menuToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
      sidebar.classList.add('open');
      overlay.classList.add('visible');
      document.body.style.overflow = 'hidden';
      menuToggle.style.display = 'none';
      sidebarClose.focus();
    }

    function closeSidebar() {
      sidebar.classList.remove('open');
      overlay.classList.remove('visible');
      document.body.style.overflow = '';
      menuToggle.style.display = '';
    }

    menuToggle.addEventListener('click', openSidebar);
    sidebarClose.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Escape key closes sidebar
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
    });

    // Swipe left to close
    let touchStartX = 0;
    sidebar.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    sidebar.addEventListener('touchend', e => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (diff > 60) closeSidebar();
    }, { passive: true });

    // ── EXPANDABLE SUB-MENUS ──
    document.querySelectorAll('.nav-item.has-sub').forEach(item => {
      item.addEventListener('click', function () {
        const sub = this.nextElementSibling;
        if (!sub || !sub.classList.contains('nav-sub')) return;

        const isOpen = sub.classList.contains('open');

        document.querySelectorAll('.nav-sub').forEach(s => s.classList.remove('open'));
        document.querySelectorAll('.nav-item.has-sub').forEach(n => n.classList.remove('expanded'));

        if (!isOpen) {
          sub.classList.add('open');
          this.classList.add('expanded');
        }

        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        this.classList.add('active');
      });
    });

    // ── PLAIN NAV ITEMS ──
    document.querySelectorAll('.nav-item:not(.has-sub):not(.logout)').forEach(item => {
      item.addEventListener('click', function () {
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        document.querySelectorAll('.nav-sub').forEach(s => s.classList.remove('open'));
        document.querySelectorAll('.nav-item.has-sub').forEach(n => n.classList.remove('expanded'));
        this.classList.add('active');
        if (window.innerWidth <= 860) closeSidebar();
      });
    });

    // ── SUB ITEMS ──
    document.querySelectorAll('.sub-item').forEach(item => {
      item.addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('.sub-item').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        if (window.innerWidth <= 860) closeSidebar();
      });
    });
  </script>
</body>
</html>