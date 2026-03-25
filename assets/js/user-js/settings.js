// ── SECTION SWITCHING ──────────────────────────────
function showSection(id, el) {
    event.preventDefault();
    document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sn-item').forEach(i => i.classList.remove('active'));
    document.getElementById('sec-' + id).classList.add('active');
    el.classList.add('active');
}

// ── SAVE PREFERENCES (notifications + privacy) ─────
function savePreferences(btn) {
    const orig = btn.innerHTML;
    btn.disabled = true; btn.textContent = 'Saving…';
    setTimeout(() => {
        btn.disabled = false; btn.innerHTML = orig;
        showToast('Preferences saved successfully!');
    }, 600);
}
// Wire all "Save Preferences" buttons
document.querySelectorAll('.btn-primary').forEach(btn => {
    if (btn.textContent.includes('Save Preferences') || btn.textContent.includes('Save Language')) {
        btn.addEventListener('click', () => savePreferences(btn));
    }
});

// ── UPDATE PASSWORD ────────────────────────────────
function updatePassword() {
    const current = document.querySelector('#sec-security input[type="password"]:nth-of-type(1)').value;
    const newPw = document.getElementById('newPw').value;
    const confirm = document.querySelector('#sec-security .form-grid input[type="password"]:last-child').value;

    // Remove old errors
    document.querySelectorAll('.pw-error').forEach(e => e.remove());

    if (!current) { showPwError('Current password is required.'); return; }
    if (newPw.length < 8) { showPwError('New password must be at least 8 characters.'); return; }
    if (newPw !== confirm) { showPwError('Passwords do not match.'); return; }

    const btn = document.querySelector('#sec-security .btn-primary');
    btn.disabled = true; btn.textContent = 'Updating…';
    setTimeout(() => {
        // Clear fields
        document.querySelectorAll('#sec-security input[type="password"]').forEach(i => i.value = '');
        document.getElementById('pwBar').style.width = '0';
        document.getElementById('pwHint').textContent = '';
        btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><polyline points="20 6 9 17 4 12"/></svg> Update Password';
        showToast('Password updated successfully!');
    }, 700);
}
function showPwError(msg) {
    const div = document.createElement('div');
    div.className = 'pw-error';
    div.style.cssText = 'color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;';
    div.textContent = msg;
    document.querySelector('#sec-security .btn-primary').before(div);
}
// Wire Update Password button
document.querySelector('#sec-security .btn-primary').addEventListener('click', updatePassword);

// ── PASSWORD STRENGTH ──────────────────────────────
function checkStrength(v) {
    const bar = document.getElementById('pwBar');
    const hint = document.getElementById('pwHint');
    if (!v) { bar.style.width = '0'; hint.textContent = ''; return; }
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^a-zA-Z0-9]/.test(v)) score++;
    const map = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    const col = ['', '#ef4444', '#f59e0b', '#3b82f6', '#16a34a'];
    bar.style.width = (score * 25) + '%';
    bar.style.background = col[score];
    hint.textContent = map[score];
    hint.style.color = col[score];
}

// ── REVOKE SESSION ─────────────────────────────────
document.querySelectorAll('#sec-sessions .btn-danger').forEach(btn => {
    if (btn.textContent.trim() === 'Revoke') {
        btn.addEventListener('click', function () {
            const item = this.closest('.session-item');
            this.disabled = true; this.textContent = 'Revoking…';
            setTimeout(() => {
                item.style.transition = 'opacity 0.3s, transform 0.3s';
                item.style.opacity = '0'; item.style.transform = 'translateX(8px)';
                setTimeout(() => { item.remove(); showToast('Session revoked successfully.'); }, 300);
            }, 500);
        });
    }
});

// Sign Out All Devices
document.querySelector('#sec-sessions .btn-danger:last-of-type').addEventListener('click', function () {
    this.disabled = true; this.textContent = 'Signing out…';
    setTimeout(() => {
        this.disabled = false; this.textContent = 'Sign Out All Other Devices';
        showToast('All other sessions signed out.');
    }, 800);
});

// ── REQUEST MY DATA ────────────────────────────────
document.querySelector('#sec-privacy .btn-secondary').addEventListener('click', function () {
    this.disabled = true; this.textContent = 'Requesting…';
    setTimeout(() => {
        this.disabled = false; this.textContent = 'Request My Data';
        showToast('Your data export will be sent to your email within 24 hours.');
    }, 900);
});

// ── LANGUAGE ───────────────────────────────────────
function selectLang(el) {
    document.querySelectorAll('.lang-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
}