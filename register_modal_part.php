<?php
/**
 * Shared Registration Modal for SVIS Alumni
 * This file is included in multiple pages to ensure consistency.
 */

// Fetch countries if not already available
if (!isset($countries) || empty($countries)) {
    $countries = [];
    if (isset($conn)) {
        $res = $conn->query("SELECT id, name FROM countries ORDER BY name ASC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $countries[] = $row;
            }
        }
    }
}
?>

<!-- ===== REGISTER MODAL ===== -->
<div id="register-modal" class="modal-overlay modal-hidden">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-logos">
        <img src="Logo/Logo.svg" alt="SVIS Alumni" style="height:48px;" onerror="this.style.display='none'"/>
      </div>
      <h2 id="register-modal-title">Join SVIS Alumni Network</h2>
      <p id="register-modal-subtitle">Sadhu Vaswani International School, Hyderabad</p>
    </div>
    <button class="modal-close" onclick="hideModal('register')" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="modal-body">
      <form id="register-form" method="POST" action="insert_reg.php">
        <div class="v-single-column-modal">
          <div class="form-group">
            <label>Full Name<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
              <input type="text" name="full_name" placeholder="Enter your full name" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Gender<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
              <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Date of Birth<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-calendar-alt"></i></div>
              <input type="date" name="dob" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Mobile Number<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-phone"></i></div>
              <input type="tel" name="phone" placeholder="Enter mobile number" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Email Address<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
              <input type="email" name="email" placeholder="Enter your email" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Current City<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-city"></i></div>
              <input type="text" name="city" placeholder="Enter current city" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Current Country<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-globe"></i></div>
              <select name="country" required data-searchable>
                <option value="">Select Country</option>
                <?php foreach ($countries as $c): ?>
                  <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Current Qualification<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-graduation-cap"></i></div>
              <input type="text" name="qualification" placeholder="e.g. B.Tech Computer Science" required/>
            </div>
          </div>
          <div class="form-group">
            <label>College / University Name<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><i class="fas fa-university"></i></div>
              <input type="text" name="college" placeholder="Enter college/university name" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Batch Year<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
              <select name="batch" required>
                <option value="">Select Year</option>
                <?php for ($y = 2008; $y <= date('Y'); $y++): ?>
                  <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Password<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
              <input type="password" id="register-password" name="password" placeholder="Create a password" required/>
              <button type="button" class="pw-toggle" onclick="togglePassword('register-password')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
          </div>
          <div class="form-group">
            <div class="checkbox-wrap">
              <input type="checkbox" name="terms" required/>
              <span>I agree to the <button type="button">Terms &amp; Conditions</button></span>
            </div>
          </div>
          <button type="submit" class="form-submit">Register for Alumni</button>
          <div class="form-switch">Already have an account? <button type="button" onclick="showModal('login')">Login here</button></div>
        </div>
      </form>
    </div>
  </div>
</div>
