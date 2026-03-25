// Configuration
const EXPENSES_CONFIG = {
    currentMonth: document.querySelector('input[name="month"]')?.value || new Date().toISOString().slice(0, 7),
    apiEndpoint: '../../process/admin-process/get_expenses.php',
    submitEndpoint: '../../process/admin-process/expenses.php',
    catColours: {
        'Maintenance': '#E74C3C',
        'Utilities': '#2563c4',
        'Salaries': '#2ECC71',
        'Admin': '#deaf37',
        'Insurance': '#8B5CF6',
        'Other': '#94a3b8'
    }
};

// DOM Elements
const DOM = {
    searchInput: document.getElementById('searchInput'),
    categoryFilter: document.getElementById('categoryFilter'),
    expensesBody: document.getElementById('expensesBody'),
    emptyState: document.getElementById('emptyState'),
    tableFooter: document.getElementById('tableFooter'),
    legendContainer: document.getElementById('legendContainer'),
    recordCount: document.getElementById('recordCount'),
    footerTotal: document.getElementById('footerTotal'),
    modal: document.getElementById('expenseModal'),
    modalTitle: document.getElementById('modalTitle'),
    editId: document.getElementById('editId'),
    btnOpenAdd: document.getElementById('btnOpenAdd'),
    btnSave: document.getElementById('btnSave'),
    btnExportCSV: document.getElementById('btnExportCSV'),
    toast: document.getElementById('toast'),
    // Form fields
    fProperty: document.getElementById('fProperty'),
    fUnit: document.getElementById('fUnit'),
    fCategory: document.getElementById('fCategory'),
    fDescription: document.getElementById('fDescription'),
    fAmount: document.getElementById('fAmount'),
    fDate: document.getElementById('fDate'),
    fRecordedBy: document.getElementById('fRecordedBy'),
    // Stats
    statTotal: document.getElementById('statTotal'),
    statPercentage: document.getElementById('statPercentage'),
    statMaintenance: document.getElementById('statMaintenance'),
    statMaintenancePercent: document.getElementById('statMaintenancePercent'),
    statUtilities: document.getElementById('statUtilities'),
    statUtilitiesPercent: document.getElementById('statUtilitiesPercent'),
    statAdmin: document.getElementById('statAdmin'),
    statAdminPercent: document.getElementById('statAdminPercent')
};

// Charts
let trendChart = null;
let donutChart = null;

// ==================== UTILITY FUNCTIONS ====================

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function showToast(msg, type = 'success') {
    DOM.toast.textContent = msg;
    DOM.toast.className = `toast ${type} show`;
    setTimeout(() => DOM.toast.classList.remove('show'), 3000);
}

function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// ==================== TABLE RENDERING ====================

function renderTable(expenses) {
    DOM.expensesBody.innerHTML = '';

    if (expenses.length === 0) {
        DOM.emptyState.style.display = 'flex';
        DOM.tableFooter.style.display = 'none';
        return;
    }

    DOM.emptyState.style.display = 'none';
    DOM.tableFooter.style.display = 'block';

    let total = 0;
    expenses.forEach(e => {
        const col = EXPENSES_CONFIG.catColours[e.expense_category] || '#94a3b8';
        const row = document.createElement('tr');
        row.setAttribute('data-id', e.expense_id);
        row.innerHTML = `
      <td style="font-weight:600;">${escapeHtml(e.description)}</td>
      <td style="color:var(--text-soft);font-size:13px;">${escapeHtml(e.property_name || '—')}</td>
      <td style="color:var(--text-soft);font-size:13px;">${escapeHtml(e.unit_name || '—')}</td>
      <td style="color:var(--text-soft);font-size:12px;">${new Date(e.expense_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
      <td><span class="badge" style="background:${col}22;color:${col};">${escapeHtml(e.expense_category)}</span></td>
      <td style="font-weight:700;color:var(--danger);">₱ ${formatCurrency(e.amount)}</td>
      <td>
        <div class="tbl-actions">
          <button class="btn-icon btn-edit" onclick="ExpenseModal.openEdit(${escapeHtml(JSON.stringify(e))})">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            Edit
          </button>
          <button class="btn-icon btn-delete" onclick="ExpenseTable.delete(${e.expense_id})">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <polyline points="3 6 5 6 21 6" />
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
              <path d="M10 11v6M14 11v6" />
              <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
            </svg>
            Delete
          </button>
        </div>
      </td>
    `;
        DOM.expensesBody.appendChild(row);
        total += parseFloat(e.amount);
    });

    DOM.recordCount.textContent = expenses.length;
    DOM.footerTotal.textContent = formatCurrency(total);
}

