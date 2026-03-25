<?php
include '../../includes/session.php';

if ($_SESSION['role'] !== 'user') {
    echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: "error",
        title: "Unauthorized",
        text: "You do not have permission to access this page.",
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        history.back();
    });
</script>
</body>
</html>';
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['name'] ?? 'Guest');
$last_name  = htmlspecialchars($_SESSION['last_name']  ?? '');
$full_name  = trim($first_name . ' ' . $last_name);
$email      = htmlspecialchars($_SESSION['email'] ?? '');
$initials   = strtoupper(mb_substr($first_name,0,1) . mb_substr($last_name,0,1));
$hour       = (int)date('G');
$greeting   = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$page_title     = 'Support & Help';
$page_hero_html = 'Support <em>&amp; Help</em>';
$page_hero_sub  = 'We\'re here for you 24/7. Find answers or reach our team directly.';
$page_hero_icon = '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
$active_nav     = 'support';
require '../../includes/_layout.php';

$faqs = [
    'Booking & Reservations' => [
        ['q'=>'How do I make a reservation?','a'=>'You can book directly through this website by browsing the Rooms section and clicking "Book Now." Select your check-in/check-out dates, number of guests, and confirm your booking using your saved payment method.'],
        ['q'=>'Can I modify my booking after confirmation?','a'=>'Yes, you can modify your booking up to 72 hours before your check-in date at no extra charge. Go to My Bookings and click "Manage Stay." Changes within 72 hours may incur a modification fee.'],
        ['q'=>'What is your cancellation policy?','a'=>'Free cancellation is available up to 48 hours before check-in. Cancellations within 48 hours are charged 50% of the total booking amount. No-shows are charged in full.'],
    ],
    'Check-in & Check-out' => [
        ['q'=>'What are the check-in and check-out times?','a'=>'Standard check-in is from 2:00 PM onwards. Check-out is at 12:00 PM noon. Early check-in and late check-out are subject to availability and may be arranged at the front desk.'],
        ['q'=>'Can I check in online?','a'=>'Yes! You can complete your pre-check-in online through My Bookings up to 24 hours before arrival. This speeds up the front-desk process significantly.'],
    ],
    'Payments & Billing' => [
        ['q'=>'What payment methods do you accept?','a'=>'We accept all major credit/debit cards (Visa, Mastercard), GCash, Maya, and PayPal. Cash payments are accepted at the property for incidentals only.'],
        ['q'=>'When is my card charged?','a'=>'Your card is pre-authorized at the time of booking. The full charge is applied upon check-in. Any additional charges (room service, etc.) are settled at check-out.'],
    ],
    'Loyalty Program' => [
        ['q'=>'How do I earn loyalty points?','a'=>'You earn 1 point for every ₱10 spent on room bookings. Bonus points are awarded during promotional periods, on your birthday, and when you refer friends.'],
        ['q'=>'When do points expire?','a'=>'Points are valid for 24 months from the date they were earned. Any account activity (booking or redemption) resets the expiry on all existing points.'],
    ],
];
?>

<link rel="stylesheet" href="../../assets/css/user-css/support.css">

<div class="contact-grid reveal">
    <div class="contact-card">
        <div class="contact-icon blue"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 015.17 12.9 19.79 19.79 0 012.1 4.27 2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></div>
        <div class="contact-title">Call Us</div>
        <div class="contact-detail">+63 33 123 4567<br>+63 912 345 6789</div>
        <div class="avail-chip green">● Available 24/7</div>
        <button class="contact-cta" onclick="showToast('Opening dialer...')">Call Now <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
    <div class="contact-card">
        <div class="contact-icon gold"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
        <div class="contact-title">Email Us</div>
        <div class="contact-detail">hello@filipinohomes.ph<br>support@filipinohomes.ph</div>
        <div class="avail-chip amber">● Replies in ~2 hours</div>
        <button class="contact-cta" onclick="document.getElementById('contactFormCard').scrollIntoView({behavior:'smooth'})">Send Message <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
    <div class="contact-card">
        <div class="contact-icon green"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
        <div class="contact-title">Live Chat</div>
        <div class="contact-detail">Chat with our team directly through our support portal.</div>
        <div class="avail-chip green">● Online Now</div>
        <button class="contact-cta" onclick="showToast('Opening live chat...')">Start Chat <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
</div>

