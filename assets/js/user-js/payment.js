function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
function openAddCard() { clearCardForm(); updatePreview(); openModal('addCardModal'); }
function closeAddCard() { closeModal('addCardModal'); }

// ── LIVE CARD PREVIEW ──────────────────────────────
function updatePreview() {
    const raw = (document.getElementById('cardNumber')?.value || '').replace(/\s/g, '');
    const holder = (document.getElementById('cardHolder')?.value || '').trim();
    const expiry = (document.getElementById('cardExpiry')?.value || '').trim();
    const groups = [];
    for (let i = 0; i < 4; i++) {
        const chunk = raw.slice(i * 4, i * 4 + 4);
        groups.push(chunk.padEnd(4, '•'));
    }
    document.getElementById('previewNumber').textContent = groups.join(' ');
    document.getElementById('previewHolder').textContent = holder.toUpperCase() || 'YOUR NAME';
    document.getElementById('previewExpiry').textContent = expiry || 'MM/YY';
    const type = raw.startsWith('4') ? 'VISA' : /^5[1-5]/.test(raw) ? 'MC' : /^3[47]/.test(raw) ? 'AMEX' : 'CARD';
    document.getElementById('previewType').textContent = type;
    const colors = {
        VISA: 'linear-gradient(135deg,#0f2447,#1e50a2)',
        MC: 'linear-gradient(135deg,#7f1d1d,#b91c1c)',
        AMEX: 'linear-gradient(135deg,#064e3b,#047857)',
        CARD: 'linear-gradient(135deg,var(--blue-800),var(--blue-500))'
    };
    document.getElementById('cardPreview').style.background = colors[type];
}

// ── AUTO-FORMAT ────────────────────────────────────
function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
}
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 4);
    if (v.length >= 3) v = v.slice(0, 2) + ' / ' + v.slice(2);
    input.value = v;
}

// ── CLEAR FORM ─────────────────────────────────────
function clearCardForm() {
    const fields = { cardNumber: '', cardExpiry: '', cardCvv: '', cardHolder: '' };
    Object.keys(fields).forEach(id => { const el = document.getElementById(id); if (el) el.value = fields[id]; });
    const err = document.getElementById('cardError');
    if (err) err.style.display = 'none';
}

// ── VALIDATE & SAVE ────────────────────────────────
function saveCard() {
    const num = (document.getElementById('cardNumber').value || '').replace(/\s/g, '');
    const expiry = (document.getElementById('cardExpiry').value || '').trim();
    const cvv = (document.getElementById('cardCvv').value || '').trim();
    const holder = (document.getElementById('cardHolder').value || '').trim();
    const errEl = document.getElementById('cardError');
    errEl.style.display = 'none';

    if (num.length !== 16 || !/^\d+$/.test(num)) { showCardError('Please enter a valid 16-digit card number.'); return; }
    if (!/^\d{2}\s*\/\s*\d{2}$/.test(expiry)) { showCardError('Please enter expiry as MM / YY.'); return; }
    if (cvv.length < 3 || !/^\d+$/.test(cvv)) { showCardError('CVV must be 3–4 digits.'); return; }
    if (!holder) { showCardError('Cardholder name is required.'); return; }

    const [mm, yy] = expiry.replace(/\s/g, '').split('/');
    if (new Date(2000 + parseInt(yy), parseInt(mm) - 1, 1) < new Date()) { showCardError('This card has expired.'); return; }

    const btn = document.getElementById('saveCardBtn');
    btn.disabled = true; btn.textContent = 'Saving…';

    setTimeout(() => {
        const last4 = num.slice(-4);
        const type = num.startsWith('4') ? 'Visa' : /^5[1-5]/.test(num) ? 'Mastercard' : 'Card';
        const bg = num.startsWith('4') ? 'linear-gradient(135deg,#0f2447,#1e50a2)' :
            /^5[1-5]/.test(num) ? 'linear-gradient(135deg,#7f1d1d,#b91c1c)' :
                'linear-gradient(135deg,#153060,#1e50a2)';
        const grid = document.querySelector('.cards-list');
        const newWrap = document.createElement('div');
        newWrap.className = 'card-item-wrap';
        newWrap.innerHTML = `
            <div class="card-visual" style="background:${bg}">
                <div><div class="cv-chip"></div><div class="cv-number">•••• •••• •••• ${last4}</div></div>
                <div class="cv-footer">
                    <div><div class="cv-label">Card Holder</div><div class="cv-value">${holder.toUpperCase()}</div></div>
                    <div style="text-align:right;"><div class="cv-label">Expires</div><div class="cv-value">${expiry.replace(/\s/g, '')}</div></div>
                    <div class="cv-type">${type}</div>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn-secondary" style="font-size:0.72rem;padding:7px 14px;">Set Default</button>
                <button class="btn-danger"    style="font-size:0.72rem;padding:7px 14px;">Remove</button>
            </div>`;
        newWrap.querySelector('.btn-secondary').addEventListener('click', function () {
            document.querySelectorAll('.cv-default-badge').forEach(b => b.remove());
            const badge = Object.assign(document.createElement('div'), { className: 'cv-default-badge', textContent: 'Default' });
            newWrap.querySelector('.card-visual').appendChild(badge);
            this.remove(); showToast('Default payment method updated.');
        });
        newWrap.querySelector('.btn-danger').addEventListener('click', function () {
            if (newWrap.querySelector('.cv-default-badge')) { showToast('Cannot remove your default card.', true); return; }
            newWrap.style.transition = 'opacity 0.3s,transform 0.3s'; newWrap.style.opacity = '0'; newWrap.style.transform = 'scale(0.95)';
            setTimeout(() => { newWrap.remove(); showToast('Card removed.'); }, 300);
        });
        grid.appendChild(newWrap);
        closeAddCard();
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Save Card';
        showToast(`${type} ending in ${last4} added!`);
    }, 800);
}

