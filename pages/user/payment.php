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
$last_name = htmlspecialchars($_SESSION['last_name'] ?? '');
$full_name = trim($first_name . ' ' . $last_name);
$email = htmlspecialchars($_SESSION['email'] ?? '');
$initials = strtoupper(mb_substr($first_name, 0, 1) . mb_substr($last_name, 0, 1));
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$page_title = 'Payment Methods';
$page_hero_html = 'Payment <em>Methods</em>';
$page_hero_sub = 'Manage your cards, e-wallets, and billing details securely.';
$page_hero_icon = '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>';
$active_nav = 'payment';
require '../../includes/_layout.php';

$cards = [
    ['type' => 'Visa', 'last4' => '4242', 'expiry' => '09/27', 'holder' => $full_name, 'default' => true, 'color' => 'linear-gradient(135deg,#1a3d7c,#2563c4)'],
    ['type' => 'Mastercard', 'last4' => '8371', 'expiry' => '04/26', 'holder' => $full_name, 'default' => false, 'color' => 'linear-gradient(135deg,#153060,#1e50a2)'],
];
$ewallets = [
    ['name' => 'GCash', 'icon' => '../../assets/images/gcash-icon.png', 'linked' => true, 'number' => '+63 912 *** 6789'],
    ['name' => 'Maya', 'icon' => '../../assets/images/maya-icon.png', 'linked' => true, 'number' => '+63 912 *** 6789'],
    ['name' => 'PayPal', 'icon' => '../../assets/images/paypal-icon.png', 'linked' => false, 'number' => null],
    ['name' => 'ShopeePay', 'icon' => '../../assets/images/shopeepay-icon.png', 'linked' => false, 'number' => null],
];
$bills = [
    ['date' => 'Mar 22, 2026', 'desc' => 'Roxon Residences 2nd Floor Unit 5 · 4 nights', 'amount' => '₱16,800', 'status' => 'pending', 'method' => 'Visa ••• 4242'],
    ['date' => 'Feb 10, 2026', 'desc' => 'Casa Camilla Unit 10 · 4 nights', 'amount' => '₱14,000', 'status' => 'paid', 'method' => 'Visa ••• 4242'],
    ['date' => 'Dec 25, 2025', 'desc' => 'Casa Camilla Unit 10 · 3 nights', 'amount' => '₱16,500', 'status' => 'paid', 'method' => 'GCash'],
    ['date' => 'Sep 5, 2025', 'desc' => 'Roxon Residences 2nd Floor Unit 5 · 3 nights', 'amount' => '₱11,400', 'status' => 'paid', 'method' => 'Mastercard ••• 8371'],
];
?>

<link rel="stylesheet" href="../../assets/css/user-css/payment.css" />

