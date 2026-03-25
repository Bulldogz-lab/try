// ── AMENITY ICONS ──────────────────────────────────────────
const amenityIcons = {
    'wifi':      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
    'shower':    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="8 6 10 4 8 2"/><polyline points="12 6 14 4 12 2"/><polyline points="16 6 18 4 16 2"/><path d="M4 20h16"/><path d="M6 20v-6a6 6 0 0112 0v6"/></svg>',
    'water':     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6 8 4 13 4 16a8 8 0 0016 0c0-3-2-8-8-14z"/></svg>',
    'rooftop':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>',
    'aircon':    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="8" rx="2"/><path d="M7 15l5 5 5-5M12 11v9"/></svg>',
    'tv':        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"/><path d="M9 21l3-3 3 3"/></svg>',
    'fridge':    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="4" y1="10" x2="20" y2="10"/></svg>',
    'parking':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17V7h4a3 3 0 010 6H9"/></svg>',
    'pool':      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-5 10-5 10 5 10 5-3 5-10 5-10-5-10-5z"/><line x1="12" y1="2" x2="12" y2="5"/></svg>',
    'gym':       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 4v16M18 4v16M3 8h4M17 8h4M3 16h4M17 16h4"/></svg>',
    'balcony':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7"/><rect x="6" y="14" width="12" height="7"/><line x1="3" y1="21" x2="21" y2="21"/></svg>',
    'breakfast': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>',
    'security':  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    'kitchen':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/></svg>',
    'jacuzzi':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M4 12c2-2 4-2 6 0s4 2 6 0M4 16c2-2 4-2 6 0s4 2 6 0M12 4v4"/></svg>',
    'concierge': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    'garden':    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"/><path d="M12 8v13M9 10c-2 0-5 1-5 5h16c0-4-3-5-5-5"/></svg>',
    'laundry':   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="13" r="4"/><path d="M6 6h.01M9 6h.01"/></svg>',
    'safe':      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/><path d="M12 8v1M12 15v1M8 12h1M15 12h1"/></svg>',
    'spa':       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 00-7.35 16.76A2 2 0 006 20h12a2 2 0 001.35-.24A10 10 0 0012 2z"/><path d="M8 12s2-4 4-4 4 4 4 4"/></svg>',
    // by display name
    'Free WiFi': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
    'Air Conditioning': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 15.5L12 19l4-3.5"/><path d="M12 3v6M12 19v2M3 12h6M19 12h2M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/></svg>',
    'Balcony': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7"/><rect x="6" y="14" width="12" height="7"/><line x1="3" y1="21" x2="21" y2="21"/></svg>',
    'Breakfast Included': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>',
    'Hot Shower': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="8 6 10 4 8 2"/><polyline points="12 6 14 4 12 2"/><polyline points="16 6 18 4 16 2"/><path d="M4 20h16"/><path d="M6 20v-6a6 6 0 0112 0v6"/></svg>',
    'Smart TV': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"/><path d="M9 21l3-3 3 3"/></svg>',
    'Mini Fridge': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="4" y1="10" x2="20" y2="10"/></svg>',
    'In-room Safe': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/><path d="M12 8v1M12 15v1M8 12h1M15 12h1"/></svg>',
    'Private Jacuzzi': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M4 12c2-2 4-2 6 0s4 2 6 0M4 16c2-2 4-2 6 0s4 2 6 0M12 4v4"/></svg>',
    'Rooftop Pool': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-5 10-5 10 5 10 5-3 5-10 5-10-5-10-5z"/><line x1="12" y1="2" x2="12" y2="5"/></svg>',
    'Spa Discount': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 00-7.35 16.76A2 2 0 006 20h12a2 2 0 001.35-.24A10 10 0 0012 2z"/><path d="M8 12s2-4 4-4 4 4 4 4"/></svg>',
    'Pool Access': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-5 10-5 10 5 10 5-3 5-10 5-10-5-10-5z"/></svg>',
};

function getAmenityIcon(name, iconSlug) {
    return amenityIcons[name]
        || (iconSlug && amenityIcons[iconSlug])
        || amenityIcons[name.toLowerCase()]
        || '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/></svg>';
}

