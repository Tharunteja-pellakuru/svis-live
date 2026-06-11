<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'SuperAdmin') {
    $_SESSION['flash_msg'] = "Access Denied: SuperAdmin privilege required.";
    $_SESSION['flash_type'] = 'error';
    header("Location: dashboard.php");
    exit();
}
include('header.php');
include('sidebar.php');
include('../db_connect.php');
?>

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── Card ── */
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow-sm, 0 1px 3px rgba(15,53,102,0.08));
        width: 100%;
        overflow: hidden;
    }

    .form-card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 1.1rem 1.5rem;
        background: var(--surface-alt);
        border-bottom: 1px solid var(--border-soft);
    }

    .form-card-head-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: rgba(26,86,160,0.10);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .form-card-head-icon svg {
        width: 14px; height: 14px;
        stroke: var(--blue); fill: none;
        stroke-width: 1.75;
        stroke-linecap: round; stroke-linejoin: round;
    }

    .form-card-head-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text);
    }

    .form-card-body {
        padding: 1.85rem 1.75rem;
    }

    /* ── Two column grid ── */
    .field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 1.5rem;
    }

    .field-full { grid-column: 1 / -1; }

    @media (max-width: 768px) {
        .field-grid { grid-template-columns: 1fr; }
    }

    /* ── Field ── */
    .field { margin-bottom: 1.35rem; }

    .field label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text);
        letter-spacing: 0.07em;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .field label span.req {
        color: #e24b4a;
        margin-left: 2px;
    }

    .input-wrap { position: relative; }

    .input-wrap input,
    .input-wrap select {
        width: 100%;
        height: 46px;
        padding: 0 46px 0 14px;
        font-family: 'Lato', sans-serif;
        font-size: 0.95rem;
        font-weight: 400;
        color: var(--text);
        background: var(--bg);
        border: 1.5px solid rgba(26,86,160,0.18);
        border-radius: 9px;
        outline: none;
        -webkit-appearance: none;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    }

    .input-wrap select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238aa0bb' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 38px;
    }

    .input-wrap input::placeholder { color: var(--text-3); font-weight: 300; }

    .input-wrap input:hover,
    .input-wrap select:hover { border-color: rgba(26,86,160,0.35); }

    .input-wrap input:focus,
    .input-wrap select:focus {
        border-color: var(--blue);
        background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09);
    }

    /* Field trailing icon */
    .f-icon {
        position: absolute;
        right: 14px; top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        width: 16px; height: 16px;
        stroke: rgba(26,86,160,0.25);
        fill: none;
        stroke-width: 1.75;
        stroke-linecap: round; stroke-linejoin: round;
        transition: stroke 0.18s;
    }

    .input-wrap:focus-within .f-icon { stroke: var(--blue); }

    /* Password toggle */
    .pw-toggle {
        position: absolute;
        right: 10px; top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        display: flex; align-items: center;
        border-radius: 5px;
        transition: background 0.15s;
    }

    .pw-toggle:hover { background: rgba(26,86,160,0.07); }

    .pw-toggle svg {
        width: 16px; height: 16px;
        stroke: var(--text-3); fill: none;
        stroke-width: 1.75;
        stroke-linecap: round; stroke-linejoin: round;
        transition: stroke 0.15s;
    }

    .pw-toggle:hover svg { stroke: var(--blue); }

    /* Strength bar */
    .strength-wrap { margin-top: 0.45rem; display: none; }

    .strength-bar {
        height: 3px;
        border-radius: 4px;
        background: rgba(26,86,160,0.08);
        overflow: hidden;
        margin-bottom: 4px;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 4px;
        transition: width 0.3s, background 0.3s;
    }

    .strength-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-3);
    }

    /* ── Section divider ── */
    .section-divider {
        height: 1px;
        background: var(--border-soft);
        margin: 0.25rem 0 1.5rem;
    }

    .section-heading {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--text-3);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-heading::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-soft);
    }

    /* ── Form actions ── */
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-soft);
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 1.5rem;
        height: 46px;
        background: var(--blue);
        color: white;
        font-family: 'Lato', sans-serif;
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.18s, box-shadow 0.18s, transform 0.1s;
        box-shadow: 0 2px 8px rgba(26,86,160,0.22);
    }

    .btn-save svg {
        width: 15px; height: 15px;
        stroke: white; fill: none;
        stroke-width: 2.2;
        stroke-linecap: round; stroke-linejoin: round;
    }

    .btn-save:hover  { background: var(--blue-hover); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
    .btn-save:active { transform: scale(0.985); }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 1.25rem;
        height: 46px;
        background: var(--surface);
        color: var(--text-2);
        font-family: 'Lato', sans-serif;
        font-size: 0.92rem;
        font-weight: 700;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .btn-cancel svg {
        width: 14px; height: 14px;
        stroke: var(--text-3); fill: none;
        stroke-width: 2;
        stroke-linecap: round; stroke-linejoin: round;
        transition: stroke 0.15s;
    }

    .btn-cancel:hover {
        background: var(--surface-alt);
        border-color: rgba(26,86,160,0.25);
        color: var(--text);
        text-decoration: none;
    }

    .btn-cancel:hover svg { stroke: var(--text-2); }
    </style>

    <div class="dash-main">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
        <div class="page-header-icon">
            <svg viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="19" y1="8" x2="19" y2="14"/>
            <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </div>
        <div>
            <div class="page-header-label">Administration</div>
            <div class="page-header-title">Add New Admin</div>
        </div>
        </div>
        <a href="admin_list.php" class="btn-cancel" style="text-decoration:none;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">

        <div class="form-card-head">
        <div class="form-card-head-icon">
            <svg viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 00-4-4h-4"/><circle cx="9" cy="7" r="4"/>
            <path d="M16 11l2 2 4-4"/>
            </svg>
        </div>
        <span class="form-card-head-title">Admin Details</span>
        </div>

        <div class="form-card-body">

        <form action="save_admin.php" method="POST" novalidate>

            <div class="section-heading">Personal Information</div>

            <div class="field-grid">

            <!-- Full Name -->
            <div class="field">
                <label for="name">Full Name <span class="req">*</span></label>
                <div class="input-wrap">
                <input type="text" id="name" name="name" placeholder="e.g. Rahul Sharma" required>
                <svg class="f-icon" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                </div>
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email">Email Address <span class="req">*</span></label>
                <div class="input-wrap">
                <input type="email" id="email" name="email" placeholder="admin@svisalumni.in" required>
                <svg class="f-icon" viewBox="0 0 24 24">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M2 7l10 7 10-7"/>
                </svg>
                </div>
            </div>

            </div>

            <div class="section-divider"></div>
            <div class="section-heading">Security & Access</div>

            <div class="field-grid">

            <!-- Password -->
            <div class="field">
                <label for="password">Password <span class="req">*</span></label>
                <div class="input-wrap">
                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password visibility">
                    <svg id="eyeIcon" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
                </div>
                <div class="strength-wrap" id="strengthWrap">
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <span class="strength-label" id="strengthLabel"></span>
                </div>
            </div>

            <!-- Role -->
            <div class="field">
                <label for="role_id">Role <span class="req">*</span></label>
                <div class="input-wrap">
                <select id="role_id" name="role_id" required>
                    <option value="">— Select a role —</option>
                    <?php
                    $roles = $conn->query("SELECT * FROM admin_roles");
                    while ($r = $roles->fetch_assoc()):
                    ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                    <?php endwhile; ?>
                </select>
                </div>
            </div>

            </div>

            <!-- Actions -->
            <div class="form-actions">
            <a href="admin_list.php" class="btn-cancel">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Cancel
            </a>
            <button type="submit" class="btn-save">
                <svg viewBox="0 0 24 24">
                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Admin
            </button>
            </div>

        </form>

        </div>
    </div>

    </div>

    </body>
    </html>

    <script>
    // Password eye toggle
    const toggle  = document.getElementById('pwToggle');
    const pwInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    const EYE_OPEN   = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    const EYE_CLOSED = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

    toggle.addEventListener('click', () => {
        const show    = pwInput.type === 'password';
        pwInput.type  = show ? 'text' : 'password';
        eyeIcon.innerHTML = show ? EYE_CLOSED : EYE_OPEN;
    });

    // Password strength meter
    const strengthWrap  = document.getElementById('strengthWrap');
    const strengthFill  = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');

    const levels = [
        { label: 'Too short',  color: '#e24b4a', w: '15%'  },
        { label: 'Weak',       color: '#e08a00', w: '35%'  },
        { label: 'Fair',       color: '#e08a00', w: '55%'  },
        { label: 'Good',       color: '#0fa87e', w: '75%'  },
        { label: 'Strong',     color: '#0f7a56', w: '100%' },
    ];

    pwInput.addEventListener('input', () => {
        const v = pwInput.value;
        if (!v) { strengthWrap.style.display = 'none'; return; }
        strengthWrap.style.display = 'block';
        let score = 0;
        if (v.length >= 8)          score++;
        if (/[A-Z]/.test(v))        score++;
        if (/[0-9]/.test(v))        score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        const lvl = v.length < 6 ? 0 : Math.min(score, 4);
        strengthFill.style.width      = levels[lvl].w;
        strengthFill.style.background = levels[lvl].color;
        strengthLabel.style.color     = levels[lvl].color;
        strengthLabel.textContent     = levels[lvl].label;
    });
    </script>