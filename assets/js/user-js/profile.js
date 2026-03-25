// ── MODAL HELPERS ──────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }

// ── EDIT PROFILE ──────────────────────────────────
function openEditModal()  { openModal('editModal'); }
function closeEditModal() { closeModal('editModal'); }

function saveProfile() {
    const first = document.getElementById('edit_first').value.trim();
    const last  = document.getElementById('edit_last').value.trim();
    const email = document.getElementById('edit_email').value.trim();
    const phone = document.getElementById('edit_phone').value.trim();
    const errEl = document.getElementById('editError');
    errEl.style.display = 'none';

    if (!first) { errEl.textContent='First name is required.'; errEl.style.display='block'; return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { errEl.textContent='Please enter a valid email.'; errEl.style.display='block'; return; }

    const btn = document.getElementById('saveProfileBtn');
    btn.disabled = true; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;animation:spin 0.8s linear infinite;"><polyline points="20 6 9 17 4 12"/></svg> Saving…';

    setTimeout(() => {
        // Update displayed values live
        document.querySelector('.avatar-info h2').textContent = first + ' ' + last;
        document.querySelector('.avatar-info p').textContent  = email;
        document.querySelectorAll('.info-row')[0].querySelector('.info-value').textContent = first;
        document.querySelectorAll('.info-row')[1].querySelector('.info-value').textContent = last || '—';
        document.querySelectorAll('.info-row')[2].querySelector('.info-value').textContent = email;
        document.querySelectorAll('.info-row')[3].querySelector('.info-value').textContent = phone;
        closeEditModal();
        btn.disabled = false; btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><polyline points="20 6 9 17 4 12"/></svg> Save Changes';
        showToast('Profile updated successfully!');
    }, 700);
}

// ── UPLOAD ID ──────────────────────────────────────
function handleFileSelect(input) {
    if (input.files[0]) {
        document.getElementById('dropzoneLabel').textContent = input.files[0].name;
        const btn = document.getElementById('uploadIdBtn');
        btn.disabled = false; btn.style.opacity = '1';
    }
}
function handleFileDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('dropzoneLabel').textContent = file.name;
        const btn = document.getElementById('uploadIdBtn');
        btn.disabled = false; btn.style.opacity = '1';
        document.getElementById('dropzone').style.borderColor = 'var(--blue-200)';
        document.getElementById('dropzone').style.background  = '';
    }
}
function submitUpload() {
    const btn = document.getElementById('uploadIdBtn');
    btn.disabled = true; btn.innerHTML = 'Uploading…';
    setTimeout(() => {
        closeModal('uploadIdModal');
        btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;"><polyline points="20 6 9 17 4 12"/></svg> Upload ID';
        document.getElementById('dropzoneLabel').textContent = 'No file selected';
        btn.style.opacity = '0.5'; btn.disabled = true;
        showToast('ID document uploaded successfully!');
    }, 900);
}

// ── DELETE ACCOUNT ─────────────────────────────────
function confirmDelete() {
    showToast('Account deletion request submitted.', true);
    closeModal('deleteModal');
    setTimeout(() => { window.location.href = 'logout.php'; }, 2000);
}

// Close modals on backdrop click
['editModal','uploadIdModal','deleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', e => { if(e.target.id===id) closeModal(id); });
});
document.addEventListener('keydown', e => {
    if (e.key==='Escape') { ['editModal','uploadIdModal','deleteModal'].forEach(closeModal); closeSidebar(); }
});

// Wire Upload New ID button
document.querySelector('.card.reveal.rd2 .btn-secondary').addEventListener('click', () => openModal('uploadIdModal'));

// Wire Delete Account button
document.querySelector('.btn-danger').addEventListener('click', () => {
    document.getElementById('deleteConfirmInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
    document.getElementById('confirmDeleteBtn').style.opacity = '0.5';
    openModal('deleteModal');
});