// ── SIDEBAR ────────────────────────────────────────────────
function openSidebar() {
    document.getElementById('sidebarOverlay').classList.add('open');
    document.getElementById('profileSidebar').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.getElementById('profileSidebar').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('profileBtn').addEventListener('click', openSidebar);
document.getElementById('sidebarClose').addEventListener('click', closeSidebar);

// ── HEADER SCROLL ──────────────────────────────────────────
const hdr = document.getElementById('hdr');
window.addEventListener('scroll', () => hdr.classList.toggle('scrolled', scrollY > 20));

// ── HAMBURGER ──────────────────────────────────────────────
const burger = document.getElementById('hamburger');
const mob    = document.getElementById('mobileNav');
let mobOpen  = false;
burger.addEventListener('click', () => {
    mobOpen = !mobOpen;
    mob.classList.toggle('open', mobOpen);
    const s = burger.querySelectorAll('span');
    if (mobOpen) {
        s[0].style.transform = 'translateY(6.5px) rotate(45deg)';
        s[1].style.opacity   = '0';
        s[2].style.transform = 'translateY(-6.5px) rotate(-45deg)';
    } else resetB();
});
function resetB() { burger.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; }); }
function closeMob() { mobOpen = false; mob.classList.remove('open'); resetB(); }

// ── ROOM MODAL ─────────────────────────────────────────────
function openRoomModal(room) {
    const imgWrap = document.getElementById('modalImgBg');
    imgWrap.innerHTML = `
        <img src="${room.image}" alt="${room.name}"
             style="width:100%;height:100%;object-fit:cover;display:block;"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <div style="display:none;width:100%;height:100%;" class="${room.grad || 'g1'}"></div>`;
    imgWrap.style.cssText = 'width:100%;height:100%;overflow:hidden;';

    document.getElementById('modalRoomName').textContent    = room.name;
    document.getElementById('modalRoomLoc').textContent     = room.location;
    document.getElementById('modalRoomPrice').innerHTML     = room.price + ' <sub>/ night</sub>';
    document.getElementById('modalRoomRating').innerHTML    =
        `<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
         ${room.rating || '4.8'} · 5 stars`;
    document.getElementById('modalRoomDesc').textContent    = room.desc;

    const amenDiv  = document.getElementById('modalAmenities');
    const amenList = Array.isArray(room.amenities) ? room.amenities : [];
    amenDiv.innerHTML = amenList.length
        ? amenList.map(a => {
            const name = (a && typeof a === 'object') ? (a.name || '') : String(a);
            const icon = (a && typeof a === 'object') ? (a.icon || null) : null;
            return `<div class="amenity-item">
                ${getAmenityIcon(name, icon)}
                <div class="amenity-name">${name}</div>
            </div>`;
          }).join('')
        : '<p style="color:var(--gray-400);font-size:0.85rem;">No amenities listed.</p>';

    const today    = new Date(); today.setDate(today.getDate() + 1);
    const checkout = new Date(today); checkout.setDate(checkout.getDate() + 3);
    document.getElementById('modalCheckin').value  = today.toISOString().split('T')[0];
    document.getElementById('modalCheckout').value = checkout.toISOString().split('T')[0];
    document.getElementById('modalGuests').value   = room.guests || 2;
    document.getElementById('roomModal').dataset.unitId = room.id;
    document.getElementById('roomModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeRoomModal() {
    document.getElementById('roomModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('roomModalClose').addEventListener('click', closeRoomModal);
document.getElementById('roomModal').addEventListener('click', e => {
    if (e.target === document.getElementById('roomModal')) closeRoomModal();
});

// ── CONFIRM BOOKING (improved success modal) ───────────────
function confirmBooking() {
    const checkin  = document.getElementById('modalCheckin').value;
    const checkout = document.getElementById('modalCheckout').value;
    const guests   = document.getElementById('modalGuests').value;
    const roomName = document.getElementById('modalRoomName').textContent;
    const unitId   = document.getElementById('roomModal').dataset.unitId;

    if (!checkin || !checkout) {
        Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both check-in and check-out dates.', confirmButtonColor: '#c9a84c' });
        return;
    }
    if (new Date(checkout) <= new Date(checkin)) {
        Swal.fire({ icon: 'warning', title: 'Invalid Dates', text: 'Check-out must be after check-in.', confirmButtonColor: '#c9a84c' });
        return;
    }
    if (new Date(checkin) < new Date(new Date().toDateString())) {
        Swal.fire({ icon: 'warning', title: 'Invalid Date', text: 'Check-in date cannot be in the past.', confirmButtonColor: '#c9a84c' });
        return;
    }

    Swal.fire({
        title: 'Processing…',
        text: 'Submitting your booking request.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('../../process/user-process/book_unit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ unit_id: unitId, checkin, checkout, guests })
    })
    .then(r => r.json())
    .then(data => {
        closeRoomModal();
        if (data.success) {
            Swal.fire({
                customClass: { popup: 'booking-success-popup' },
                showConfirmButton: true,
                confirmButtonText: 'Awesome, let\'s go!',
                confirmButtonColor: '#c9a84c',
                html: `
                <div class="bsm-hero">
                    <div class="bsm-check-row">
                        <div class="bsm-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="bsm-hero-text">
                            <div class="bsm-title">Booking Submitted!</div>
                            <div class="bsm-sub">We've received your reservation request.</div>
                        </div>
                    </div>
                    <div class="bsm-ref">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:9px;height:9px;stroke:#e8c86a;">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Ref #BK-${String(data.booking_id || '').padStart(4, '0')}
                    </div>
                </div>
                <div class="bsm-body">
                    <div class="bsm-unit-card">
                        <div class="bsm-unit-icon">
                            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div>
                            <div class="bsm-unit-label">Reserved Unit</div>
                            <div class="bsm-unit-name">${data.unit_name}</div>
                        </div>
                    </div>

                    <div class="bsm-dates">
                        <div class="bsm-date-side">
                            <div class="bsm-date-lbl">
                                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Check-in
                            </div>
                            <div class="bsm-date-val">${data.checkin}</div>
                        </div>
                        <div class="bsm-nights-mid">
                            <div class="bsm-nights-num">${data.nights}</div>
                            <div class="bsm-nights-lbl">nights</div>
                        </div>
                        <div class="bsm-date-side">
                            <div class="bsm-date-lbl">
                                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Check-out
                            </div>
                            <div class="bsm-date-val">${data.checkout}</div>
                        </div>
                    </div>

                    <div class="bsm-stats">
                        <div class="bsm-stat">
                            <div class="bsm-stat-icon ic-gold-sm">
                                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="bsm-stat-lbl">Guests</div>
                                <div class="bsm-stat-val">${data.guests} guest${data.guests > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        <div class="bsm-stat">
                            <div class="bsm-stat-icon ic-blue-sm">
                                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            <div>
                                <div class="bsm-stat-lbl">Total Amount</div>
                                <div class="bsm-stat-val">${data.total_amount}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bsm-notice">
                        <div class="bsm-notice-dot"></div>
                        <div>
                            <div class="bsm-notice-title">Pending Confirmation</div>
                            <div class="bsm-notice-sub">Our team will review and confirm your booking shortly. You'll be notified once it's approved.</div>
                        </div>
                    </div>
                </div>`,
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Booking Failed',
                text: data.message || 'Something went wrong. Please try again.',
                confirmButtonColor: '#c9a84c'
            });
        }
    })
    .catch(() => {
        closeRoomModal();
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'Could not reach the server. Please check your connection and try again.',
            confirmButtonColor: '#c9a84c'
        });
    });
}

