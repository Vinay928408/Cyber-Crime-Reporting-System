// Cyber Crime Reporting System - JavaScript

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password strength checker
    const passwordInputs = document.querySelectorAll('input[type="password"][id*="password"]');
    passwordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    });

    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            updateCharacterCounter(this);
        });
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            window.print();
        });
    });
});

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = '';

    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;

    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');

    if (strengthBar && strengthText) {
        strengthBar.className = 'progress-bar';
        
        switch(strength) {
            case 0:
            case 1:
                strengthBar.style.width = '20%';
                strengthBar.classList.add('bg-danger');
                feedback = 'Very Weak';
                break;
            case 2:
                strengthBar.style.width = '40%';
                strengthBar.classList.add('bg-warning');
                feedback = 'Weak';
                break;
            case 3:
                strengthBar.style.width = '60%';
                strengthBar.classList.add('bg-info');
                feedback = 'Medium';
                break;
            case 4:
                strengthBar.style.width = '80%';
                strengthBar.classList.add('bg-primary');
                feedback = 'Strong';
                break;
            case 5:
                strengthBar.style.width = '100%';
                strengthBar.classList.add('bg-success');
                feedback = 'Very Strong';
                break;
        }
        
        strengthBar.setAttribute('aria-valuenow', strength * 20);
        strengthText.textContent = feedback;
    }
}

// Character counter
function updateCharacterCounter(textarea) {
    const maxLength = textarea.getAttribute('maxlength');
    const currentLength = textarea.value.length;
    const counter = document.getElementById(textarea.id + '-counter');

    if (counter) {
        counter.textContent = `${currentLength} / ${maxLength}`;
        
        if (currentLength >= maxLength * 0.9) {
            counter.classList.add('text-danger');
        } else {
            counter.classList.remove('text-danger');
        }
    }
}

// AJAX form submission
function submitFormAjax(formId, successCallback, errorCallback) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            if (errorCallback) errorCallback(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (errorCallback) errorCallback({message: 'An error occurred. Please try again.'});
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.notification-container') || document.body;
    container.appendChild(notification);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Loading spinner
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="spinner"></div>';
    }
}

function hideLoading(elementId, content) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = content;
    }
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copied to clipboard!', 'success');
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        showNotification('Failed to copy to clipboard', 'danger');
    });
}

// Smooth scroll
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Filter table
function filterTable(tableId, inputId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');

    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(filter)) {
                    found = true;
                    break;
                }
            }

            rows[i].style.display = found ? '' : 'none';
        }
    });
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let rowData = [];

        for (let j = 0; j < cells.length; j++) {
            rowData.push('"' + cells[j].textContent + '"');
        }

        csv.push(rowData.join(','));
    }

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Print specific element
function printElement(elementId) {
    const element = document.getElementById(elementId);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="assets/css/style.css" rel="stylesheet">
            </head>
            <body>
                ${element.innerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate phone number
function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone) && phone.length >= 10;
}

// Get file size in human readable format
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Preview image before upload
function previewImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}