// ==================== STATS UPDATES ====================

function updateStats(stats) {
    DOM.statTotal.textContent = stats.total.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    DOM.statMaintenance.textContent = stats.maintenance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    DOM.statUtilities.textContent = stats.utilities.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    DOM.statAdmin.textContent = stats.admin.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

    const maintenancePercent = stats.total > 0 ? Math.round(stats.maintenance / stats.total * 100) : 0;
    const utilitiesPercent = stats.total > 0 ? Math.round(stats.utilities / stats.total * 100) : 0;
    const adminPercent = stats.total > 0 ? Math.round(stats.admin / stats.total * 100) : 0;

    DOM.statMaintenancePercent.textContent = maintenancePercent + '%';
    DOM.statUtilitiesPercent.textContent = utilitiesPercent + '%';
    DOM.statAdminPercent.textContent = adminPercent + '%';
}

// ==================== LEGEND UPDATES ====================

function updateLegend(categories) {
    DOM.legendContainer.innerHTML = '';
    const total = categories.reduce((sum, cat) => sum + cat.total, 0);

    categories.forEach(cat => {
        const pct = total > 0 ? Math.round(cat.total / total * 100) : 0;
        const col = EXPENSES_CONFIG.catColours[cat.category] || '#94a3b8';
        const item = document.createElement('div');
        item.className = 'legend-item';
        item.innerHTML = `
      <div class="legend-dot" style="background:${col};"></div>
      <span class="legend-label">${escapeHtml(cat.category)}</span>
      <span class="legend-val">${pct}%</span>
    `;
        DOM.legendContainer.appendChild(item);
    });
}

// ==================== CHARTS ====================

