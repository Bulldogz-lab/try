function toggleFaq(id) {
    const item = document.getElementById(id);
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
    const name = document.getElementById('contact_name').value.trim();
    const email = document.getElementById('contact_email').value.trim();
    const subject = document.getElementById('contact_subject').value.trim();
    const message = document.getElementById('contact_message').value.trim();
    const errEl = document.getElementById('contactError');
    errEl.style.display = 'none';

    if (!name) { errEl.textContent = 'Full name is required.'; errEl.style.display = 'block'; return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { errEl.textContent = 'Please enter a valid email address.'; errEl.style.display = 'block'; return; }
    if (!subject) { errEl.textContent = 'Subject is required.'; errEl.style.display = 'block'; return; }
    if (message.length < 10) { errEl.textContent = 'Please write a more detailed message (at least 10 characters).'; errEl.style.display = 'block'; return; }

    const selectedTopic = document.querySelector('.ticket-type.selected')?.textContent || 'General';
    const btn = document.getElementById('sendMsgBtn');
    btn.disabled = true; btn.textContent = 'Sending…';

    setTimeout(() => {
        const ticketId = '#TKT-' + String(Math.floor(Math.random() * 90000) + 10000).padStart(5, '0');

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
                ${['Booking Inquiry', 'Payment Issue', 'Room Request', 'Feedback', 'Other'].map((t, i) => `<button class="ticket-type${i === 0 ? ' selected' : ''}" onclick="selectTicketType(this)">${t}</button>`).join('')}
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
            document.getElementById('contactFormCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
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
        const id = item.querySelector('.ticket-num')?.textContent || '';
        showToast('Opened ticket ' + id + ': ' + subject);
    });
});