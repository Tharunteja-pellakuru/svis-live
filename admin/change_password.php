<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('../db_connect.php'); ?>

<div class="dash-main">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24">
          <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Administration</div>
        <div class="page-header-title">Change Password</div>
      </div>
    </div>
  </div>

  <style>
    .form-card-body { padding: 1.75rem 1.5rem; }
    .field { margin-bottom: 1.35rem; }
    .field label { display: block; font-size: 0.78rem; font-weight: 700; color: var(--text); letter-spacing: 0.07em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap { position: relative; }
    .input-wrap input { width: 100%; height: 46px; padding: 0 46px 0 14px; font-family: 'Lato', sans-serif; font-size: 0.95rem; color: var(--text); background: var(--bg); border: 1.5px solid rgba(26,86,160,0.18); border-radius: 9px; outline: none; transition: all 0.18s; }
    .input-wrap input:focus { border-color: var(--blue); background: var(--surface); box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; display: flex; align-items: center; border-radius: 5px; }
    .pw-toggle svg { width: 16px; height: 16px; stroke: var(--text-3); fill: none; stroke-width: 1.75; }
    .field-divider { height: 1px; background: var(--border-soft); margin: 1.5rem 0; }
    .strength-wrap { margin-top: 0.5rem; display: none; }
    .strength-bar { height: 3px; border-radius: 4px; background: rgba(26,86,160,0.08); overflow: hidden; margin-bottom: 4px; }
    .strength-fill { height: 100%; width: 0%; border-radius: 4px; transition: width 0.3s, background 0.3s; }
    .strength-label { font-size: 0.75rem; font-weight: 700; color: var(--text-3); }
    .btn-submit { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 48px; margin-top: 1.5rem; font-weight: 700; color: white; background: var(--blue); border: none; border-radius: 9px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 9px; box-shadow: 0 2px 8px rgba(26,86,160,0.25); }
    .btn-submit:hover { background: var(--blue-hover); box-shadow: 0 4px 14px rgba(26,86,160,0.35); }
    .tips-box { margin-top: 1.5rem; background: var(--blue-light); border: 1px solid rgba(26,86,160,0.15); border-radius: 10px; padding: 1rem 1.1rem; }
    .tips-title { font-size: 0.78rem; font-weight: 700; color: var(--blue); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.55rem; display: flex; align-items: center; gap: 6px; }
    .tips-list { list-style: none; display: flex; flex-direction: column; gap: 5px; }
    .tips-list li { font-size: 0.82rem; color: var(--blue-dark); display: flex; align-items: center; gap: 7px; opacity: 0.85; }
    .tips-list li::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: var(--blue); flex-shrink: 0; opacity: 0.5; }
    .alert-box { display: flex; align-items: flex-start; gap: 9px; border-radius: 9px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; font-size: 0.88rem; line-height: 1.5; }
    .alert-box.error { background: #fef2f2; border: 1px solid #f5c6c2; border-left: 3px solid #c0392b; color: #c0392b; }
    .alert-box.warning { background: #fffbeb; border: 1px solid #fde68a; border-left: 3px solid #a16207; color: #a16207; }
    .alert-box.success { background: #f0faf6; border: 1px solid #a7dfc8; border-left: 3px solid #0f7a56; color: #0f7a56; }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></div>
        <span class="panel-head-title">Update your credentials</span>
      </div>
    </div>

    <div class="form-card-body">
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $current = $_POST['current_password']; $new = $_POST['new_password']; $confirm = $_POST['confirm_password']; $id = $_SESSION['admin_id'];
          $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id=?"); $stmt->bind_param("i", $id); $stmt->execute(); $result = $stmt->get_result()->fetch_assoc();
          if (!password_verify($current, $result['password'])) {
              echo '<div class="alert-box error">Current password is incorrect.</div>';
          } elseif ($new !== $confirm) {
              echo '<div class="alert-box warning">New passwords do not match.</div>';
          } else {
              $hashed = password_hash($new, PASSWORD_DEFAULT); $update = $conn->prepare("UPDATE admin_users SET password=? WHERE id=?"); $update->bind_param("si", $hashed, $id); $update->execute();
              echo '<div class="alert-box success">Password updated successfully.</div>';
          }
      }
      ?>
      <form method="POST" novalidate>
        <div class="field">
          <label for="current_password">Current Password</label>
          <div class="input-wrap">
            <input type="password" id="current_password" name="current_password" placeholder="Enter your current password" required>
            <button type="button" class="pw-toggle" data-target="current_password"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
          </div>
        </div>
        <div class="field-divider"></div>
        <div class="field">
          <label for="new_password">New Password</label>
          <div class="input-wrap">
            <input type="password" id="new_password" name="new_password" placeholder="Enter a strong new password" required>
            <button type="button" class="pw-toggle" data-target="new_password"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
          </div>
          <div class="strength-wrap" id="strengthWrap"><div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div><span class="strength-label" id="strengthLabel"></span></div>
        </div>
        <div class="field">
          <label for="confirm_password">Confirm New Password</label>
          <div class="input-wrap">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password" required>
            <button type="button" class="pw-toggle" data-target="confirm_password"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
          </div>
        </div>
        <button type="submit" class="btn-submit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Update Password
        </button>
      </form>
      <div class="tips-box">
        <div class="tips-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Password Tips</div>
        <ul class="tips-list"><li>Use at least 8 characters</li><li>Mix uppercase and lowercase letters</li><li>Include numbers and special characters</li></ul>
      </div>
    </div>
  </div>
</div>

<script>
  const EYE_OPEN = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
  const EYE_CLOSED = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;
  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target); const show = input.type === 'password';
      input.type = show ? 'text' : 'password'; btn.querySelector('svg').innerHTML = show ? EYE_CLOSED : EYE_OPEN;
    });
  });
  const newPw = document.getElementById('new_password'); const strengthWrap = document.getElementById('strengthWrap'); const strengthFill = document.getElementById('strengthFill'); const strengthLabel = document.getElementById('strengthLabel');
  const levels = [{ label: 'Too short', color: '#e24b4a', w: '15%' },{ label: 'Weak', color: '#e08a00', w: '35%' },{ label: 'Fair', color: '#e08a00', w: '55%' },{ label: 'Good', color: '#0fa87e', w: '75%' },{ label: 'Strong', color: '#0f7a56', w: '100%' }];
  newPw.addEventListener('input', () => {
    const v = newPw.value; if (!v) { strengthWrap.style.display = 'none'; return; } strengthWrap.style.display = 'block';
    let score = 0; if (v.length >= 8) score++; if (/[A-Z]/.test(v)) score++; if (/[0-9]/.test(v)) score++; if (/[^A-Za-z0-9]/.test(v)) score++;
    const lvl = v.length < 6 ? 0 : Math.min(score, 4); strengthFill.style.width = levels[lvl].w; strengthFill.style.background = levels[lvl].color; strengthLabel.style.color = levels[lvl].color; strengthLabel.textContent = levels[lvl].label;
  });
</script>
</body></html>