function updateCharts(trends, categories) {
    if (trendChart) trendChart.destroy();
    if (donutChart) donutChart.destroy();

    const trendLabels = trends.map(t => t.label);
    const trendData = trends.map(t => t.amount);
    const catLabels = categories.map(c => c.category);
    const catData = categories.map(c => c.total);
    const catCols = catLabels.map(l => EXPENSES_CONFIG.catColours[l] || '#94a3b8');

    // Trend Chart
    trendChart = new Chart(document.getElementById('expTrendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Total Expenses',
                data: trendData,
                borderColor: '#2563c4',
                backgroundColor: 'rgba(37,99,196,.08)',
                borderWidth: 2.5,
                tension: .4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#2563c4',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { callback: v => '₱' + (v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v), font: { size: 11 } }
                }
            }
        }
    });

    // Donut Chart
    donutChart = new Chart(document.getElementById('catDonut'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData,
                backgroundColor: catCols,
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
}

// ==================== DATA LOADING ====================

async function loadExpenses() {
    const search = DOM.searchInput.value.trim();
    const category = DOM.categoryFilter.value;

    try {
        const params = new URLSearchParams();
        params.append('month', EXPENSES_CONFIG.currentMonth);
        params.append('action', 'filter');
        if (search) params.append('q', search);
        if (category) params.append('category', category);

        const res = await fetch(EXPENSES_CONFIG.apiEndpoint + '?' + params);
        const data = await res.json();

        renderTable(data.expenses);
        updateStats(data.stats);
        updateCharts(data.trends, data.categories);
        updateLegend(data.categories);
    } catch (e) {
        console.error('Error loading expenses:', e);
        showToast('Error loading expenses', 'error');
    }
}

// ==================== MODAL CLASS ====================

const ExpenseModal = {
    open() {
        DOM.modalTitle.textContent = 'Log Expense';
        DOM.editId.value = '';
        DOM.fProperty.value = '';
        DOM.fUnit.value = '';
        DOM.fCategory.value = '';
        DOM.fDescription.value = '';
        DOM.fAmount.value = '';
        DOM.fDate.value = new Date().toISOString().split('T')[0];
        DOM.modal.classList.add('open');
    },

    openEdit(exp) {
        DOM.modalTitle.textContent = 'Edit Expense';
        DOM.editId.value = exp.expense_id;
        DOM.fProperty.value = exp.property_id || '';
        DOM.fUnit.value = exp.unit_id || '';
        DOM.fCategory.value = exp.expense_category || '';
        DOM.fDescription.value = exp.description || '';
        DOM.fAmount.value = exp.amount || '';
        DOM.fDate.value = exp.expense_date || '';
        DOM.modal.classList.add('open');
    },

    close() {
        DOM.modal.classList.remove('open');
    }
};

// ==================== FORM CLASS ====================

const ExpenseForm = {
    async save() {
        const id = DOM.editId.value;
        const property_id = DOM.fProperty.value;
        const unit_id = DOM.fUnit.value;
        const category = DOM.fCategory.value;
        const description = DOM.fDescription.value.trim();
        const amount = DOM.fAmount.value;
        const date = DOM.fDate.value;

        if (!category || !description || !amount || !date) {
            showToast('Please fill in all required fields.', 'error');
            return;
        }

        DOM.btnSave.disabled = true;
        DOM.btnSave.textContent = 'Saving…';

        try {
            const fd = new FormData();
            fd.append('action', id ? 'update' : 'create');
            if (id) fd.append('expense_id', id);
            fd.append('property_id', property_id);
            fd.append('unit_id', unit_id);
            fd.append('expense_category', category);
            fd.append('description', description);
            fd.append('amount', amount);
            fd.append('expense_date', date);

            const res = await fetch(EXPENSES_CONFIG.submitEndpoint, {
                method: 'POST',
                body: fd
            });

            const json = await res.json();

            if (json.success) {
                showToast(id ? 'Expense updated!' : 'Expense logged!', 'success');
                ExpenseModal.close();
                setTimeout(() => loadExpenses(), 500);
            } else {
                showToast(json.message || 'Something went wrong.', 'error');
            }
        } catch (e) {
            showToast('Error: ' + e.message, 'error');
        } finally {
            DOM.btnSave.disabled = false;
            DOM.btnSave.textContent = 'Save Expense';
        }
    }
};

// ==================== TABLE CLASS ====================

const ExpenseTable = {
    async delete(id) {
        if (!confirm('Delete this expense? This cannot be undone.')) return;

        try {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('expense_id', id);

            const res = await fetch(EXPENSES_CONFIG.submitEndpoint, {
                method: 'POST',
                body: fd
            });

            const json = await res.json();

            if (json.success) {
                showToast('Expense deleted.', 'success');
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity .3s';
                    setTimeout(() => {
                        row.remove();
                        loadExpenses();
                    }, 350);
                }
            } else {
                showToast(json.message || 'Delete failed.', 'error');
            }
        } catch (e) {
            showToast('Error: ' + e.message, 'error');
        }
    },

    exportCSV() {
        const table = document.getElementById('expensesTable');
        const rows = [...table.querySelectorAll('tr')];
        const csvRows = rows.map(r =>
            [...r.querySelectorAll('th, td')]
                .slice(0, 6)
                .map(c => '"' + c.innerText.replace(/"/g, '""') + '"')
                .join(',')
        );
        const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `expenses_${EXPENSES_CONFIG.currentMonth}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }
};

// ==================== EVENT LISTENERS ====================

document.addEventListener('DOMContentLoaded', function () {
    // Filter listeners
    DOM.searchInput.addEventListener('keyup', loadExpenses);
    DOM.categoryFilter.addEventListener('change', loadExpenses);

    // Modal listeners
    DOM.btnOpenAdd.addEventListener('click', () => ExpenseModal.open());
    DOM.modal.addEventListener('click', e => {
        if (e.target === DOM.modal) ExpenseModal.close();
    });

    // Export listener
    DOM.btnExportCSV.addEventListener('click', () => ExpenseTable.exportCSV());

    // Initial load
    loadExpenses();
});