// ── FILTERS ────────────────────────────────────────────────
function filterRooms(cat, btn) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.room-card').forEach(card => {
        if (cat === 'all') { card.style.display = ''; return; }
        const cats = card.dataset.cat || '';
        card.style.display = cats.includes(cat) ? '' : 'none';
    });
}
function searchRooms(val) {
    const q = val.toLowerCase();
    document.querySelectorAll('.room-card').forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        card.style.display = name.includes(q) ? '' : 'none';
    });
}

// ── TOAST ──────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.style.opacity   = '1';
    t.style.transform = 'translateX(-50%) translateY(0)';
    setTimeout(() => {
        t.style.opacity   = '0';
        t.style.transform = 'translateX(-50%) translateY(80px)';
    }, 3500);
}

// ── SCROLL REVEAL ──────────────────────────────────────────
const revObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); revObs.unobserve(e.target); }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -32px 0px' });
document.querySelectorAll('.reveal').forEach(el => revObs.observe(el));

// ── KEYBOARD ──────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeRoomModal(); closeSidebar(); closeManageModal(); }
});

// ── VERIFICATION BADGE ─────────────────────────────────────
const badgeText = document.getElementById('badgeText');
const badgeDot  = document.getElementById('badgeDot');
const verifyBtn = document.getElementById('verifyBtn');

