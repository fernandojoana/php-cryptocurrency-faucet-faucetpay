// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const addressInput = document.getElementById('address');
    const captchaInput = document.getElementById('captcha');
    
    // Real-time address validation
    addressInput.addEventListener('input', function() {
        const address = this.value.trim();
        if (address.length > 0) {
            this.classList.remove('is-invalid');
            // Add basic validation feedback
            if (address.length < 26) {
                this.classList.add('is-invalid');
            } else {
                this.classList.add('is-valid');
            }
        }
    });
    
    // CAPTCHA validation
    captchaInput.addEventListener('input', function() {
        const value = this.value.trim();
        if (value && !isNaN(value)) {
            this.classList.remove('is-invalid');
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const address = addressInput.value.trim();
        const captcha = captchaInput.value.trim();
        
        if (!address || !captcha) {
            e.preventDefault();
            if (!address) addressInput.classList.add('is-invalid');
            if (!captcha) captchaInput.classList.add('is-invalid');
        }
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Copy address functionality
    addressInput.addEventListener('click', function() {
        this.select();
    });
});

// Loading state for buttons
function setLoadingState(button, loading = true) {
    if (loading) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    } else {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-gift"></i> Reivindicar';
    }
}

// Update balance periodically (would require AJAX endpoint)
function updateBalance() {
    console.log('Balance update functionality would be implemented here');
}
