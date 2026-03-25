<?php
$page_title = 'Properties List';
$active_page = 'properties_list';

include '../../includes/session.php';
include '../../includes/layout_open.php';
include '../../includes/db.php';
include '../../includes/properties.php';
?>

<div class="page-header">
  <div class="top-header">
    <h2>All Properties</h2>
    <div class="page-header-sub">Manage and monitor all registered properties</div>
  </div>
  <a href="add_property.php" class="btn btn-primary">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Add Property
  </a>
</div>

<div class="page-inner">
  <div class="cards-area">
    <div class="stat-row">

      <div class="stat-card">
        <div>
          <div class="stat-label">Total Properties</div>
          <div class="stat-value" id="stat-total"><?= $total ?></div>
          <div class="stat-sub">+<span id="stat-new-month"><?= $new_month ?></span> this month</div>
        </div>
        <div class="stat-icon-wrap blue">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 9.5L12 3l9 6.5V21H3V9.5z" />
          </svg>
        </div>
      </div>

      <div class="stat-card">
        <div>
          <div class="stat-label">Occupied</div>
          <div class="stat-value" id="stat-occupied"><?= $occupied ?></div>
          <div class="stat-sub"><span id="stat-occ-pct"><?= $occ_pct ?></span>% occupancy</div>
        </div>
        <div class="stat-icon-wrap green">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
        </div>
      </div>

      <div class="stat-card">
        <div>
          <div class="stat-label">Vacant</div>
          <div class="stat-value" id="stat-vacant"><?= $vacant ?></div>
          <div class="stat-sub">Available now</div>
        </div>
        <div class="stat-icon-wrap gold">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
        </div>
      </div>

      <div class="stat-card">
        <div>
          <div class="stat-label">Under Maintenance</div>
          <div class="stat-value" id="stat-maintenance"><?= $maintenance ?></div>
          <div class="stat-sub">Properties flagged</div>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
          </svg>
        </div>
      </div>

    </div>

    <div class="card">
      <div class="card-header">
        <span class="card-title">Property Directory</span>
        <div style="display:flex;gap:8px;">
          <form method="GET" action="" style="margin:0;">
            <select name="type" onchange="this.form.submit()"
              style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius);font-size:13px;color:var(--text-soft);background:var(--white);">
              <option value="">All Types</option>
              <?php foreach ($allowed_types as $t): ?>
                <option value="<?= $t ?>" <?= $filter_type === $t ? 'selected' : '' ?>>
                  <?= htmlspecialchars($t) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Property</th>
              <th>Type</th>
              <th>Location</th>
              <th>Units</th>
              <th>Occupancy</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>

            <?php if (mysqli_num_rows($result) === 0): ?>
              <tr>
                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-soft);">
                  No properties found<?= $filter_type ? ' for type <strong>' . htmlspecialchars($filter_type) . '</strong>' : '' ?>.
                </td>
              </tr>

            <?php else: ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                $pid = (int) $row['property_id'];
                $unit_q = mysqli_query($conn, "
                    SELECT 
                        COUNT(*) AS total_units,
                        SUM(status = 'occupied') AS occupied_units
                    FROM units
                    WHERE property_id = $pid
                ");
                $unit_data    = mysqli_fetch_assoc($unit_q);
                $total_units  = (int) $unit_data['total_units'];
                $occupied_units = (int) $unit_data['occupied_units'];
                $row_pct      = $total_units > 0 ? round(($occupied_units / $total_units) * 100) : 0;
                $bar_col      = bar_color($row_pct);
                $prop_id      = 'P-' . str_pad($pid, 3, '0', STR_PAD_LEFT);
                ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <span style="font-size:22px;"><?= property_emoji($row['property_type']) ?></span>
                      <div>
                        <div style="font-weight:600;"><?= htmlspecialchars($row['property_name']) ?></div>
                        <div style="font-size:11px;color:var(--text-soft);">ID #<?= $prop_id ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($row['property_type']) ?></td>
                  <td><?= htmlspecialchars($row['address']) ?></td>
                  <td><?= $total_units ?></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                      <div style="flex:1;height:5px;background:var(--blue-50);border-radius:3px;">
                        <div style="width:<?= $row_pct ?>%;height:100%;background:<?= $bar_col ?>;border-radius:3px;"></div>
                      </div>
                      <span style="font-size:12px;font-weight:600;"><?= $row_pct ?>%</span>
                    </div>
                  </td>
                  <td><?= status_badge($row['status']) ?></td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <a href="view_property.php?id=<?= $pid ?>" class="btn btn-secondary"
                        style="padding:5px 12px;font-size:12px;">View</a>
                      <button class="btn btn-danger delete-property-btn"
                        style="padding:5px 12px;font-size:12px;"
                        data-id="<?= $pid ?>"
                        data-name="<?= htmlspecialchars($row['property_name']) ?>">Delete</button>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>

          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php
mysqli_close($conn);
include '../../includes/layout_close.php';
?>

<script>
// ── Animate a stat value changing ──────────────────────────────────────────
function animateStat(el, newVal) {
    el.style.transition = 'opacity .2s';
    el.style.opacity = '0';
    setTimeout(() => {
        el.textContent = newVal;
        el.style.opacity = '1';
    }, 200);
}

// ── Fetch fresh stats and update all stat cards ────────────────────────────
async function refreshStats() {
    try {
        const res  = await fetch('../../process/admin-process/get_property_stats.php');
        const data = await res.json();
        if (data.status !== 'success') return;

        const s = data.stats;
        animateStat(document.getElementById('stat-total'),       s.total);
        animateStat(document.getElementById('stat-occupied'),    s.occupied);
        animateStat(document.getElementById('stat-vacant'),      s.vacant);
        animateStat(document.getElementById('stat-maintenance'), s.maintenance);
        animateStat(document.getElementById('stat-new-month'),   s.new_this_month);

        const pct = s.total > 0 ? Math.round((s.occupied / s.total) * 100) : 0;
        document.getElementById('stat-occ-pct').textContent = pct;
    } catch (e) {
        console.error('Stats refresh failed:', e);
    }
}

// ── Delete handler ─────────────────────────────────────────────────────────
document.querySelectorAll('.delete-property-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
        const id    = this.dataset.id;
        const name  = this.dataset.name;
        const row   = this.closest('tr');
        const emoji = row.querySelector('td:first-child span')?.textContent || '';

        PS.confirm(
            `Remove property ${emoji} <strong>${name}</strong>? This action cannot be undone.`,
            async () => {
                try {
                    const formData = new FormData();
                    formData.append('property_id', id);

                    const response = await fetch('../../process/admin-process/delete_property.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        PS.toast(data.message, 'success');

                        row.style.transition = 'opacity .4s';
                        row.style.opacity = '0';

                        setTimeout(async () => {
                            row.remove();

                            // Show empty state if no rows left
                            const tbody     = document.querySelector('tbody');
                            const remaining = tbody.querySelectorAll('tr').length;
                            if (remaining === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = `
                                    <td colspan="7" style="text-align:center;padding:32px;color:var(--text-soft);">
                                        No properties found.
                                    </td>`;
                                tbody.appendChild(emptyRow);
                            }

                            // Refresh all stat cards
                            await refreshStats();

                        }, 400);

                    } else {
                        PS.toast(data.message, 'error');
                    }

                } catch (error) {
                    console.error(error);
                    PS.toast('Server error. Please try again.', 'error');
                }
            },
            {
                title: 'Remove Property',
                confirmLabel: 'Remove',
                confirmClass: 'btn btn-danger'
            }
        );
    });
});
</script>