function updateBadge() {
    const status = badgeText ? badgeText.textContent.trim().toLowerCase() : '';
    if (status === 'verified' || status === 'verified guest') {
        if (badgeDot)  badgeDot.style.background  = 'green';
        if (verifyBtn) verifyBtn.style.display     = 'none';
    } else {
        if (badgeDot)  badgeDot.style.background  = 'red';
        if (verifyBtn) verifyBtn.style.display     = 'inline-block';
    }
}
updateBadge();

// ── MANAGE STAY MODAL ──────────────────────────────────────
let currentBookingId = null;

function openManageModal(booking) {
    currentBookingId = booking.booking_id;

    const heroImg = document.getElementById('manageHeroImg');
    if (heroImg) {
        heroImg.style.backgroundImage = booking.image ? `url('${booking.image}')` : 'none';
    }

    document.getElementById('manageBookingRef').textContent = '#BK-' + String(booking.booking_id).padStart(4, '0');
    document.getElementById('manageUnitName').textContent   = booking.unit_name;
    document.getElementById('manageProperty').textContent   = booking.property_name;

    const st   = (booking.status || '').toLowerCase().replace(' ', '');
    const pill = document.getElementById('manageStatusPill');
    pill.className = 'mm-status mm-st-' + st;
    document.getElementById('manageStatusText').textContent = booking.status;

    const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    let inDate = null, outDate = null;
    try {
        inDate  = new Date(booking.checkin  + 'T12:00:00');
        outDate = new Date(booking.checkout + 'T12:00:00');
    } catch(e) {}

    document.getElementById('manageCheckin').textContent     = booking.checkin;
    document.getElementById('manageCheckout').textContent    = booking.checkout;
    document.getElementById('manageCheckinDay').textContent  = inDate  ? days[inDate.getDay()]  : '';
    document.getElementById('manageCheckoutDay').textContent = outDate ? days[outDate.getDay()] : '';
    document.getElementById('manageNightsNum').textContent   = booking.nights;

    const cdEl = document.getElementById('manageCountdownText');
    if (inDate) {
        const now  = new Date();
        const diff = Math.ceil((inDate - now) / (1000*60*60*24));
        if (diff > 0)        cdEl.innerHTML = '<strong>' + diff + ' day' + (diff !== 1 ? 's' : '') + '</strong> until check-in';
        else if (diff === 0) cdEl.innerHTML = '<strong>Today!</strong> Check-in day';
        else {
            const outDiff = Math.ceil((outDate - now) / (1000*60*60*24));
            if (outDiff > 0) cdEl.innerHTML = 'Day <strong>' + (booking.nights + diff) + '</strong> of ' + booking.nights + ' nights';
            else             cdEl.innerHTML = 'Stay <strong>completed</strong>';
        }
    }

    document.getElementById('manageGuests').textContent   = (booking.guests || 2) + ' guest' + ((booking.guests || 2) > 1 ? 's' : '');
    document.getElementById('manageTotal').textContent    = booking.total_amount;
    const perNight = booking.nights > 0
        ? 'PHP ' + Number(String(booking.total_amount).replace(/[^0-9.]/g, '') / booking.nights).toLocaleString('en-PH', { maximumFractionDigits: 0 })
        : '—';
    document.getElementById('managePerNight').textContent = perNight;

    const progressWrap = document.getElementById('manageProgressWrap');
    if (inDate && outDate) {
        const now     = new Date();
        const total   = outDate - inDate;
        const elapsed = now - inDate;
        const pct     = Math.max(0, Math.min(100, Math.round((elapsed / total) * 100)));
        document.getElementById('manageProgressFill').style.width = pct + '%';
        document.getElementById('manageProgressText').textContent = pct + '%';
        progressWrap.style.display = (pct > 0 && pct < 100) ? '' : 'none';
    } else {
        progressWrap.style.display = 'none';
    }

    document.getElementById('manageCancelBtn').style.display =
        (st === 'completed' || st === 'cancelled') ? 'none' : '';

    document.getElementById('manageModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeManageModal() {
    document.getElementById('manageModal').classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('manageModal').addEventListener('click', e => {
    if (e.target === document.getElementById('manageModal')) closeManageModal();
});

function cancelBooking() {
    if (!currentBookingId) return;
    Swal.fire({
        icon: 'warning',
        title: 'Cancel Booking?',
        text: 'Are you sure you want to cancel this reservation? This cannot be undone.',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel it',
        cancelButtonText: 'No, keep it'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Cancelling…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch('../../process/user-process/cancel_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ booking_id: currentBookingId })
        })
        .then(r => r.json())
        .then(data => {
            closeManageModal();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Cancelled', text: data.message, timer: 1800, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Could not cancel booking.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Server unreachable.' }));
    });
}

