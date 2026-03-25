<?php
$page_title  = 'Staff / Admin Roles';
$active_page = 'staff_roles';
include '../../includes/session.php';
include '../../includes/db.php';
include '../../includes/layout_open.php';

if ($_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$search = trim($_GET['search'] ?? '');

// ── Staff query ───────────────────────────────────────────
$where = "WHERE u.role != 'user'";
if ($search !== '') {
    $s     = mysqli_real_escape_string($conn, $search);
    $where .= " AND (u.first_name LIKE '%$s%' OR u.last_name LIKE '%$s%' OR u.email LIKE '%$s%')";
}

$sql = "
    SELECT
        u.user_id,
        u.first_name, u.last_name, u.email, u.phone,
        u.role, u.created_at,
        COALESCE(u.is_active, 1) AS is_active,
        u.last_login
    FROM users u
    $where
    ORDER BY FIELD(u.role,'admin','manager','frontdesk','accounting','maintenance'), u.first_name
";
$res   = mysqli_query($conn, $sql);
$staff = [];
while ($row = mysqli_fetch_assoc($res)) $staff[] = $row;

// ── Stats ─────────────────────────────────────────────────
$counts = ['admin'=>0,'manager'=>0,'frontdesk'=>0,'accounting'=>0,'maintenance'=>0,'total'=>0];
foreach ($staff as $s) {
    $counts['total']++;
    $r = strtolower($s['role']);
    if (isset($counts[$r])) $counts[$r]++;
}

// ── Role definitions ──────────────────────────────────────
$role_defs = [
    'admin'       => ['Super Admin',      'Full access to all modules',                '#0f2744'],
    'manager'     => ['Property Manager', 'Properties, bookings, reports',             '#1d4ed8'],
    'frontdesk'   => ['Front Desk',       'Check-in/out, reservations',                '#059669'],
    'accounting'  => ['Accounting',       'Financial, invoices, reports',              '#b45309'],
    'maintenance' => ['Maintenance',      'Units, amenities, maintenance tickets',     '#6b7280'],
];

function roleLabel($role) {
    global $role_defs;
    return $role_defs[strtolower($role)][0] ?? ucfirst($role);
}
function lastActiveLabel($lastLogin) {
    if (!$lastLogin) return 'Never';
    $diff = time() - strtotime($lastLogin);
    if ($diff < 60)      return 'Just now';
    if ($diff < 3600)    return round($diff/60) . ' min ago';
    if ($diff < 86400)   return round($diff/3600) . ' hr' . (round($diff/3600)>1?'s':'') . ' ago';
    if ($diff < 604800)  return round($diff/86400) . ' day' . (round($diff/86400)>1?'s':'') . ' ago';
    return date('M j, Y', strtotime($lastLogin));
}
?>

<link rel="stylesheet" href="../../assets/css/admin-css/staff_roles.css">

<div class="page-header">
    <div class="top-header">
        <h2>Staff &amp; Admin Roles</h2>
        <div class="page-header-sub">Manage team members and their access permissions</div>
    </div>
    <button class="btn btn-primary" onclick="openInvite()">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Invite Staff
    </button>
</div>

<div class="page-inner">
    <div class="cards-area">

        <!-- Stats -->
        <div class="stat-row">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Staff</div>
                    <div class="stat-value"><?= $counts['total'] ?></div>
                </div>
                <div class="stat-icon-wrap blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Admins</div>
                    <div class="stat-value"><?= $counts['admin'] ?></div>
                </div>
                <div class="stat-icon-wrap gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Managers</div>
                    <div class="stat-value"><?= $counts['manager'] ?></div>
                </div>
                <div class="stat-icon-wrap green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Field Staff</div>
                    <div class="stat-value"><?= $counts['frontdesk'] + $counts['maintenance'] ?></div>
                </div>
                <div class="stat-icon-wrap red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="two-col">

            <!-- Staff table -->
            <div class="card" style="flex:2;">
                <div class="card-header">
                    <span class="card-title">Team Members</span>
                    <form method="GET" style="display:contents;">
                        <div class="search-wrap">
                            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="search"
                                   value="<?= htmlspecialchars($search) ?>"
                                   placeholder="Search staff…"
                                   oninput="clearTimeout(st2);st2=setTimeout(()=>this.form.submit(),450)">
                        </div>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Last Active</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($staff)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">No staff found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($staff as $s):
                                $fullName = htmlspecialchars(trim($s['first_name'].' '.$s['last_name']));
                                $initials = strtoupper(substr($s['first_name'],0,1));
                                $roleCls  = 'role-'.strtolower($s['role']);
                                $isActive = (int)$s['is_active'];
                            ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="staff-avatar"><?= $initials ?></div>
                                        <div>
                                            <div style="font-weight:700;font-size:0.84rem;"><?= $fullName ?></div>
                                            <div style="font-size:0.72rem;color:#94a3b8;"><?= htmlspecialchars($s['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="role-pill <?= $roleCls ?>"><?= roleLabel($s['role']) ?></span></td>
                                <td style="font-size:0.78rem;color:#94a3b8;"><?= lastActiveLabel($s['last_login']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $isActive ? 'success' : 'gray' ?>">
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-wrap">
                                        <button class="tbl-btn"
                                            onclick="toggleActive(<?= $s['user_id'] ?>, '<?= $fullName ?>', <?= $isActive ?>)">
                                            <?= $isActive ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                        <?php if ($s['user_id'] != $_SESSION['user_id']): ?>
                                        <button class="tbl-btn danger"
                                            onclick="removeStaff(<?= $s['user_id'] ?>, '<?= $fullName ?>')">
                                            Remove
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div style="padding:10px 20px;font-size:0.75rem;color:#94a3b8;border-top:1px solid #f1f5f9;">
                    <?= count($staff) ?> team member<?= count($staff) !== 1 ? 's' : '' ?>
                    <?= $search ? '· search: <strong>'.htmlspecialchars($search).'</strong>' : '' ?>
                </div>
            </div>

            <!-- Roles card -->
            <div class="card" style="flex:1;">
                <div class="card-header"><span class="card-title">Roles &amp; Access</span></div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <?php foreach ($role_defs as $roleKey => [$roleName, $roleDesc, $roleColor]):
                        $cnt = $counts[$roleKey] ?? 0;
                    ?>
                    <div class="role-card">
                        <div class="role-card-dot" style="background:<?= $roleColor ?>;"></div>
                        <div class="role-card-body">
                            <div class="role-card-name"><?= $roleName ?></div>
                            <div class="role-card-desc"><?= $roleDesc ?></div>
                        </div>
                        <div class="role-card-count"><?= $cnt ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let st2;

function openInvite()  { document.getElementById('inviteOverlay').classList.add('open'); }
function closeInvite() { document.getElementById('inviteOverlay').classList.remove('open'); }
document.getElementById('inviteOverlay').addEventListener('click', e => {
    if (e.target === document.getElementById('inviteOverlay')) closeInvite();
});

function submitInvite() {
    const first    = document.getElementById('invFirst').value.trim();
    const last     = document.getElementById('invLast').value.trim();
    const email    = document.getElementById('invEmail').value.trim();
    const role     = document.getElementById('invRole').value;
    const password = document.getElementById('invPassword').value;

    if (!first || !last || !email || !password) {
        Swal.fire({ icon:'warning', title:'Missing Fields', text:'Please fill in all required fields.' });
        return;
    }
    if (password.length < 8) {
        Swal.fire({ icon:'warning', title:'Weak Password', text:'Password must be at least 8 characters.' });
        return;
    }

    Swal.fire({ title:'Creating account…', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

    fetch('../../process/admin-process/add_staff.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action:'invite', first_name:first, last_name:last, email, role, password })
    })
    .then(r => r.json())
    .then(data => {
        closeInvite();
        if (data.success) {
            Swal.fire({ icon:'success', title:'Account Created!', text:data.message, timer:1600, showConfirmButton:false })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon:'error', title:'Failed', text:data.message });
        }
    })
    .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Server unreachable.' }));
}

function toggleActive(userId, name, current) {
    const activate = current == 0;
    Swal.fire({
        title: `${activate ? 'Activate' : 'Deactivate'} ${name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: activate ? '#16a34a' : '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${activate ? 'activate' : 'deactivate'}`,
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Updating…', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        fetch('../../process/admin-process/staff_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: activate ? 'activate' : 'deactivate', user_id: userId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon:'success', title:'Done!', text:data.message, timer:1400, showConfirmButton:false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon:'error', title:'Failed', text:data.message });
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Server unreachable.' }));
    });
}

function removeStaff(userId, name) {
    Swal.fire({
        title: `Remove ${name}?`,
        text: 'This will permanently remove this staff member.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove',
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Removing…', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        fetch('../../process/admin-process/staff_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'remove', user_id: userId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon:'success', title:'Removed!', text:data.message, timer:1400, showConfirmButton:false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon:'error', title:'Failed', text:data.message });
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Server unreachable.' }));
    });
}
</script>

<?php include '../../includes/layout_close.php'; ?>