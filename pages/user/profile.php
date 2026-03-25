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
$phone      = htmlspecialchars($_SESSION['phone'] ?? '');
$nationality = htmlspecialchars($_SESSION['nationality'] ?? '');
$birthday   = htmlspecialchars($_SESSION['birthday'] ?? '');
$gender     = htmlspecialchars($_SESSION['gender'] ?? '');
$initials   = strtoupper(mb_substr($first_name,0,1) . mb_substr($last_name,0,1));
$hour       = (int)date('G');
$greeting   = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$page_title     = 'View Profile';
$page_hero_html = 'My <em>Profile</em>';
$page_hero_sub  = 'Manage your personal details and account information.';
$page_hero_icon = '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>';
$active_nav     = 'profile';
require '../../includes/_layout.php';
?>

<link rel="stylesheet" href="../../assets/css/user-css/profile.css">

<div class="stat-strip">
    <div class="stat-box">
        <div class="stat-num">7</div>
        <div class="stat-lbl">Total Stays</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">1,240</div>
        <div class="stat-lbl">Loyalty Points</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">Gold</div>
        <div class="stat-lbl">Membership Tier</div>
    </div>
</div>

<div class="card reveal rd1">
    <div class="avatar-block">
        <div class="avatar-circle">
            <?php echo $initials; ?>
            <div class="avatar-edit-btn" title="Change photo">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
        </div>
        <div class="avatar-info">
            <h2><?php echo $full_name; ?></h2>
            <p><?php echo $email; ?></p>
            <div class="avatar-since">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Member since January 2024
            </div>
        </div>
    </div>

    <div class="card-title">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Personal Information
        <button class="btn-secondary" style="margin-left:auto;font-size:0.72rem;padding:7px 16px;" onclick="openEditModal()">Edit Profile</button>
    </div>

    <div class="info-row">
        <span class="info-label">First Name</span>
        <span class="info-value"><?php echo $first_name; ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Last Name</span>
        <span class="info-value"><?php echo $last_name ?: '—'; ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Email</span>
        <span class="info-value"><?php echo $email; ?></span>
        <span class="badge badge-green">Verified</span>
    </div>
    <div class="info-row">
        <span class="info-label">Phone</span>
        <span class="info-value"><?php echo $phone; ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Nationality</span>
        <span class="info-value"><?php echo $nationality; ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Date of Birth</span>
        <span class="info-value"><?php echo $birthday; ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Gender</span>
        <span class="info-value"><?php echo $gender; ?></span>
    </div>
</div>

<div class="card reveal rd2">
    <div class="card-title">
        <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        Identity Verification
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <p style="font-size:0.88rem;color:var(--text-mid);margin-bottom:4px;">Government ID · <strong>Philippine Passport</strong></p>
            <p style="font-size:0.78rem;color:var(--text-soft);">Submitted Jan 10, 2024 · Expires Mar 2029</p>
        </div>
        <span class="badge badge-green">✓ Verified</span>
    </div>
    <div class="card-section-divider"></div>
    <button class="btn-secondary">Upload New ID</button>
</div>

