// Enhanced JavaScript for Admin Module
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JavaScript loaded successfully!');
    
    // Delete confirmation for all elements with class 'delete-btn'
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });

    // Form validation for add_user.php
    const addUserForm = document.querySelector('form[method="POST"]');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('Passwords do not match!');
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

    // Table filtering for activity_logs.php
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Search input detected:', this.value);
        });
    }

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

    // Success message auto-hide
    const successMessages = document.querySelectorAll('.success');
    successMessages.forEach(function(msg) {
        setTimeout(function() {
            msg.style.opacity = '0.7';
        }, 3000);
    });

    console.log('All JavaScript features initialized!');
});