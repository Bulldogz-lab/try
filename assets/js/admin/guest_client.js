const rows = Array.from(document.querySelectorAll('#guestTableBody tr[data-status]'));
const pills = document.querySelectorAll('#filterPills .filter-pill-sm');
const searchInput = document.getElementById('guestSearch');
const countEl = document.getElementById('visibleCount');
const noResults = document.getElementById('noResults');

let activeFilter = 'all';
let searchQuery = '';

function applyFilters() {
    let visible = 0;

    rows.forEach(row => {
        const status = row.dataset.status;   
        const search = row.dataset.search;   

        const filterOk = activeFilter === 'all' || status === activeFilter;
        const searchOk = searchQuery === '' || search.includes(searchQuery);

        const show = filterOk && searchOk;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    countEl.textContent = visible;
    noResults.style.display = visible === 0 ? 'block' : 'none';
}

pills.forEach(pill => {
    pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        activeFilter = pill.dataset.filter;
        applyFilters();
    });
});

searchInput.addEventListener('input', () => {
    searchQuery = searchInput.value.trim().toLowerCase();
    applyFilters();
});


function toggleBlacklist(userId, name, blacklist) {
    const action = blacklist ? 'block' : 'unblock';
    const color = blacklist ? '#dc2626' : '#16a34a';

    Swal.fire({
        title: `${blacklist ? 'Block' : 'Unblock'} Guest?`,
        text: `Are you sure you want to ${action} ${name}?`,
        icon: blacklist ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Updating…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch('../../process/admin-process/process_blacklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ user_id: userId, action: blacklist ? 'blacklist' : 'unblacklist' })
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Done!', text: data.message, timer: 1400, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed', text: data.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Server unreachable.' }));
    });
}