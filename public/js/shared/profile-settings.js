(function () {
  'use strict';

  const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB, matches the server-side limit
  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

  function initAvatarPicker() {
    const input = document.getElementById('profilePicInput');
    const avatarImg = document.getElementById('profileAvatarImg');
    const removeBtn = document.getElementById('profileAvatarRemoveBtn');
    const errorEl = document.getElementById('profileAvatarError');

    if (!input || !avatarImg) {
      return;
    }

    const originalSrc = avatarImg.getAttribute('src');

    function showError(message) {
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = message ? 'block' : 'none';
      }
    }

    function resetSelection() {
      input.value = '';
      avatarImg.src = originalSrc;
      if (removeBtn) {
        removeBtn.style.display = 'none';
      }
      showError('');
    }

    input.addEventListener('change', function () {
      const file = input.files && input.files[0];
      showError('');

      if (!file) {
        return;
      }

      if (!ALLOWED_TYPES.includes(file.type)) {
        showError('Please choose a JPG, PNG, or GIF image.');
        resetSelection();
        return;
      }

      if (file.size > MAX_FILE_SIZE) {
        showError('That image is larger than 2MB. Please choose a smaller file.');
        resetSelection();
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        avatarImg.src = e.target.result;
        if (removeBtn) {
          removeBtn.style.display = 'flex';
        }
      };
      reader.readAsDataURL(file);
    });

    if (removeBtn) {
      removeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        resetSelection();
      });
    }
  }

  function initPasswordToggles() {
    document.querySelectorAll('.password-toggle-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-target');
        const field = document.getElementById(targetId);
        if (!field) {
          return;
        }
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
          field.type = 'text';
          if (icon) icon.classList.replace('bx-hide', 'bx-show');
        } else {
          field.type = 'password';
          if (icon) icon.classList.replace('bx-show', 'bx-hide');
        }
      });
    });
  }

  function scorePasswordStrength(password) {
    if (!password) {
      return 0;
    }
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    return score;
  }

  function initPasswordStrengthAndMatch() {
    const newPasswordField = document.getElementById('newPassword');
    const confirmField = document.getElementById('confirmPassword');
    const meterWrap = document.getElementById('passwordStrengthWrap');
    const fill = document.getElementById('passwordStrengthFill');
    const label = document.getElementById('passwordStrengthLabel');
    const matchHint = document.getElementById('passwordMatchHint');

    if (newPasswordField && meterWrap && fill && label) {
      newPasswordField.addEventListener('input', function () {
        const value = newPasswordField.value;
        meterWrap.classList.remove('strength-weak', 'strength-fair', 'strength-strong');

        if (!value) {
          fill.style.width = '0%';
          label.textContent = '';
          return;
        }

        const score = scorePasswordStrength(value);
        if (score <= 2) {
          meterWrap.classList.add('strength-weak');
          fill.style.width = '33%';
          label.textContent = 'Weak';
        } else if (score <= 3) {
          meterWrap.classList.add('strength-fair');
          fill.style.width = '66%';
          label.textContent = 'Fair';
        } else {
          meterWrap.classList.add('strength-strong');
          fill.style.width = '100%';
          label.textContent = 'Strong';
        }
      });
    }

    function checkMatch() {
      if (!confirmField || !matchHint) {
        return;
      }
      const newVal = newPasswordField ? newPasswordField.value : '';
      const confirmVal = confirmField.value;

      matchHint.classList.remove('is-match', 'is-mismatch');

      if (!confirmVal) {
        return;
      }

      if (newVal === confirmVal) {
        matchHint.textContent = 'Passwords match.';
        matchHint.classList.add('is-match');
      } else {
        matchHint.textContent = "Passwords don't match.";
        matchHint.classList.add('is-mismatch');
      }
    }

    if (newPasswordField) {
      newPasswordField.addEventListener('input', checkMatch);
    }
    if (confirmField) {
      confirmField.addEventListener('input', checkMatch);
    }
  }

  function initSubmitLoadingState() {
    document.querySelectorAll('form[data-loading-submit]').forEach(function (form) {
      form.addEventListener('submit', function () {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn || btn.disabled) {
          return;
        }
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        // Disabling the submit button here, synchronously inside its own
        // form's submit handler, would drop its name=value pair from the
        // request -- the browser builds the submitted form data AFTER the
        // submit event finishes, and a disabled button isn't included in
        // it. That silently strips update_personal_info/update_security
        // from the POST, so the server never matches either branch and
        // never redirects. Deferring the disable to the next tick lets the
        // browser finish serializing the submission first.
        setTimeout(function () {
          btn.disabled = true;
        }, 0);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAvatarPicker();
    initPasswordToggles();
    initPasswordStrengthAndMatch();
    initSubmitLoadingState();
  });
})();
