<?php
$page_title = 'Transactions';
$active_page = 'transactions';
include '../../includes/session.php';
include '../../includes/db.php';          // Provides $conn (mysqli instance)
include '../../includes/layout_open.php';

// ── Expected DB schema ────────────────────────────────────────────────────────
// transactions: id, reference_no, description, category, type (Income|Expense),
//               amount (positive for income, negative for expense),
//               transaction_date (DATE), property_id
// properties:   id, name
// ─────────────────────────────────────────────────────────────────────────────

$year = (int) date('Y');

// ── Total Income (full year) ──────────────────────────────────────────────────
$stmtIncome = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE type = 'Income' AND YEAR(transaction_date) = ?
");
mysqli_stmt_bind_param($stmtIncome, 'i', $year);
mysqli_stmt_execute($stmtIncome);
mysqli_stmt_bind_result($stmtIncome, $totalIncomeYear);
mysqli_stmt_fetch($stmtIncome);
mysqli_stmt_close($stmtIncome);
$totalIncomeYear = (int) $totalIncomeYear;

// ── Total Expenses (full year) ────────────────────────────────────────────────
$stmtExpense = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(ABS(amount)), 0)
    FROM transactions
    WHERE type = 'Expense' AND YEAR(transaction_date) = ?
");
mysqli_stmt_bind_param($stmtExpense, 'i', $year);
mysqli_stmt_execute($stmtExpense);
mysqli_stmt_bind_result($stmtExpense, $totalExpenseYear);
mysqli_stmt_fetch($stmtExpense);
mysqli_stmt_close($stmtExpense);
$totalExpenseYear = (int) $totalExpenseYear;

$netProfitYear = $totalIncomeYear - $totalExpenseYear;

// ── Transaction count (full year) ─────────────────────────────────────────────
$stmtCount = mysqli_prepare($conn, "
    SELECT COUNT(*) FROM transactions WHERE YEAR(transaction_date) = ?
");
mysqli_stmt_bind_param($stmtCount, 'i', $year);
mysqli_stmt_execute($stmtCount);
mysqli_stmt_bind_result($stmtCount, $totalCountYear);
mysqli_stmt_fetch($stmtCount);
mysqli_stmt_close($stmtCount);
$totalCountYear = (int) $totalCountYear;

// ── All rows for the table (JS handles client-side filtering) ─────────────────
$stmtAll = mysqli_prepare($conn, "
    SELECT
        t.id,
        DATE_FORMAT(t.transaction_date, '%b %d') AS date_label,
        DATE_FORMAT(t.transaction_date, '%Y-%m')  AS month_val,
        t.reference_no,
        t.description,
        t.category,
        p.property_name   AS property_name,
        t.type,
        t.amount
    FROM transactions t
    LEFT JOIN properties p ON p.property_id = t.property_id
    WHERE YEAR(t.transaction_date) = ?
    ORDER BY t.transaction_date DESC
");
mysqli_stmt_bind_param($stmtAll, 'i', $year);
mysqli_stmt_execute($stmtAll);
$result = mysqli_stmt_get_result($stmtAll);
$txns = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmtAll);

// Dynamic category list for the dropdown
$categories = array_values(array_unique(array_column($txns, 'category')));
sort($categories);

function formatPeso(int $n): string
{
    return '₱ ' . number_format(abs($n));
}
?>

<div class="page-header">
    <div class="top-header">
        <h2>Transactions</h2>
        <div class="page-header-sub">Full ledger of all financial transactions</div>
    </div>
    <button class="btn btn-secondary" id="exportCsvBtn">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" y1="15" x2="12" y2="3" />
        </svg>
        Export CSV
    </button>
</div>

<div class="page-inner">
    <div class="cards-area">

        <!-- ── Stat cards: full-year totals — NEVER change when filters change ── -->
        <div class="stat-row">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Income</div>
                    <div class="stat-value"><?= formatPeso($totalIncomeYear) ?></div>
                    <div class="stat-sub">This year</div>
                </div>
                <div class="stat-icon-wrap green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                        <polyline points="17 6 23 6 23 12" />
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value"><?= formatPeso($totalExpenseYear) ?></div>
                    <div class="stat-sub">This year</div>
                </div>
                <div class="stat-icon-wrap red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" />
                        <polyline points="17 18 23 18 23 12" />
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Net Profit</div>
                    <div class="stat-value" style="color:var(--<?= $netProfitYear >= 0 ? 'success' : 'danger' ?>);">
                        <?= ($netProfitYear < 0 ? '−' : '') . formatPeso($netProfitYear) ?>
                    </div>
                    <div class="stat-sub">This year</div>
                </div>
                <div class="stat-icon-wrap blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Transactions</div>
                    <div class="stat-value"><?= $totalCountYear ?></div>
                    <div class="stat-sub">This year</div>
                </div>
                <div class="stat-icon-wrap gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- ── Transaction ledger ──────────────────────────────────────────── -->
        <div class="card">
            <div class="txn-card-header">
                <span class="card-title">Transaction Ledger</span>

                <div class="txn-filters">
                    <select id="typeFilter">
                        <option value="">All Types</option>
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                    </select>

                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="month" id="monthFilter" value="<?= date('Y-m') ?>" />

                    <span class="filter-badge" id="filterCount"></span>

                    <button class="btn-clear" id="clearFiltersBtn" onclick="clearFilters()" style="display:none;">
                        ✕ Clear
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Property</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($txns as $t):
                            $isIncome = $t['type'] === 'Income';
                            $amountDisplay = ($isIncome ? '+' : '−') . '₱' . number_format(abs((int) $t['amount']));
                            ?>
                            <tr data-month="<?= htmlspecialchars($t['month_val']) ?>"
                                data-type="<?= htmlspecialchars($t['type']) ?>"
                                data-category="<?= htmlspecialchars($t['category']) ?>"
                                data-amount="<?= (int) $t['amount'] ?>">
                                <td style="color:var(--text-soft);font-size:12px;"><?= htmlspecialchars($t['date_label']) ?>
                                </td>
                                <td><strong><?= htmlspecialchars($t['reference_no']) ?></strong></td>
                                <td><?= htmlspecialchars($t['description']) ?></td>
                                <td><span class="badge badge-blue"><?= htmlspecialchars($t['category']) ?></span></td>
                                <td style="font-size:12px;color:var(--text-soft);">
                                    <?= htmlspecialchars($t['property_name'] ?? '—') ?></td>
                                <td>
                                    <span class="badge <?= $isIncome ? 'badge-green' : 'badge-red' ?>">
                                        <?= htmlspecialchars($t['type']) ?>
                                    </span>
                                </td>
                                <td style="font-weight:700;color:var(--<?= $isIncome ? 'success' : 'danger' ?>);">
                                    <?= $amountDisplay ?>
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
                    <div style="color:#aaa;font-size:14px;">No transactions match your filters.</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ── Scoped styles: filter bar layout ──────────────────────────────────── -->
<style>
    /* Override card-header specifically for this page */
    .txn-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        /* Do NOT wrap — keep title left, filters right on one line */
        flex-wrap: nowrap;
    }

    .txn-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        /* Prevent the filter group from growing/shrinking so it never wraps onto a new line */
        flex: 0 0 auto;
    }

    .txn-filters select,
    .txn-filters input[type="month"] {
        padding: 6px 11px;
        height: 34px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 13px;
        background: var(--white);
        color: var(--text);
        outline: none;
        cursor: pointer;
        transition: border-color .18s;
    }

    .txn-filters select:focus,
    .txn-filters input[type="month"]:focus {
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
        transition: background .15s;
    }

    .btn-clear:hover {
        background: #dceeff;
    }

    /* Allow wrap only on mobile */
    @media (max-width: 640px) {
        .txn-card-header {
            flex-wrap: wrap;
        }

        .txn-filters {
            flex-wrap: wrap;
        }
    }
