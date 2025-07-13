// Enhanced JavaScript for Patient Module

document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(function(field) {
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.textContent = '👁️';
        toggleBtn.style.marginLeft = '5px';
        toggleBtn.style.background = 'none';
        toggleBtn.style.border = 'none';
        toggleBtn.style.cursor = 'pointer';
        
        toggleBtn.addEventListener('click', function() {
            if (field.type === 'password') {
                field.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                field.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        });
        field.parentNode.insertBefore(toggleBtn, field.nextSibling);
    });

    // Form validation for login
    const loginForm = document.querySelector('form[method="POST"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]');
            const password = document.querySelector('input[name="password"]');
            if (email && password) {
                if (!email.value.includes('@')) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
                if (password.value.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    return false;
                }
            }
        });
    }

    // Success message auto-hide
    const successMessages = document.querySelectorAll('.success');
    successMessages.forEach(function(msg) {
        setTimeout(function() {
            msg.style.opacity = '0.7';
        }, 3000);
    });
}); 