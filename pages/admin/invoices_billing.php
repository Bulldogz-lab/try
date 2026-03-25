<?php
$page_title = 'Invoices / Billing';
$active_page = 'invoices_billing';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

$statsResult = mysqli_query($conn, "
    SELECT
        COUNT(*)                                        AS total,
        SUM(status = 'Paid')                            AS paid,
        SUM(status = 'Pending')                         AS pending,
        SUM(status = 'Overdue')                         AS overdue
    FROM invoices
");
$stats = mysqli_fetch_assoc($statsResult);

$allResult = mysqli_query($conn, "
    SELECT
        i.id,
        i.invoice_no,
        t.full_name          AS tenant_name,
        i.unit_id,
        DATE_FORMAT(i.issued_date, '%b %d') AS issued_label,
        DATE_FORMAT(i.due_date,   '%b %d') AS due_label,
        i.items,
        i.total,
        i.status
    FROM invoices i
    LEFT JOIN tenants t ON t.tenant_id = i.tenant_id
    ORDER BY i.issued_date DESC
");
$invoices = mysqli_fetch_all($allResult, MYSQLI_ASSOC);

$tenantsResult = mysqli_query($conn, "SELECT tenant_id, full_name FROM tenants ORDER BY full_name ASC");
$tenants = mysqli_fetch_all($tenantsResult, MYSQLI_ASSOC);
?>

<div class="page-header">
  <div class="top-header">
    <h2>Invoices &amp; Billing</h2>
    <div class="page-header-sub">Generate, send and track invoices for all tenants</div>
  </div>
  <button class="btn btn-primary" id="openNewInvoiceBtn">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    New Invoice
  </button>
</div>

<div class="page-inner">
  <div class="cards-area">

    <div class="stat-row">
      <div class="stat-card">
        <div>
          <div class="stat-label">Total Invoices</div>
          <div class="stat-value"><?= (int) $stats['total'] ?></div>
          <div class="stat-sub">All time</div>
        </div>
        <div class="stat-icon-wrap blue">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
          </svg>
        </div>
      </div>
      <div class="stat-card">
        <div>
          <div class="stat-label">Paid</div>
          <div class="stat-value"><?= (int) $stats['paid'] ?></div>
          <div class="stat-sub">All time</div>
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
          <div class="stat-label">Pending</div>
          <div class="stat-value"><?= (int) $stats['pending'] ?></div>
          <div class="stat-sub">All time</div>
        </div>
        <div class="stat-icon-wrap gold">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
          </svg>
        </div>
      </div>
      <div class="stat-card">
        <div>
          <div class="stat-label">Overdue</div>
          <div class="stat-value"><?= (int) $stats['overdue'] ?></div>
          <div class="stat-sub">All time</div>
        </div>
        <div class="stat-icon-wrap red">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
          </svg>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="inv-card-header">
        <span class="card-title">Invoice List</span>
        <div class="inv-filters">
          <input type="text" id="searchFilter" placeholder="Search tenant or invoice…" />
          <select id="statusFilter">
            <option value="">All Status</option>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
            <option value="Overdue">Overdue</option>
          </select>
          <span class="filter-badge" id="filterCount"></span>
          <button class="btn-clear" id="clearFiltersBtn" style="display:none;" onclick="clearFilters()">✕ Clear</button>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Tenant</th>
              <th>Unit</th>
              <th>Issued</th>
              <th>Due</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="invoiceTableBody">
            <?php foreach ($invoices as $inv):
              $statusClass = match ($inv['status']) {
                'Paid' => 'success',
                'Pending' => 'pending',
                'Overdue' => 'danger',
                default => 'blue'
              };
              ?>
              <tr data-status="<?= htmlspecialchars($inv['status']) ?>"
                data-search="<?= strtolower(htmlspecialchars($inv['invoice_no'] . ' ' . $inv['tenant_name'] . ' ' . $inv['unit'])) ?>"
                data-id="<?= (int) $inv['id'] ?>">
                <td><strong><?= htmlspecialchars($inv['invoice_no']) ?></strong></td>
                <td><?= htmlspecialchars($inv['tenant_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($inv['unit']) ?></td>
                <td style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($inv['issued_label']) ?></td>
                <td style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($inv['due_label']) ?></td>
                <td style="font-size:12px;"><?= htmlspecialchars($inv['items']) ?></td>
                <td style="font-weight:700;">₱ <?= number_format((float) $inv['total'], 2) ?></td>
                <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
                <td>
                  <div style="display:flex;gap:5px;">
                    <a href="view_invoice.php?id=<?= (int) $inv['id'] ?>" class="btn btn-secondary"
                      style="padding:4px 10px;font-size:11px;">View</a>
                    <button class="btn btn-primary send-btn" data-id="<?= (int) $inv['id'] ?>"
                      style="padding:4px 10px;font-size:11px;">Send</button>
                    <button class="btn btn-secondary status-btn" data-id="<?= (int) $inv['id'] ?>"
                      data-status="<?= htmlspecialchars($inv['status']) ?>"
                      style="padding:4px 10px;font-size:11px;">⋯</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div id="emptyState" style="display:none;text-align:center;padding:52px 16px;">
          <svg width="40" height="40" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24"
            style="margin:0 auto 12px;display:block;">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <div style="color:#aaa;font-size:14px;">No invoices match your filters.</div>
        </div>
      </div>
    </div>

  </div>
</div>

<div id="invoiceModal" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">New Invoice</span>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form method="POST" action="../../process/admin-process/invoice.php" class="modal-form">
      <div class="form-row">
        <div class="form-group">
          <label>Tenant</label>
          <select name="tenant_id" required>
            <option value="">Select tenant…</option>
            <?php foreach ($tenants as $t): ?>
              <option value="<?= (int) $t['tenant_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Unit</label>
          <input type="text" name="unit" placeholder="e.g. A-101" required />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Issued Date</label>
          <input type="date" name="issued_date" value="<?= date('Y-m-d') ?>" required />
        </div>
        <div class="form-group">
          <label>Due Date</label>
          <input type="date" name="due_date" required />
        </div>
      </div>
      <div class="form-group">
        <label>Items / Description</label>
        <input type="text" name="items" placeholder="e.g. Rent + Water" required />
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Total Amount (₱)</label>
          <input type="number" name="total" min="0" step="0.01" placeholder="0.00" required />
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="Pending">Pending</option>
            <option value="Paid">Paid</option>
            <option value="Overdue">Overdue</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Invoice</button>
      </div>
    </form>
  </div>
</div>

<!-- ── Status change dropdown (contextual) ───────────────────────────────── -->
<div id="statusDropdown" class="status-dropdown" style="display:none;">
  <button onclick="updateStatus('Paid')"> ✅ Mark as Paid</button>
  <button onclick="updateStatus('Pending')">🕐 Mark as Pending</button>
  <button onclick="updateStatus('Overdue')">⚠️ Mark as Overdue</button>
  <hr style="margin:4px 0;border:none;border-top:1px solid #eee;">
  <button onclick="deleteInvoice()" style="color:#d43b3b;">🗑 Delete</button>
</div>

<!-- ── Scoped styles ─────────────────────────────────────────────────────── -->
<style>
  /* Card header — title left, filters right */
  .inv-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: nowrap;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
  }

  .inv-filters {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
  }

  .inv-filters input[type="text"],
  .inv-filters select {
    padding: 6px 11px;
    height: 34px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 13px;
    background: var(--white);
    color: var(--text);
    outline: none;
    transition: border-color .18s;
  }

  .inv-filters input[type="text"] {
    width: 200px;
  }

  .inv-filters input[type="text"]:focus,
  .inv-filters select:focus {
    border-color: #4f8ef7;
  }

  .filter-badge {
    font-size: 12px;
    color: #888;
    padding: 5px 10px;
    background: #f5f6fa;
    border-radius: 20px;
    border: 1px solid var(--border);
    white-space: nowrap;
  }

  .btn-clear {
    font-size: 12px;
    color: #4f8ef7;
    border: 1px solid #d0e2ff;
    background: #eff4ff;
    padding: 6px 12px;
    border-radius: var(--radius);
    cursor: pointer;
    white-space: nowrap;
  }

  .btn-clear:hover {
    background: #dceeff;
  }

  /* Modal */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .modal-box {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, .18);
    overflow: hidden;
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
  }

  .modal-title {
    font-size: 15px;
    font-weight: 700;
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 16px;
    color: #999;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
  }

  .modal-close:hover {
    background: #f0f0f0;
    color: #333;
  }

  .modal-form {
    padding: 20px 22px;
  }

  .form-row {
    display: flex;
    gap: 14px;
  }

  .form-row .form-group {
    flex: 1;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 14px;
  }

  .form-group label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-soft);
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .form-group input,
  .form-group select {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 13px;
    background: var(--white);
    color: var(--text);
    outline: none;
    transition: border-color .18s;
  }

  .form-group input:focus,
  .form-group select:focus {
    border-color: #4f8ef7;
  }

  .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 6px;
  }

  /* Status dropdown */
  .status-dropdown {
    position: fixed;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, .12);
    padding: 6px;
    z-index: 999;
    min-width: 180px;
  }

  .status-dropdown button {
    display: block;
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    padding: 8px 12px;
    font-size: 13px;
    border-radius: 6px;
    cursor: pointer;
    color: var(--text);
  }

  .status-dropdown button:hover {
    background: #f5f6fa;
  }

  @media (max-width: 640px) {
    .inv-card-header {
      flex-wrap: wrap;
    }

    .inv-filters {
      flex-wrap: wrap;
    }

    .form-row {
      flex-direction: column;
      gap: 0;
    }
  }
