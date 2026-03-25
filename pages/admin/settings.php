<?php
$page_title = 'Settings';
$active_page = 'settings';
include '../../includes/session.php';
include '../../includes/layout_open.php';
?>
<div class="page-header">
    <div class="top-header">
        <h2>Settings</h2>
        <div class="page-header-sub">Manage your account, system preferences, and integrations</div>
    </div>
</div>

<div class="page-inner">
    <div class="cards-area">

        <div class="two-col">

            <!-- Profile Settings -->
            <div class="card">
                <div class="card-header"><span class="card-title">Profile Information</span></div>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div
                        style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--blue-300),var(--blue-700));display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:white;flex-shrink:0;">
                        MJ</div>
                    <div>
                        <div style="font-size:16px;font-weight:700;">Myra Jonson</div>
                        <div style="font-size:12px;color:var(--text-soft);">Property Manager · Super Admin</div>
                        <button class="btn btn-secondary" style="margin-top:8px;padding:5px 12px;font-size:12px;">Change
                            Photo</button>
                    </div>
                </div>
                <form>
                    <div class="form-grid">
                        <div class="form-group"><label>First Name</label><input type="text" value="Myra" /></div>
                        <div class="form-group"><label>Last Name</label><input type="text" value="Jonson" /></div>
                        <div class="form-group"><label>Email</label><input type="email" value="myra@propmanager.com" />
                        </div>
                        <div class="form-group"><label>Phone</label><input type="tel" value="+63 917 123 4567" /></div>
                        <div class="form-group full"><label>Address</label><input type="text"
                                value="123 Management Ave, Makati City" /></div>
                    </div>
                    <div class="form-actions" style="margin-top:16px;"><button type="submit"
                            class="btn btn-primary">Save Changes</button></div>
                </form>
            </div>

            <!-- Security -->
            <div class="card">
                <div class="card-header"><span class="card-title">Security</span></div>
                <form>
                    <div class="form-grid" style="grid-template-columns:1fr;">
                        <div class="form-group"><label>Current Password</label><input type="password"
                                placeholder="••••••••" /></div>
                        <div class="form-group"><label>New Password</label><input type="password"
                                placeholder="••••••••" /></div>
                        <div class="form-group"><label>Confirm New Password</label><input type="password"
                                placeholder="••••••••" /></div>
                    </div>
                    <div class="form-actions" style="margin-top:16px;"><button type="submit"
                            class="btn btn-primary">Update Password</button></div>
                </form>
                <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--border);">
                    <div style="font-size:14px;font-weight:700;margin-bottom:12px;">Two-Factor Authentication</div>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:var(--gray-light);border-radius:var(--radius);">
                        <div>
                            <div style="font-size:13.5px;font-weight:600;">Authenticator App</div>
                            <div style="font-size:11px;color:var(--text-soft);">Use Google Authenticator or similar
                            </div>
                        </div>
                        <span class="badge badge-success">Enabled</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Preferences -->
        <div class="card">
            <div class="card-header"><span class="card-title">System Preferences</span></div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
                <?php
                $prefs = [
                    ['Default Currency', 'PHP (₱ Philippine Peso)', 'select', ['PHP (₱)', 'USD ($)', 'EUR (€)']],
                    ['Date Format', 'MM/DD/YYYY', 'select', ['MM/DD/YYYY', 'DD/MM/YYYY', 'YYYY-MM-DD']],
                    ['Time Zone', 'Asia/Manila (UTC+8)', 'select', ['Asia/Manila', 'Asia/Singapore', 'UTC']],
                    ['Language', 'English', 'select', ['English', 'Filipino', 'Español']],
                ];
                foreach ($prefs as $pref): ?>
                    <div class="form-group">
                        <label><?= $pref[0] ?></label>
                        <select>
                            <?php foreach ($pref[3] as $opt): ?>
                                <option <?= $opt === $pref[1] ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="card">
            <div class="card-header"><span class="card-title">Notification Preferences</span></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php
                $notifs = [
                    ['New Reservation', 'Get notified when a new booking is made', true],
                    ['Check-in Reminders', 'Alert 1 hour before guest check-in', true],
                    ['Payment Received', 'Notify when a payment is confirmed', true],
                    ['Maintenance Requests', 'Alert when a maintenance task is filed', true],
                    ['Monthly Reports', 'Receive auto-generated monthly reports', false],
                    ['Low Occupancy Alerts', 'Notify when occupancy drops below 50%', false],
                ];
                foreach ($notifs as $n): ?>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:var(--gray-light);border-radius:var(--radius);">
                        <div>
                            <div style="font-size:13.5px;font-weight:600;"><?= $n[0] ?></div>
                            <div style="font-size:11px;color:var(--text-soft);"><?= $n[1] ?></div>
                        </div>
                        <div
                            style="width:40px;height:22px;border-radius:20px;background:<?= $n[2] ? 'var(--blue-400)' : 'var(--border)' ?>;position:relative;cursor:pointer;transition:background .2s;">
                            <div
                                style="width:16px;height:16px;border-radius:50%;background:white;position:absolute;top:3px;left:<?= $n[2] ? '21px' : '3px' ?>;transition:left .2s;">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
<?php include '../../includes/layout_close.php'; ?>