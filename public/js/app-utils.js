/**
 * COBIT Assessment Application Utilities
 */

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-important)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Loading button state
function setButtonLoading(button, loading = true) {
    if (loading) {
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml || button.innerHTML;
    }
}

// Confirm delete with better UX
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    setTimeout(() => {
        toastElement.remove();
    }, 5000);
}

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    return true;
}

// Empty state helper
function showEmptyState(containerId, message = 'No data available', icon = 'inbox') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = `
        <div class="empty">
            <div class="empty-icon">
                <i class="ti ti-${icon} icon" style="font-size: 48px;"></i>
            </div>
            <p class="empty-title">${message}</p>
            <p class="empty-subtitle text-muted">
                Try adjusting your filters or creating a new item.
            </p>
        </div>
    `;
}

// DataTable helper
function initDataTable(tableId, options = {}) {
    const defaultOptions = {
        pageLength: 15,
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    };
    
    return $(tableId).DataTable({...defaultOptions, ...options});
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!', 'success');
    }, function(err) {
        showToast('Failed to copy', 'error');
    });
}

// Format date
function formatDate(date, format = 'DD MMM YYYY') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = d.toLocaleString('en-US', { month: 'short' });
    const year = d.getFullYear();
    
    if (format === 'DD MMM YYYY') {
        return `${day} ${month} ${year}`;
    }
    return d.toLocaleDateString();
}

// Debounce function for search inputs
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scroll to element
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Loading overlay
function showLoadingOverlay(show = true) {
    let overlay = document.getElementById('loading-overlay');
    
    if (show) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    } else {
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
}

// Add loading overlay styles
const style = document.createElement('style');
style.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    
    .toast-container {
        z-index: 10000;
    }
    
    .empty {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-icon {
        color: var(--tblr-secondary);
        margin-bottom: 1rem;
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-subtitle {
        font-size: 0.875rem;
    }
`;
document.head.appendChild(style);
