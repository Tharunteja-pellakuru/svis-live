<?php
session_start();
include('../db_connect.php');

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    // Your reset logic here — e.g. send reset link
    // $message = "Reset link sent!"; $success = true;
    // or: $message = "Email not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — SVIS Alumni Portal</title>
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
    --ok:          #0f7a56;
    --ok-bg:       #f0faf6;
    --ok-border:   #a7dfc8;
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

  /* Dot-grid background — matches login page */
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

  /* ── Branding ── */
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
    width: 42px; height: 42px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
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

  /* ── Card ── */
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

  /* ── Icon circle at top of card ── */
  .card-icon {
    width: 52px; height: 52px;
    background: var(--blue-light);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
  }

  .card-icon svg {
    width: 22px; height: 22px;
    stroke: var(--blue); fill: none;
    stroke-width: 1.75;
    stroke-linecap: round; stroke-linejoin: round;
  }

  /* ── Card heading ── */
  .card-head { margin-bottom: 1.85rem; }

  .card-head h1 {
    font-family: 'Lato', sans-serif;
    font-size: 1.75rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1.2;
    margin-bottom: 0.35rem;
  }

  .card-head p {
    font-size: 0.92rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.55;
  }

  /* ── Alert boxes ── */
  .alert-box {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    border-radius: 9px;
    padding: 0.7rem 0.9rem;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
    font-weight: 400;
    line-height: 1.45;
  }

  .alert-box svg {
    flex-shrink: 0; margin-top: 1px;
    width: 15px; height: 15px; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }

  .alert-box.error {
    background: var(--err-bg);
    border: 1px solid var(--err-border);
    border-left: 3px solid var(--err);
    color: var(--err);
  }

  .alert-box.error svg { stroke: var(--err); }

  .alert-box.success {
    background: var(--ok-bg);
    border: 1px solid var(--ok-border);
    border-left: 3px solid var(--ok);
    color: var(--ok);
  }

  .alert-box.success svg { stroke: var(--ok); }

  /* ── Field ── */
  .field { margin-bottom: 1.3rem; }

  .field label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
  }

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

  /* ── Helper text ── */
  .field-hint {
    margin-top: 0.45rem;
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 300;
  }

  /* ── Submit button ── */
  .btn-submit {
    width: 100%;
    height: 48px;
    margin-top: 0.3rem;
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
    gap: 9px;
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

  /* ── Back to login link ── */
  .back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 1.6rem;
    font-size: 0.88rem;
    color: var(--blue);
    text-decoration: none;
    font-weight: 400;
    transition: opacity 0.15s;
  }

  .back-link svg {
    width: 14px; height: 14px;
    stroke: var(--blue); fill: none;
    stroke-width: 2;
    stroke-linecap: round; stroke-linejoin: round;
    transition: transform 0.15s;
  }

  .back-link:hover { opacity: 0.7; }
  .back-link:hover svg { transform: translateX(-3px); }

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

  @media (max-width: 460px) {
    .card { padding: 2.25rem 1.5rem 2rem; border-radius: 14px; }
    .card::before { left: 1.5rem; right: 1.5rem; }
  }

</style>
</head>
<body>

  <!-- Branding -->
  <div class="site-header">
    <a href="login.php" class="site-logo">
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

    <div class="card-icon">
      <svg viewBox="0 0 24 24">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0110 0v4"/>
        <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/>
      </svg>
    </div>

    <div class="card-head">
      <h1>Reset your password</h1>
      <p>Enter your registered email address and we'll send you a link to reset your password.</p>
    </div>

    <?php if ($message && !$success): ?>
    <div class="alert-box error">
      <svg viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8"  x2="12"    y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert-box success">
      <svg viewBox="0 0 24 24">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>

      <div class="field">
        <label for="email">Email Address</label>
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
          <svg class="i-icon" viewBox="0 0 24 24">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M2 7l10 7 10-7"/>
          </svg>
        </div>
        <div class="field-hint">We'll send a reset link to this address.</div>
      </div>

      <button type="submit" class="btn-submit">
        Send Reset Link
        <svg viewBox="0 0 24 24">
          <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
      </button>

    </form>

    <a href="login.php" class="back-link">
      <svg viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
      Back to sign in
    </a>

    <div class="card-foot">
      <svg viewBox="0 0 24 24">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0110 0v4"/>
      </svg>
      <span>Restricted to authorised personnel only</span>
    </div>

  </div>

</body>
</html>