// ── ROOMS & HISTORY CAROUSEL ───────────────────────────────
const carouselState = {
    rooms:   { page: 0, perPage: 6 },
    history: { page: 0, perPage: 3 },
};

function initCarousel(type) {
    const state    = carouselState[type];
    const gridId   = type === 'rooms' ? 'roomsGrid' : 'historyList';
    const dotsId   = type === 'rooms' ? 'roomsDots' : 'historyDots';
    const prevId   = type === 'rooms' ? 'roomsPrev' : 'historyPrev';
    const nextId   = type === 'rooms' ? 'roomsNext' : 'historyNext';
    const grid     = document.getElementById(gridId);
    const dotsWrap = document.getElementById(dotsId);
    if (!grid) return;

    const items = Array.from(grid.children);
    const total = items.length;
    const pages = Math.ceil(total / state.perPage);

    const prevBtn = document.getElementById(prevId);
    const nextBtn = document.getElementById(nextId);
    if (total <= state.perPage) {
        if (prevBtn)  prevBtn.style.display  = 'none';
        if (nextBtn)  nextBtn.style.display  = 'none';
        if (dotsWrap) dotsWrap.style.display = 'none';
        return;
    }

    if (dotsWrap) {
        dotsWrap.innerHTML = '';
        for (let i = 0; i < pages; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            dot.onclick = () => goToPage(type, i);
            dotsWrap.appendChild(dot);
        }
    }
    renderPage(type);
}

function renderPage(type) {
    const state  = carouselState[type];
    const gridId = type === 'rooms' ? 'roomsGrid' : 'historyList';
    const dotsId = type === 'rooms' ? 'roomsDots' : 'historyDots';
    const prevId = type === 'rooms' ? 'roomsPrev' : 'historyPrev';
    const nextId = type === 'rooms' ? 'roomsNext' : 'historyNext';
    const grid   = document.getElementById(gridId);
    if (!grid) return;

    const items = Array.from(grid.children);
    const total = items.length;
    const pages = Math.ceil(total / state.perPage);
    const start = state.page * state.perPage;
    const end   = start + state.perPage;

    items.forEach((el, i) => {
        el.style.display = (i >= start && i < end) ? '' : 'none';
    });

    document.querySelectorAll(`#${dotsId} .carousel-dot`)
        .forEach((d, i) => d.classList.toggle('active', i === state.page));

    const prevBtn = document.getElementById(prevId);
    const nextBtn = document.getElementById(nextId);
    if (prevBtn) prevBtn.disabled = state.page === 0;
    if (nextBtn) nextBtn.disabled = state.page >= pages - 1;
}

function scrollCarousel(type, dir) {
    const state  = carouselState[type];
    const gridId = type === 'rooms' ? 'roomsGrid' : 'historyList';
    const grid   = document.getElementById(gridId);
    if (!grid) return;
    const pages  = Math.ceil(grid.children.length / state.perPage);
    state.page   = Math.max(0, Math.min(pages - 1, state.page + dir));
    renderPage(type);
}

function goToPage(type, page) {
    carouselState[type].page = page;
    renderPage(type);
}

document.addEventListener('DOMContentLoaded', () => {
    initCarousel('rooms');
    initCarousel('history');
});