function showCardError(msg) {
    const el = document.getElementById('cardError');
    el.textContent = msg; el.style.display = 'block';
}

// ── SET DEFAULT CARD ───────────────────────────────
document.querySelectorAll('.card-actions .btn-secondary').forEach(btn => {
    if (btn.textContent.trim() === 'Set Default') {
        btn.addEventListener('click', function () {
            this.disabled = true; this.textContent = 'Setting…';
            setTimeout(() => {
                document.querySelectorAll('.cv-default-badge').forEach(b => b.remove());
                const cardVisual = this.closest('.card-item-wrap').querySelector('.card-visual');
                const badge = Object.assign(document.createElement('div'), { className: 'cv-default-badge', textContent: 'Default' });
                cardVisual.appendChild(badge);
                this.remove(); showToast('Default payment method updated.');
            }, 500);
        });
    }
});

// ── REMOVE CARD ────────────────────────────────────
document.querySelectorAll('.card-actions .btn-danger').forEach(btn => {
    btn.addEventListener('click', function () {
        const wrap = this.closest('.card-item-wrap');
        if (wrap.querySelector('.cv-default-badge')) { showToast('Cannot remove your default card. Set another as default first.', true); return; }
        this.disabled = true; this.textContent = 'Removing…';
        setTimeout(() => {
            wrap.style.transition = 'opacity 0.3s,transform 0.3s'; wrap.style.opacity = '0'; wrap.style.transform = 'scale(0.95)';
            setTimeout(() => { wrap.remove(); showToast('Card removed.'); }, 300);
        }, 400);
    });
});

// ── LINK EWALLET ───────────────────────────────────
document.querySelectorAll('.ewallet-item .btn-secondary').forEach(btn => {
    btn.addEventListener('click', function () {
        const name = this.closest('.ewallet-item').querySelector('.ewallet-name').textContent;
        this.disabled = true; this.textContent = 'Linking…';
        setTimeout(() => {
            this.closest('.ewallet-item').classList.add('linked');
            this.closest('.ewallet-item').querySelector('.ewallet-num').textContent = '+63 912 *** 6789';
            this.replaceWith(Object.assign(document.createElement('span'), { className: 'badge badge-green', textContent: 'Linked' }));
            showToast(name + ' linked successfully!');
        }, 800);
    });
});

// ── INVOICE DOWNLOAD ───────────────────────────────
document.querySelectorAll('.billing-table .btn-secondary').forEach((btn, i) => {
    btn.addEventListener('click', function () {
        const desc = this.closest('tr').cells[1].textContent;
        this.disabled = true; this.textContent = '…';
        setTimeout(() => { this.disabled = false; this.textContent = 'Invoice'; showToast('Invoice for "' + desc + '" downloaded!'); }, 600);
    });
});

document.getElementById('addCardModal').addEventListener('click', e => { if (e.target.id === 'addCardModal') closeAddCard(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeAddCard(); closeSidebar(); } });