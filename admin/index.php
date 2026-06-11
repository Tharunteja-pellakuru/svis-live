<?php
session_start();
include('../db_connect.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user + role + permissions
    $stmt = $conn->prepare("
        SELECT a.*, r.role_name, r.permissions 
        FROM admin_users a
        LEFT JOIN admin_roles r ON r.id = a.role_id
        WHERE a.email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_role'] = $row['role_name'];
            $_SESSION['admin_permissions'] = json_decode($row['permissions'], true);

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Invalid email!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — SVIS Alumni Portal</title>
<link rel="icon" type="image/png" href="../Logo/FavIcon.png">
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<style>

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --blue:        #1a56a0;
    --blue-dark:   #0f3566;
    --blue-hover:  #154a8a;
    --blue-light:  #e8f0fb;
    --blue-dot:    #c8daef;
    --text:        #0d1f3c;
    --muted:       #6b82a0;
    --border:      #dde8f5;
    --bg:          #f5f8fd;
    --white:       #ffffff;
    --err:         #c0392b;
    --err-bg:      #fef2f2;
    --err-border:  #f5c6c2;
  }

  html, body { height: 100%; }

  body {
    font-family: 'Lato', sans-serif;
    background: var(--bg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem 1rem;
  }

  /* Subtle dot-grid background */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: radial-gradient(circle, var(--blue-dot) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.5;
    pointer-events: none;
    z-index: 0;
  }

  /* ── Branding above card ── */
  .site-header {
    position: relative;
    z-index: 1;
    margin-bottom: 1.75rem;
    animation: fadeUp 0.4s ease both;
  }

  .site-logo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }

  .logo-icon {
    width: 42px;
    height: 42px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
  }

  .logo-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .logo-text { display: flex; flex-direction: column; line-height: 1; }

  .logo-name {
    font-family: 'Lato', sans-serif;
    font-size: 1.25rem;
    font-weight: 900;
    color: var(--text);
    letter-spacing: 0.01em;
  }

  .logo-tag {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
    color: var(--muted);
    margin-top: 3px;
  }

  /* ── Login card ── */
  .card {
    position: relative;
    z-index: 1;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    width: 100%;
    max-width: 420px;
    padding: 2.75rem 2.5rem 2.25rem;
    animation: fadeUp 0.45s 0.06s ease both;
    opacity: 0;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* Blue top accent line */
  .card::before {
    content: '';
    position: absolute;
    top: 0; left: 2.5rem; right: 2.5rem;
    height: 3px;
    background: var(--blue);
    border-radius: 0 0 3px 3px;
  }

  /* ── Card heading ── */
  .card-head { margin-bottom: 2rem; }

  .card-head h1 {
    font-family: 'Lato', sans-serif;
    font-size: 1.85rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1.2;
    margin-bottom: 0.3rem;
  }

  .card-head p {
    font-size: 0.92rem;
    color: var(--muted);
    font-weight: 300;
  }

  /* ── Error box ── */
  .error-box {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    background: var(--err-bg);
    border: 1px solid var(--err-border);
    border-left: 3px solid var(--err);
    border-radius: 8px;
    padding: 0.7rem 0.9rem;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
    color: var(--err);
    font-weight: 400;
    line-height: 1.45;
  }

  .error-box svg {
    flex-shrink: 0;
    margin-top: 1px;
    width: 15px; height: 15px;
    stroke: var(--err);
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* ── Fields ── */
  .field { margin-bottom: 1.25rem; }

  .label-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.5rem;
  }

  .label-row label {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 0.07em;
    text-transform: uppercase;
  }

  .label-row a {
    font-size: 0.78rem;
    color: var(--blue);
    text-decoration: none;
    font-weight: 400;
    transition: opacity 0.15s;
  }

  .label-row a:hover { opacity: 0.65; text-decoration: underline; }

  .input-wrap { position: relative; }

  .input-wrap input {
    width: 100%;
    height: 46px;
    padding: 0 46px 0 14px;
    font-family: 'Lato', sans-serif;
    font-size: 0.95rem;
    font-weight: 400;
    color: var(--text);
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 9px;
    outline: none;
    -webkit-appearance: none;
    transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
  }

  .input-wrap input::placeholder { color: var(--muted); font-weight: 300; }
  .input-wrap input:hover        { border-color: #adc6e8; }
  .input-wrap input:focus {
    border-color: var(--blue);
    background: var(--white);
    box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09);
  }

  /* Trailing icon */
  .i-icon {
    position: absolute;
    right: 14px; top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    width: 16px; height: 16px;
    stroke: var(--border);
    fill: none;
    stroke-width: 1.75;
    stroke-linecap: round; stroke-linejoin: round;
    transition: stroke 0.18s;
  }

  .input-wrap:focus-within .i-icon { stroke: var(--blue); }

  /* Password toggle */
  .pw-btn {
    position: absolute;
    right: 10px; top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
  }

  .pw-btn svg {
    width: 16px; height: 16px;
    stroke: var(--muted);
    fill: none;
    stroke-width: 1.75;
    stroke-linecap: round; stroke-linejoin: round;
    transition: stroke 0.15s;
  }

  .pw-btn:hover svg { stroke: var(--blue); }

  /* ── Submit button ── */
  .btn-submit {
    width: 100%;
    height: 48px;
    margin-top: 0.4rem;
    font-family: 'Lato', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: var(--white);
    background: var(--blue);
    border: none;
    border-radius: 9px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.18s, transform 0.1s;
  }



  .btn-submit svg {
    width: 15px; height: 15px;
    stroke: white; fill: none;
    stroke-width: 2.2;
    stroke-linecap: round; stroke-linejoin: round;
  }

  .btn-submit:hover  { background: var(--blue-hover); }
  .btn-submit:active { transform: scale(0.985); }

  /* ── Card footer ── */
  .card-foot {
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .card-foot svg {
    width: 13px; height: 13px;
    stroke: var(--muted); fill: none;
    stroke-width: 1.75;
    stroke-linecap: round; stroke-linejoin: round;
    flex-shrink: 0;
  }

  .card-foot span {
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 300;
  }

  /* ── Responsive ── */
  @media (max-width: 460px) {
    .card { padding: 2.25rem 1.5rem 2rem; border-radius: 14px; }
    .card::before { left: 1.5rem; right: 1.5rem; }
  }

</style>
</head>
<body>

  <!-- Branding -->
  <div class="site-header">
    <a href="#" class="site-logo">
      <div class="logo-icon">
        <img src="../Logo/Logo.png" alt="SVIS Logo" />
      </div>
      <div class="logo-text">
        <span class="logo-name">SVIS Alumni</span>
        <span class="logo-tag">Admin Panel</span>
      </div>
    </a>
  </div>

  <!-- Card -->
  <div class="card">

    <div class="card-head">
      <h1>Welcome</h1>
      <p>Sign in to access the admin dashboard</p>
    </div>

    <?php if ($message): ?>
    <div class="error-box">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8"  x2="12"    y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>

      <!-- Email -->
      <div class="field">
        <div class="label-row">
          <label for="email">Email Address</label>
        </div>
        <div class="input-wrap">
          <input
            type="email"
            id="email"
            name="email"
            placeholder="admin@svisalumni.in"
            required
            autocomplete="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          >
          <svg class="i-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M2 7l10 7 10-7"/>
          </svg>
        </div>
      </div>

      <!-- Password -->
      <div class="field">
        <div class="label-row">
          <label for="password">Password</label>
          <!-- <a href="forgot_password.php">Forgot password?</a> -->
        </div>
        <div class="input-wrap">
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
          >
          <button type="button" class="pw-btn" id="pwToggle" aria-label="Toggle password visibility">
            <svg id="eyeIcon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Login button -->
      <button type="submit" class="btn-submit">
        Sign In
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
      </button>

    </form>

    <div class="card-foot">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0110 0v4"/>
      </svg>
      <span>Restricted to authorised personnel only</span>
    </div>

  </div>

<script>
  const toggle  = document.getElementById('pwToggle');
  const pwInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');

  const EYE_OPEN   = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
  const EYE_CLOSED = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

  toggle.addEventListener('click', () => {
    const show     = pwInput.type === 'password';
    pwInput.type   = show ? 'text' : 'password';
    eyeIcon.innerHTML = show ? EYE_CLOSED : EYE_OPEN;
  });
</script>

</body>
</html>