<div class="card reveal rd1">
    <div class="card-title">
        <svg viewBox="0 0 24 24"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"/><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"/><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"/><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"/><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"/></svg>
        My Tickets
    </div>
    <div class="ticket-item">
        <div>
            <div class="ticket-subject">Late check-out request for Mar 26</div>
            <div class="ticket-meta">Submitted Mar 18, 2026 · <span class="ticket-num">#TKT-00892</span></div>
        </div>
        <span class="badge badge-gold" style="margin-left:auto;">In Progress</span>
    </div>
    <div class="ticket-item">
        <div>
            <div class="ticket-subject">Invoice correction for FH-2026-0044</div>
            <div class="ticket-meta">Submitted Feb 16, 2026 · <span class="ticket-num">#TKT-00801</span></div>
        </div>
        <span class="badge badge-green" style="margin-left:auto;">Resolved</span>
    </div>
    <p style="font-size:0.78rem;color:var(--text-soft);margin-top:14px;">No urgent tickets. Our team is available 24/7 for new inquiries.</p>
</div>

<div class="card reveal rd2">
    <div class="card-title">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Frequently Asked Questions
    </div>
    <?php foreach($faqs as $category => $items): ?>
    <div class="faq-category">
        <div class="faq-cat-title">
            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            <?php echo $category; ?>
        </div>
        <?php foreach($items as $idx => $faq): ?>
        <div class="faq-item" id="faq-<?php echo $category.$idx; ?>">
            <div class="faq-q" onclick="toggleFaq('faq-<?php echo $category.$idx; ?>')">
                <?php echo $faq['q']; ?>
                <svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="faq-a" id="faq-a-<?php echo $category.$idx; ?>">
                <div class="faq-a-inner"><?php echo $faq['a']; ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="card reveal rd3" id="contactFormCard">
    <div class="card-title">
        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Send Us a Message
    </div>
    <p style="font-size:0.84rem;color:var(--text-soft);margin-bottom:16px;">Tell us what you need and we'll get back to you within 2 hours.</p>
    <div style="margin-bottom:14px;">
        <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-mid);margin-bottom:8px;">Topic</div>
        <div class="ticket-types">
            <?php foreach(['Booking Inquiry','Payment Issue','Room Request','Feedback','Other'] as $t): ?>
            <button class="ticket-type<?php echo $t==='Booking Inquiry'?' selected':''; ?>" onclick="selectTicketType(this)"><?php echo $t; ?></button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-grid" style="margin-bottom:14px;">
        <div class="form-field">
            <label>Full Name</label>
            <input type="text" id="contact_name" value="<?php echo $full_name; ?>">
        </div>
        <div class="form-field">
            <label>Email</label>
            <input type="email" id="contact_email" value="<?php echo $email; ?>">
        </div>
    </div>
    <div class="form-grid cols-1" style="margin-bottom:14px;">
        <div class="form-field">
            <label>Subject</label>
            <input type="text" id="contact_subject" placeholder="Brief description of your concern">
        </div>
    </div>
    <div class="form-field" style="margin-bottom:18px;">
        <label>Message</label>
        <textarea id="contact_message" placeholder="Describe your concern in detail. Include your booking ID if applicable."></textarea>
    </div>
    <div id="contactError" style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;"></div>
    <button class="btn-primary" id="sendMsgBtn" onclick="submitTicket()">
        <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Send Message
    </button>
</div>

<script>
function toggleFaq(id) {
    const item   = document.getElementById(id);
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => {
        i.classList.remove('open');
        const a = i.querySelector('.faq-a');
        if (a) a.style.maxHeight = '0';
    });
    if (!isOpen) {
        item.classList.add('open');
        const a = item.querySelector('.faq-a');
        a.style.maxHeight = a.scrollHeight + 'px';
    }
}

function selectTicketType(el) {
    document.querySelectorAll('.ticket-type').forEach(t => t.classList.remove('selected'));
    el.classList.add('selected');
}

