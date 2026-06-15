document.addEventListener('DOMContentLoaded', function() {
    var backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    document.querySelectorAll('.qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('.qty-input');
            var val = parseInt(input.value);
            if (val > 1) input.value = val - 1;
        });
    });

    document.querySelectorAll('.qty-plus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('.qty-input');
            var val = parseInt(input.value);
            if (val < 99) input.value = val + 1;
        });
    });

    document.querySelectorAll('.needs-validation').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    document.querySelectorAll('.confirm-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) e.preventDefault();
        });
    });

    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    var passwordInput = document.getElementById('regPassword');
    var strengthBar = document.getElementById('passwordStrength');
    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function() {
            var val = this.value;
            var strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;
            var colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745'];
            var widths = ['25%', '50%', '75%', '100%'];
            strengthBar.style.width = widths[strength - 1] || '0%';
            strengthBar.style.backgroundColor = colors[strength - 1] || '#dc3545';
        });
    }
});

function updateCartQuantity(cartId, newQty) {
    if (newQty < 1) return;
    window.location.href = 'cart.php?action=update&cart_id=' + cartId + '&quantity=' + newQty;
}

function confirmOrder() {
    return confirm('Are you sure you want to place this order?');
}

function printOrder() {
    window.print();
}
