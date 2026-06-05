/**
 * Secure Auth System - Premium UI Scripting
 */

document.addEventListener('DOMContentLoaded', () => {
    setupPasswordStrengthMeter();
    setupAjaxFormSubmissions();
    setupAdminControls();
});

/**
 * Floating Toasted Alert Notification
 */
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    
    let icon = '<i class="bi bi-info-circle-fill text-primary"></i>';
    if (type === 'success') {
        icon = '<i class="bi bi-check-circle-fill text-success"></i>';
    } else if (type === 'error') {
        icon = '<i class="bi bi-exclamation-triangle-fill text-danger"></i>';
    } else if (type === 'warning') {
        icon = '<i class="bi bi-exclamation-circle-fill text-warning"></i>';
    }
    
    toast.innerHTML = `
        <div style="font-size: 1.25rem;">${icon}</div>
        <div style="flex-grow: 1; font-size: 0.9rem; font-weight: 500;">${message}</div>
        <button type="button" class="btn-close btn-close-white style="font-size: 0.75rem; opacity: 0.6;" onclick="this.parentElement.remove()"></button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 5000);
}

/**
 * Toggle Loading Spin Layer Overlay
 */
function toggleLoader(show) {
    let overlay = document.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>';
        document.body.appendChild(overlay);
    }
    
    if (show) {
        overlay.classList.add('active');
    } else {
        overlay.classList.remove('active');
    }
}

/**
 * Handle form submissions programmatically via AJAX
 */
function setupAjaxFormSubmissions() {
    const ajaxForms = document.querySelectorAll('form[data-ajax="true"]');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (form.hasAttribute('data-validate') && !validateForm(form)) {
                return;
            }
            
            const url = form.getAttribute('action');
            const method = form.getAttribute('method') || 'POST';
            const formData = new FormData(form);
            
            toggleLoader(true);
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: method === 'POST' ? formData : null
                });
                
                const result = await response.json();
                toggleLoader(false);
                
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1200);
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    }
                } else if (result.status === 'verify') {
                    showToast(result.message, 'warning');
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1800);
                    }
                } else {
                    showToast(result.message || 'Error occurred.', 'error');
                }
                
            } catch (err) {
                toggleLoader(false);
                console.error("AJAX error: ", err);
                showToast('A network error occurred. Please try again.', 'error');
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const emailInput = form.querySelector('input[type="email"]');
    const passwordInput = form.querySelector('input[type="password"]:not([name="current_password"])');
    
    if (emailInput) {
        const email = emailInput.value.trim();
        // Allow all email formats
    }
    
    if (passwordInput) {
        const pwd = passwordInput.value;
        if (pwd.length < 4) {
            showToast('Password must be at least 4 characters long.', 'warning');
            isValid = false;
        }
    }
    
    return isValid;
}

/**
 * Real-time Password Strength Meter
 */
function setupPasswordStrengthMeter() {
    const passwordInputs = document.querySelectorAll('input[data-strength-meter]');
    
    passwordInputs.forEach(input => {
        const meterBar = document.getElementById(input.getAttribute('data-strength-bar'));
        const textLabel = document.getElementById(input.getAttribute('data-strength-text'));
        
        if (!meterBar) return;
        
        input.addEventListener('input', () => {
            const val = input.value;
            let score = 0;
            
            if (val.length === 0) {
                meterBar.className = 'password-meter-bar';
                if (textLabel) textLabel.textContent = '';
                return;
            }
            
            if (val.length >= 8) score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            
            meterBar.className = 'password-meter-bar';
            
            if (score <= 2) {
                meterBar.classList.add('strength-weak');
                if (textLabel) {
                    textLabel.textContent = 'Weak Password';
                    textLabel.style.color = '#ef4444';
                }
            } else if (score <= 4) {
                meterBar.classList.add('strength-fair');
                if (textLabel) {
                    textLabel.textContent = 'Medium Password';
                    textLabel.style.color = '#f59e0b';
                }
            } else if (score === 5) {
                meterBar.classList.add('strength-good');
                if (textLabel) {
                    textLabel.textContent = 'Strong Password';
                    textLabel.style.color = '#3b82f6';
                }
            } else {
                meterBar.classList.add('strength-strong');
                if (textLabel) {
                    textLabel.textContent = 'Very Secure';
                    textLabel.style.color = '#10b981';
                }
            }
        });
    });
}

/**
 * AJAX Listeners for administrative dashboard operations (Real-time updates!)
 */
function setupAdminControls() {
    // 1. AJAX toggle is_verified status switch
    const statusToggles = document.querySelectorAll('.user-status-toggle');
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', async () => {
            const userId = toggle.getAttribute('data-user-id');
            const status = toggle.checked ? 1 : 0;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            toggleLoader(true);
            
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('status', status);
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch(APP_URL + 'admin/user/toggle-status', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                toggleLoader(false);
                
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    const badge = document.getElementById(`status-badge-${userId}`);
                    if (badge) {
                        if (status === 1) {
                            badge.className = 'badge-verified';
                            badge.textContent = 'Verified';
                        } else {
                            badge.className = 'badge-unverified';
                            badge.textContent = 'Unverified';
                        }
                    }
                } else {
                    showToast(result.message, 'error');
                    toggle.checked = !toggle.checked;
                }
            } catch (err) {
                toggleLoader(false);
                toggle.checked = !toggle.checked;
                console.error('Toggle status error:', err);
                showToast('Failed to toggle verification state.', 'error');
            }
        });
    });
    
    // 2. Real-time Dropdown Role Switcher (No modal, changes immediately!)
    const roleSelects = document.querySelectorAll('.user-role-select');
    roleSelects.forEach(select => {
        select.addEventListener('change', async () => {
            const userId = select.getAttribute('data-user-id');
            const role = select.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            toggleLoader(true);
            
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('role', role);
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch(APP_URL + 'admin/user/update-role', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                toggleLoader(false);
                
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                } else {
                    showToast(result.message, 'error');
                    // Reset to previous value by checking actual session role or reloading
                    setTimeout(() => { window.location.reload(); }, 1200);
                }
            } catch (err) {
                toggleLoader(false);
                console.error('Update role error:', err);
                showToast('Failed to update role. Network error.', 'error');
            }
        });
    });
}

/**
 * Resend OTP code programmatically (AJAX call)
 */
async function resendOTP(emailVal) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    toggleLoader(true);
    
    try {
        const formData = new FormData();
        formData.append('email', emailVal);
        formData.append('csrf_token', csrfToken);
        
        const response = await fetch(APP_URL + 'verify-otp/resend', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        toggleLoader(false);
        
        if (result.status === 'success') {
            showToast(result.message, 'success');
            
            const resendBtn = document.getElementById('resend-otp-btn');
            if (resendBtn) {
                resendBtn.disabled = true;
                let counter = 60;
                
                const timer = setInterval(() => {
                    if (counter <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend OTP';
                    } else {
                        resendBtn.textContent = `Wait (${counter}s)`;
                        counter--;
                    }
                }, 1000);
            }
        } else {
            showToast(result.message, 'error');
        }
    } catch (err) {
        toggleLoader(false);
        showToast('Error requesting OTP resend.', 'error');
    }
}