<div class="card reveal">
    <div class="card-title">
        <svg viewBox="0 0 24 24">
            <rect x="1" y="4" width="22" height="16" rx="2" />
            <line x1="1" y1="10" x2="23" y2="10" />
        </svg>
        Saved Cards
        <button class="btn-primary" style="margin-left:auto;font-size:0.74rem;padding:8px 18px;"
            onclick="openAddCard()">+ Add New Card</button>
    </div>
    <div class="cards-list">
        <?php foreach ($cards as $c): ?>
            <div class="card-item-wrap">
                <div class="card-visual" style="background:<?php echo $c['color']; ?>">
                    <?php if ($c['default']): ?>
                        <div class="cv-default-badge">Default</div><?php endif; ?>
                    <div>
                        <div class="cv-chip"></div>
                        <div class="cv-number">•••• •••• •••• <?php echo $c['last4']; ?></div>
                    </div>
                    <div class="cv-footer">
                        <div>
                            <div class="cv-label">Card Holder</div>
                            <div class="cv-value"><?php echo strtoupper($c['holder']); ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div class="cv-label">Expires</div>
                            <div class="cv-value"><?php echo $c['expiry']; ?></div>
                        </div>
                        <div class="cv-type"><?php echo $c['type']; ?></div>
                    </div>
                </div>
                <div class="card-actions">
                    <?php if (!$c['default']): ?>
                        <button class="btn-secondary" style="font-size:0.72rem;padding:7px 14px;"
                            onclick="showToast('Set as default payment method.')">Set Default</button>
                    <?php endif; ?>
                    <button class="btn-danger" style="font-size:0.72rem;padding:7px 14px;"
                        onclick="showToast('Card removed.')">Remove</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card reveal rd1">
    <div class="card-title">
        <svg viewBox="0 0 24 24">
            <path d="M21 12V7H5a2 2 0 010-4h14v4" />
            <path d="M3 5v14a2 2 0 002 2h16v-5" />
            <path d="M18 12a2 2 0 000 4h4v-4z" />
        </svg>
        E-Wallets
    </div>
    <div class="ewallet-grid">
        <?php foreach ($ewallets as $w): ?>
            <div class="ewallet-item <?php echo $w['linked'] ? 'linked' : ''; ?>">
                <div class="ewallet-icon"><img src="<?php echo $w['icon']; ?>" alt="<?php echo $w['name']; ?>"></div>
                <div class="ewallet-info">
                    <div class="ewallet-name"><?php echo $w['name']; ?></div>
                    <div class="ewallet-num"><?php echo $w['linked'] ? $w['number'] : 'Not linked'; ?></div>
                </div>
                <?php if ($w['linked']): ?>
                    <span class="badge badge-green">Linked</span>
                <?php else: ?>
                    <button class="btn-secondary" style="font-size:0.7rem;padding:6px 12px;white-space:nowrap;"
                        onclick="showToast('Redirecting to <?php echo $w['name']; ?>...')">Link</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card reveal rd2">
    <div class="card-title">
        <svg viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
            <polyline points="10 9 9 9 8 9" />
        </svg>
        Billing History
    </div>
    <div style="overflow-x:auto;">
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $b): ?>
                    <tr>
                        <td><?php echo $b['date']; ?></td>
                        <td><?php echo $b['desc']; ?></td>
                        <td style="font-size:0.78rem;color:var(--text-soft);"><?php echo $b['method']; ?></td>
                        <td class="bt-amount"><?php echo $b['amount']; ?></td>
                        <td><span
                                class="badge <?php echo $b['status'] === 'paid' ? 'badge-green' : ($b['status'] === 'pending' ? 'badge-gold' : 'badge-red'); ?>"><?php echo ucfirst($b['status']); ?></span>
                        </td>
                        <td><button class="btn-secondary" style="font-size:0.7rem;padding:5px 12px;"
                                onclick="showToast('Invoice downloaded.')">Invoice</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addCardModal">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close-btn" onclick="closeAddCard()">✕</button>
        <div class="modal-title">Add New Card</div>
        <div class="modal-sub">Your details are encrypted and stored securely.</div>

        <!-- Live card preview -->
        <div id="cardPreview" style="
            width:100%; aspect-ratio:1.586; border-radius:14px; margin-bottom:20px;
            background:linear-gradient(135deg,var(--blue-800),var(--blue-500));
            padding:18px 20px; display:flex; flex-direction:column;
            justify-content:space-between; position:relative; overflow:hidden;
            box-shadow:0 10px 30px rgba(10,22,40,.3);">
            <!-- bg circles -->
            <div
                style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.06);">
            </div>
            <div
                style="position:absolute;bottom:-40px;left:10px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.04);">
            </div>
            <!-- chip -->
            <div
                style="width:34px;height:26px;background:rgba(232,200,122,0.75);border-radius:4px;position:relative;z-index:1;">
                <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(0,0,0,0.2);"></div>
            </div>
            <!-- number -->
            <div id="previewNumber"
                style="font-family:'Playfair Display',serif;font-size:1.1rem;letter-spacing:0.2em;color:rgba(255,255,255,0.9);position:relative;z-index:1;text-align:center;">
                •••• •••• •••• ••••
            </div>
            <!-- footer -->
            <div style="display:flex;justify-content:space-between;align-items:flex-end;position:relative;z-index:1;">
                <div>
                    <div
                        style="font-size:0.55rem;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:3px;">
                        Card Holder</div>
                    <div id="previewHolder"
                        style="font-size:0.78rem;font-weight:600;color:rgba(255,255,255,0.9);letter-spacing:0.04em;">
                        YOUR NAME</div>
                </div>
                <div style="text-align:right;">
                    <div
                        style="font-size:0.55rem;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:3px;">
                        Expires</div>
                    <div id="previewExpiry" style="font-size:0.78rem;font-weight:600;color:rgba(255,255,255,0.9);">MM/YY
                    </div>
                </div>
                <div id="previewType"
                    style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:rgba(255,255,255,0.6);">
                    CARD</div>
            </div>
        </div>

        <div class="form-field" style="margin-bottom:14px;">
            <label>Card Number</label>
            <input type="text" id="cardNumber" maxlength="19" placeholder="0000 0000 0000 0000"
                style="font-family:'Playfair Display',serif;letter-spacing:0.12em;font-size:1rem;"
                oninput="formatCardNumber(this);updatePreview()">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div class="form-field">
                <label>Expiry Date</label>
                <input type="text" id="cardExpiry" maxlength="7" placeholder="MM / YY"
                    oninput="formatExpiry(this);updatePreview()">
            </div>
            <div class="form-field">
                <label>CVV</label>
                <input type="password" id="cardCvv" maxlength="4" placeholder="•••">
            </div>
        </div>

        <div class="form-field" style="margin-bottom:20px;">
            <label>Cardholder Name</label>
            <input type="text" id="cardHolder" placeholder="Name as printed on card" value="<?php echo $full_name; ?>"
                oninput="updatePreview()">
        </div>

        <div id="cardError"
            style="display:none;color:#ef4444;font-size:0.78rem;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:9px 12px;margin-bottom:14px;">
        </div>

        <div style="display:flex;gap:10px;">
            <button class="btn-secondary" style="flex:1;" onclick="closeAddCard()">Cancel</button>
            <button class="btn-primary" id="saveCardBtn" style="flex:2;" onclick="saveCard()">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;">
                    <rect x="1" y="4" width="22" height="16" rx="2" />
                    <line x1="1" y1="10" x2="23" y2="10" />
                </svg>
                Save Card
            </button>
        </div>
    </div>
</div>

<script src="../../assets/js/user-js/payment.js"></script>

<?php require '../../includes/_layout_end.php'; ?>