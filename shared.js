/* Shared JavaScript for SVIS Alumni Portal */

function showToast(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;

  const icon = type === 'success'
    ? '<i class="fas fa-check-circle"></i>'
    : '<i class="fas fa-exclamation-circle"></i>';

  toast.innerHTML = `${icon} <span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-20px)';
    setTimeout(() => toast.remove(), 500);
  }, 4000);
}

function handleRegisterAJAX(formId) {
  const regForm = document.getElementById(formId);
  if (regForm && !regForm.dataset.ajaxAttached) {
    regForm.dataset.ajaxAttached = 'true';
    regForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const submitBtn = this.querySelector('button[type="submit"]');
      if (!submitBtn) return;

      const originalBtnText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

      const formData = new FormData(this);
      fetch('insert_reg.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showToast(data.message, 'success');
            regForm.reset();
            if (typeof hideModal === 'function') {
              setTimeout(() => hideModal('register'), 2000);
            }
          } else {
            showToast(data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
    });
  }
}


function handleLoginAJAX(formId) {
  const loginForm = document.getElementById(formId);
  if (loginForm && !loginForm.dataset.ajaxAttached) {
    loginForm.dataset.ajaxAttached = 'true';
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const submitBtn = this.querySelector('button[type="submit"]');
      if (!submitBtn) return;

      const originalBtnText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';

      const formData = new FormData(this);
      fetch('login_code.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => {
              window.location.href = data.redirect;
            }, 1000);
          } else {
            showToast(data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('An error occurred during login. Please try again.', 'error');
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
    });
  }
}

/* Modal System */
function showModal(type) {
  const m = document.getElementById(type + '-modal');
  if (!m) return;
  m.classList.remove('modal-hidden');
  m.classList.add('modal-visible');
  document.body.style.overflow = 'hidden';
  if (type === 'register') hideModal('login');
  if (type === 'login') hideModal('register');
}

function hideModal(type) {
  const m = document.getElementById(type + '-modal');
  if (!m) return;
  m.classList.add('modal-hidden');
  m.classList.remove('modal-visible');
  document.body.style.overflow = '';
}

/* Close modal on outside click */
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.add('modal-hidden');
    e.target.classList.remove('modal-visible');
    document.body.style.overflow = '';
  }
});

/* Password Toggle */
function togglePassword(id) {
  const input = document.getElementById(id);
  if (!input) return;
  input.type = (input.type === 'password') ? 'text' : 'password';
}

/* Custom Selects */
function initCustomSelects() {
  document.querySelectorAll('select').forEach(select => {
    if (select.closest('.cs-wrapper')) return;

    const isSearchable = select.hasAttribute('data-searchable');
    const wrapper = document.createElement('div');
    wrapper.className = 'cs-wrapper';
    if (select.closest('.input-wrap')) wrapper.classList.add('with-icon');

    const selected = document.createElement('div');
    selected.className = 'cs-selected';
    selected.textContent = select.options[select.selectedIndex]?.text || 'Select...';

    const menu = document.createElement('div');
    menu.className = 'cs-menu';

    // --- Search input for searchable selects ---
    let searchInput = null;
    let optionsContainer = menu; // default: options go directly in menu

    if (isSearchable) {
      const searchWrap = document.createElement('div');
      searchWrap.className = 'cs-search-wrap';
      searchInput = document.createElement('input');
      searchInput.type = 'text';
      searchInput.className = 'cs-search-input';
      searchInput.placeholder = 'Search...';
      searchInput.autocomplete = 'off';
      searchWrap.appendChild(searchInput);
      menu.appendChild(searchWrap);

      const scrollArea = document.createElement('div');
      scrollArea.className = 'cs-options-scroll';
      menu.appendChild(scrollArea);
      optionsContainer = scrollArea;

      // Filter logic
      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        scrollArea.querySelectorAll('.cs-option').forEach(opt => {
          const match = opt.textContent.toLowerCase().includes(query);
          opt.style.display = match ? '' : 'none';
        });
      });

      // Prevent menu close when clicking inside search
      searchInput.addEventListener('click', e => e.stopPropagation());
      searchWrap.addEventListener('click', e => e.stopPropagation());
    }

    // --- Build options ---
    Array.from(select.options).forEach((opt, idx) => {
      const option = document.createElement('div');
      option.className = 'cs-option';
      if (idx === select.selectedIndex) option.classList.add('active');
      option.textContent = opt.text;
      option.onclick = (e) => {
        e.stopPropagation();
        select.selectedIndex = idx;
        select.dispatchEvent(new Event('change'));
        selected.textContent = opt.text;
        menu.classList.remove('show');
        wrapper.classList.remove('open');
        menu.querySelectorAll('.cs-option').forEach(o => o.classList.remove('active'));
        option.classList.add('active');
        // Reset search
        if (searchInput) {
          searchInput.value = '';
          searchInput.dispatchEvent(new Event('input'));
        }
      };
      optionsContainer.appendChild(option);
    });

    wrapper.appendChild(selected);
    wrapper.appendChild(menu);
    select.style.display = 'none';
    select.parentNode.insertBefore(wrapper, select.nextSibling);

    selected.onclick = (e) => {
      e.stopPropagation();
      const isOpen = menu.classList.contains('show');
      document.querySelectorAll('.cs-menu').forEach(m => m.classList.remove('show'));
      document.querySelectorAll('.cs-wrapper').forEach(w => w.classList.remove('open'));
      if (!isOpen) {
        menu.classList.add('show');
        wrapper.classList.add('open');
        // Auto-focus search input when opened
        if (searchInput) {
          setTimeout(() => searchInput.focus(), 50);
        }
      }
    };
  });
}

document.addEventListener('click', (e) => {
  // Don't close if clicking inside a search input
  if (e.target.closest('.cs-search-wrap')) return;
  document.querySelectorAll('.cs-menu').forEach(m => m.classList.remove('show'));
  document.querySelectorAll('.cs-wrapper').forEach(w => w.classList.remove('open'));
});

document.addEventListener('DOMContentLoaded', function() {
  handleLoginAJAX('login-form');
  handleRegisterAJAX('register-form');
});