<div class="card reveal rd3" style="border-color:#fecaca;">
    <div class="card-title" style="color:#dc2626;">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Danger Zone
    </div>
    <p style="font-size:0.85rem;color:var(--text-soft);margin-bottom:16px;">Permanently delete your account and all associated data. This action cannot be undone.</p>
    <button class="btn-danger">Delete My Account</button>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <form action="../../process/user-process/edit-profile.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="edit_profile" value="1">

            <button type="button" class="modal-close-btn" onclick="closeEditModal()">✕</button>
            
            <div class="modal-title">Edit Profile</div>
            <div class="modal-sub">Update your personal information below.</div>

            <div class="form-grid" style="margin-bottom:14px;">
                <div class="form-field">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="form-field">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
            </div>

            <div class="form-grid cols-1" style="margin-bottom:14px;">
                <div class="form-field">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
            </div>

            <div class="form-grid" style="margin-bottom:14px;">
                <div class="form-field">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                <div class="form-field">
                    <label>Nationality</label>
                    <input type="text" name="nationality" value="<?php echo htmlspecialchars($nationality); ?>">
                </div>
            </div>

            <div class="form-grid" style="margin-bottom:22px;">
                <div class="form-field">
                    <label>Date of Birth</label>
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>">
                </div>
                <div class="form-field">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Prefer not to say" <?php echo $gender === 'Prefer not to say' ? 'selected' : ''; ?>>Prefer not to say</option>
                    </select>
                </div>
            </div>

            <div id="editError" style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:12px;"></div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="uploadIdModal">
    <div class="modal-box" style="max-width:440px;">
        <button class="modal-close-btn" onclick="closeModal('uploadIdModal')">✕</button>
        <div class="modal-title">Upload New ID</div>
        <div class="modal-sub">Accepted formats: JPG, PNG, PDF · Max 5MB</div>
        <div id="dropzone" style="border:2px dashed var(--blue-200);border-radius:14px;padding:36px 20px;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;margin-bottom:16px;"
            onclick="document.getElementById('idFileInput').click()"
            ondragover="event.preventDefault();this.style.borderColor='var(--blue-400)';this.style.background='var(--blue-50)'"
            ondragleave="this.style.borderColor='var(--blue-200)';this.style.background=''"
            ondrop="handleFileDrop(event)">
            <svg viewBox="0 0 24 24" style="width:36px;height:36px;stroke:var(--blue-300);fill:none;stroke-width:1.5;margin:0 auto 10px;display:block;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <p style="font-size:0.85rem;font-weight:600;color:var(--text-dark);margin-bottom:4px;">Click to browse or drag & drop</p>
            <p style="font-size:0.74rem;color:var(--text-soft);" id="dropzoneLabel">No file selected</p>
        </div>
        <input type="file" id="idFileInput" accept=".jpg,.jpeg,.png,.pdf" style="display:none" onchange="handleFileSelect(this)">
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-secondary" onclick="closeModal('uploadIdModal')">Cancel</button>
            <button class="btn-primary" id="uploadIdBtn" onclick="submitUpload()" disabled style="opacity:0.5;">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Upload ID
            </button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close-btn" onclick="closeModal('deleteModal')">✕</button>
        <div style="text-align:center;padding:10px 0 20px;">
            <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg viewBox="0 0 24 24" style="width:26px;height:26px;stroke:#ef4444;fill:none;stroke-width:2;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
            </div>
            <div class="modal-title" style="color:#dc2626;">Delete Account?</div>
            <p style="font-size:0.84rem;color:var(--text-soft);margin:8px 0 20px;line-height:1.7;">This will permanently delete your account, all bookings, and loyalty points. Type <strong>DELETE</strong> to confirm.</p>
            <input type="text" id="deleteConfirmInput" placeholder='Type "DELETE"'
                style="width:100%;padding:10px 13px;border:1.5px solid #fecaca;border-radius:var(--radius);font-family:'Jost',sans-serif;font-size:0.88rem;color:var(--text-dark);outline:none;margin-bottom:16px;"
                oninput="document.getElementById('confirmDeleteBtn').disabled=this.value!=='DELETE';document.getElementById('confirmDeleteBtn').style.opacity=this.value==='DELETE'?'1':'0.5'">
            <div style="display:flex;gap:10px;justify-content:center;">
                <button class="btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button id="confirmDeleteBtn" disabled style="opacity:0.5;font-family:'Jost',sans-serif;font-size:0.84rem;font-weight:600;background:#ef4444;color:#fff;border:none;padding:10px 22px;border-radius:40px;cursor:pointer;"
                    onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/user-js/profile.js"></script>

<?php require '../../includes/_layout_end.php'; ?>