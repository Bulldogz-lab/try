<?php
$page_title = 'Messages';
$active_page = 'messages';
include '../../includes/session.php';
include '../../includes/layout_open.php';
?>
<link rel="stylesheet" href="../../assets/css/admin-css/message.css">

<div class="page-header">
    <div class="top-header">
        <h2>Messages</h2>
        <div class="page-header-sub">Communications with tenants and staff</div>
    </div>
    <button class="btn btn-primary">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        New Message
    </button>
</div>

<div class="page-inner" style="overflow:hidden;">
    <div class="msg-layout">

        <div class="msg-list">
            <div class="msg-list-header">
                <input type="text" placeholder="Search messages..." />
            </div>
            <div class="msg-threads">
                <?php
                $threads = [
                    ['Zaldy Co', 'Can I extend my lease for another month?', '10:32 AM', 2, true],
                    ['Carlos Reyes', 'HVAC repair is done. Invoice attached.', '9:14 AM', 0, false],
                    ['Bongbong Marcos', 'When can I move in exactly?', 'Yesterday', 1, false],
                    ['Sarah Duterte', 'Is there parking available?', 'Yesterday', 0, false],
                    ['Maria Santos', 'Requesting early check-out on the 25th.', 'Aug 21', 3, false],
                    ['Juan dela Cruz', 'Is the gym open on weekends?', 'Aug 20', 0, false],
                    ['Rosa Reyes', 'Payment sent via GCash.', 'Aug 19', 0, false],
                ];
                foreach ($threads as $i => $t): ?>
                    <div class="msg-thread<?= $i === 0 ? ' active' : '' ?>">
                        <div class="avatar"><?= strtoupper($t[0][0]) ?></div>
                        <div class="msg-thread-info">
                            <div class="msg-thread-name"><?= htmlspecialchars($t[0]) ?></div>
                            <div class="msg-thread-preview"><?= htmlspecialchars($t[1]) ?></div>
                        </div>
                        <div class="msg-thread-meta">
                            <div class="msg-thread-time"><?= $t[2] ?></div>
                            <?php if ($t[3] > 0): ?>
                                <div class="msg-unread"><?= $t[3] ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Conversation pane -->
        <div class="msg-pane">
            <div class="msg-pane-header">
                <div class="avatar">Z</div>
                <div>
                    <div class="msg-pane-title">Zaldy Co</div>
                    <div class="msg-pane-sub">Unit A-101 · Skyline Apartments · Online</div>
                </div>
            </div>
            <div class="msg-pane-body">
                <div class="msg-bubble them">
                    <div class="bubble">Hi Myra! Just checking in — is it possible to extend my lease for another month?
                        My new place won't be ready until October.</div>
                    <div class="btime">10:28 AM</div>
                </div>
                <div class="msg-bubble me">
                    <div class="bubble">Hi Zaldy! Sure, I can process that for you. I'll just need you to sign a lease
                        extension form. I'll send it over by end of day.</div>
                    <div class="btime">10:30 AM</div>
                </div>
                <div class="msg-bubble them">
                    <div class="bubble">Can I extend my lease for another month?</div>
                    <div class="btime">10:32 AM</div>
                </div>
                <div class="msg-bubble them">
                    <div class="bubble">Also, will the rate be the same?</div>
                    <div class="btime">10:32 AM</div>
                </div>
            </div>
            <div class="msg-compose">
                <input type="text" placeholder="Type a message..." />
                <button class="btn btn-primary" style="padding:10px 16px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
<?php include '../../includes/layout_close.php'; ?>