function updateStatus(bookingId, newStatus, btn) {
    const labels = { confirmed: 'confirm', cancelled: 'cancel', completed: 'mark as completed' };
    const colors = { confirmed: '#16a34a', cancelled: '#dc2626', completed: '#2563eb' };

    Swal.fire({
        title: `${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)} Booking?`,
        text: `Are you sure you want to ${labels[newStatus]} booking #BK-${String(bookingId).padStart(4, '0')}?`,
        icon: newStatus === 'cancelled' ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: colors[newStatus],
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${labels[newStatus]}`,
        cancelButtonText: 'No, go back'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Updating…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch('../../process/admin-process/update_booking_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ booking_id: bookingId, status: newStatus })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, timer: 1200, showConfirmButton: false })
                    .then(() => refreshTable(true)); // silent AJAX refresh, no page reload
            } else {
                Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Could not update booking.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Server unreachable. Please try again.' }));
    });
}