</style>

<script>
  (function () {
    // ── Filtering ──────────────────────────────────────────────────────────
    const rows = Array.from(document.querySelectorAll('#invoiceTableBody tr'));
    const searchInput = document.getElementById('searchFilter');
    const statusSel = document.getElementById('statusFilter');
    const countBadge = document.getElementById('filterCount');
    const clearBtn = document.getElementById('clearFiltersBtn');
    const emptyState = document.getElementById('emptyState');

    function applyFilters() {
      const q = searchInput.value.toLowerCase().trim();
      const status = statusSel.value;
      let n = 0;

      rows.forEach(function (row) {
        const matchSearch = !q || row.dataset.search.includes(q);
        const matchStatus = !status || row.dataset.status === status;
        const show = matchSearch && matchStatus;
        row.style.display = show ? '' : 'none';
        if (show) n++;
      });

      countBadge.textContent = n + ' result' + (n !== 1 ? 's' : '');
      emptyState.style.display = n === 0 ? 'block' : 'none';
      clearBtn.style.display = (q || status) ? 'inline-block' : 'none';
    }

    window.clearFilters = function () {
      searchInput.value = '';
      statusSel.value = '';
      applyFilters();
    };

    searchInput.addEventListener('input', applyFilters);
    statusSel.addEventListener('change', applyFilters);
    applyFilters();

    // ── New Invoice Modal ──────────────────────────────────────────────────
    document.getElementById('openNewInvoiceBtn').addEventListener('click', function () {
      document.getElementById('invoiceModal').style.display = 'flex';
    });

    window.closeModal = function () {
      document.getElementById('invoiceModal').style.display = 'none';
    };

    // Close on backdrop click
    document.getElementById('invoiceModal').addEventListener('click', function (e) {
      if (e.target === this) closeModal();
    });

    // ── Status / Delete dropdown ───────────────────────────────────────────
    let activeInvoiceId = null;
    const dropdown = document.getElementById('statusDropdown');

    document.querySelectorAll('.status-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        activeInvoiceId = this.dataset.id;
        const rect = this.getBoundingClientRect();
        dropdown.style.top = (rect.bottom + window.scrollY + 4) + 'px';
        dropdown.style.left = (rect.left + window.scrollX - 120) + 'px';
        dropdown.style.display = 'block';
      });
    });

    document.addEventListener('click', function () {
      dropdown.style.display = 'none';
    });

    window.updateStatus = function (newStatus) {
      if (!activeInvoiceId) return;
      fetch('ajax/../../process/admin-process/invoice.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=update_status&id=' + activeInvoiceId + '&status=' + encodeURIComponent(newStatus)
      })
        .then(r => r.json())
        .then(function (res) {
          if (res.success) {
            // Update badge in the row without reload
            const row = document.querySelector(`tr[data-id="${activeInvoiceId}"]`);
            if (row) {
              const badge = row.querySelector('.badge');
              const map = { Paid: 'success', Pending: 'pending', Overdue: 'danger' };
              badge.className = 'badge badge-' + (map[newStatus] || 'blue');
              badge.textContent = newStatus;
              row.dataset.status = newStatus;
            }
            dropdown.style.display = 'none';
            applyFilters();
          } else {
            alert('Failed to update status.');
          }
        })
        .catch(() => alert('Network error.'));
    };

    window.deleteInvoice = function () {
      if (!activeInvoiceId || !confirm('Delete this invoice? This cannot be undone.')) return;
      fetch('ajax/../../process/admin-process/invoice.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + activeInvoiceId
      })
        .then(r => r.json())
        .then(function (res) {
          if (res.success) {
            const row = document.querySelector(`tr[data-id="${activeInvoiceId}"]`);
            if (row) row.remove();
            dropdown.style.display = 'none';
            applyFilters();
          } else {
            alert('Failed to delete invoice.');
          }
        })
        .catch(() => alert('Network error.'));
    };

    // ── Send button ────────────────────────────────────────────────────────
    document.querySelectorAll('.send-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const id = this.dataset.id;
        if (!confirm('Send this invoice to the tenant?')) return;
        fetch('ajax/../../process/admin-process/invoice.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=send&id=' + id
        })
          .then(r => r.json())
          .then(function (res) {
            alert(res.success ? '✅ Invoice sent successfully.' : '❌ Failed to send invoice.');
          })
          .catch(() => alert('Network error.'));
      });
    });

  })();
</script>

<?php include '../../includes/layout_close.php'; ?>