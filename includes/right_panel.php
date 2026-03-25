<?php
/**
 * right_panel.php — Shared right panel (calendar + schedule + activity)
 * Included on pages that show the right-hand panel (dashboard, etc.)
 */
?>
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
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
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