</style>

<script>
    (function () {
        const rows = Array.from(document.querySelectorAll('#tableBody tr'));
        const typeFilter = document.getElementById('typeFilter');
        const catFilter = document.getElementById('categoryFilter');
        const monthFilter = document.getElementById('monthFilter');
        const filterCount = document.getElementById('filterCount');
        const clearBtn = document.getElementById('clearFiltersBtn');
        const emptyState = document.getElementById('emptyState');

        function applyFilters() {
            const type = typeFilter.value;
            const cat = catFilter.value;
            const month = monthFilter.value;
            let n = 0;

            rows.forEach(function (row) {
                const show =
                    (!type || row.dataset.type === type) &&
                    (!cat || row.dataset.category === cat) &&
                    (!month || row.dataset.month === month);

                row.style.display = show ? '' : 'none';
                if (show) n++;
            });

            filterCount.textContent = n + ' result' + (n !== 1 ? 's' : '');
            emptyState.style.display = n === 0 ? 'block' : 'none';
            clearBtn.style.display = (type || cat) ? 'inline-block' : 'none';
        }

        window.clearFilters = function () {
            typeFilter.value = '';
            catFilter.value = '';
            applyFilters();
        };

        // Export CSV — only visible (filtered) rows
        document.getElementById('exportCsvBtn').addEventListener('click', function () {
            const visible = rows.filter(r => r.style.display !== 'none');
            const headers = ['Date', 'Reference', 'Description', 'Category', 'Property', 'Type', 'Amount'];
            const lines = [headers.join(',')];

            visible.forEach(function (row) {
                const cells = Array.from(row.querySelectorAll('td')).map(function (td) {
                    return '"' + td.innerText.replace(/"/g, '""').trim() + '"';
                });
                lines.push(cells.join(','));
            });

            const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = Object.assign(document.createElement('a'), { href: url, download: 'transactions.csv' });
            a.click();
            URL.revokeObjectURL(url);
        });

        typeFilter.addEventListener('change', applyFilters);
        catFilter.addEventListener('change', applyFilters);
        monthFilter.addEventListener('change', applyFilters);

        applyFilters();
    })();
</script>

<?php include '../../includes/layout_close.php'; ?>