function submitTicket() {
    const name    = document.getElementById('contact_name').value.trim();
    const email   = document.getElementById('contact_email').value.trim();
    const subject = document.getElementById('contact_subject').value.trim();
    const message = document.getElementById('contact_message').value.trim();
    const errEl   = document.getElementById('contactError');
    errEl.style.display = 'none';

    if (!name)    { errEl.textContent = 'Full name is required.'; errEl.style.display='block'; return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { errEl.textContent = 'Please enter a valid email address.'; errEl.style.display='block'; return; }
    if (!subject) { errEl.textContent = 'Subject is required.'; errEl.style.display='block'; return; }
    if (message.length < 10) { errEl.textContent = 'Please write a more detailed message (at least 10 characters).'; errEl.style.display='block'; return; }

    const selectedTopic = document.querySelector('.ticket-type.selected')?.textContent || 'General';
    const btn = document.getElementById('sendMsgBtn');
    btn.disabled = true; btn.textContent = 'Sending…';

    setTimeout(() => {
        const ticketId = '#TKT-' + String(Math.floor(Math.random()*90000)+10000).padStart(5,'0');

        const formCard = document.getElementById('contactFormCard');
        formCard.innerHTML = `
            <div style="text-align:center;padding:32px 20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(34,197,94,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                    <svg viewBox="0 0 24 24" style="width:30px;height:30px;stroke:#16a34a;fill:none;stroke-width:2.5;"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--text-dark);margin-bottom:8px;">Message Sent!</div>
                <p style="font-size:0.85rem;color:var(--text-soft);line-height:1.7;margin-bottom:6px;">
                    Your message has been received. Our team will reply to <strong>${email}</strong> within 2 hours.
                </p>
                <p style="font-size:0.78rem;color:var(--text-soft);margin-bottom:24px;">
                    Ticket ID: <strong style="color:var(--blue-500);">${ticketId}</strong> · Topic: <strong>${selectedTopic}</strong>
                </p>
                <button class="btn-secondary" onclick="resetContactForm()">Send Another Message</button>
            </div>`;

        const ticketList = document.querySelector('.ticket-item:last-of-type');
        if (ticketList) {
            const newTicket = document.createElement('div');
            newTicket.className = 'ticket-item';
            newTicket.style.cssText = 'animation:fadeIn 0.4s ease both;';
            newTicket.innerHTML = `
                <div>
                    <div class="ticket-subject">${subject}</div>
                    <div class="ticket-meta">Submitted just now · <span class="ticket-num">${ticketId}</span></div>
                </div>
                <span class="badge badge-gold" style="margin-left:auto;">Open</span>`;
            ticketList.after(newTicket);
        }
        showToast('Message sent! Ticket ' + ticketId + ' created.');
    }, 900);
}

function resetContactForm() {
    const card = document.getElementById('contactFormCard');
    card.innerHTML = `
        <div class="card-title">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--blue-500);fill:none;stroke-width:2;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Send Us a Message
        </div>
        <p style="font-size:0.84rem;color:var(--text-soft);margin-bottom:16px;">Tell us what you need and we'll get back to you within 2 hours.</p>
        <div style="margin-bottom:14px;">
            <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-mid);margin-bottom:8px;">Topic</div>
            <div class="ticket-types">
                ${['Booking Inquiry','Payment Issue','Room Request','Feedback','Other'].map((t,i)=>`<button class="ticket-type${i===0?' selected':''}" onclick="selectTicketType(this)">${t}</button>`).join('')}
            </div>
        </div>
        <div class="form-grid" style="margin-bottom:14px;">
            <div class="form-field"><label>Full Name</label><input type="text" id="contact_name" value="<?php echo $full_name; ?>"></div>
            <div class="form-field"><label>Email</label><input type="email" id="contact_email" value="<?php echo $email; ?>"></div>
        </div>
        <div class="form-grid cols-1" style="margin-bottom:14px;">
            <div class="form-field"><label>Subject</label><input type="text" id="contact_subject" placeholder="Brief description of your concern"></div>
        </div>
        <div class="form-field" style="margin-bottom:18px;">
            <label>Message</label>
            <textarea id="contact_message" placeholder="Describe your concern in detail. Include your booking ID if applicable."></textarea>
        </div>
        <div id="contactError" style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;"></div>
        <button class="btn-primary" id="sendMsgBtn" onclick="submitTicket()">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Send Message
        </button>`;
}

document.querySelectorAll('.contact-cta').forEach(btn => {
    const text = btn.textContent.trim();
    if (text.startsWith('Call')) {
        btn.addEventListener('click', () => { window.location.href = 'tel:+63331234567'; });
    } else if (text.startsWith('Send')) {
        btn.addEventListener('click', () => {
            document.getElementById('contactFormCard').scrollIntoView({ behavior:'smooth', block:'start' });
        });
    } else if (text.startsWith('Start')) {
        btn.addEventListener('click', () => {
            btn.disabled = true; btn.textContent = 'Connecting…';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = 'Start Chat <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;display:inline;"><polyline points="9 18 15 12 9 6"/></svg>';
                showToast('Live chat is currently unavailable. Please send us an email.');
            }, 1200);
        });
    }
});

document.querySelectorAll('.ticket-item').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', () => {
        const subject = item.querySelector('.ticket-subject')?.textContent || '';
        const id      = item.querySelector('.ticket-num')?.textContent || '';
        showToast('Opened ticket ' + id + ': ' + subject);
    });
});
</script>

<?php require '../../includes/_layout_end.php'; ?>