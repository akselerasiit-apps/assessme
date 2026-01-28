/**
 * COBIT Assessment Application Utilities
 */

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-important)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Check if Bootstrap is available
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                // Fallback: just hide the alert
                alert.style.display = 'none';
            }
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
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 0.625rem;
        padding: 0.25rem 0.4rem;
        border-radius: 10px;
    }
`;
document.head.appendChild(style);

// ===== NOTIFICATIONS SYSTEM =====
let notificationCheckInterval;

// Initialize notifications when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeNotifications();
    
    // Mark all as read handler
    document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        markAllNotificationsAsRead();
    });
});

function initializeNotifications() {
    // Load notifications initially
    loadNotifications();
    
    // Check for new notifications every 30 seconds
    notificationCheckInterval = setInterval(checkNotifications, 30000);
}

function checkNotifications() {
    fetch('/api/notifications/unread-count', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        updateNotificationBadge(data.count);
    })
    .catch(error => console.error('Error checking notifications:', error));
}

function loadNotifications() {
    fetch('/api/notifications?per_page=10', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        displayNotifications(data.data);
        updateNotificationBadge(data.data.filter(n => !n.is_read).length);
    })
    .catch(error => console.error('Error loading notifications:', error));
}

function displayNotifications(notifications) {
    const listContainer = document.getElementById('notification-list');
    
    if (!notifications || notifications.length === 0) {
        listContainer.innerHTML = `
            <div class="list-group-item text-center text-muted py-5">
                <i class="ti ti-bell-off icon mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mb-0">No notifications</p>
            </div>
        `;
        return;
    }
    
    listContainer.innerHTML = notifications.map(notification => {
        const unreadClass = notification.is_read ? '' : 'bg-light';
        const timeAgo = formatTimeAgo(notification.created_at);
        const assessmentLink = notification.assessment_id 
            ? `/assessments/${notification.assessment_id}` 
            : '#';
        
        return `
            <a href="${assessmentLink}" class="list-group-item list-group-item-action ${unreadClass}" 
               onclick="markNotificationAsRead(${notification.id}, event)">
                <div class="d-flex align-items-start">
                    <div class="flex-fill">
                        <div class="fw-bold">${notification.title}</div>
                        <div class="text-muted small mt-1">${notification.message}</div>
                        <div class="text-muted small mt-1">
                            <i class="ti ti-clock"></i> ${timeAgo}
                        </div>
                    </div>
                    ${!notification.is_read ? '<span class="badge bg-blue ms-2">New</span>' : ''}
                </div>
            </a>
        `;
    }).join('');
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-count');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

function markNotificationAsRead(notificationId, event) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(() => {
        // Reload notifications to update UI
        setTimeout(() => loadNotifications(), 100);
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllNotificationsAsRead() {
    fetch('/api/notifications/read-all', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(() => {
        showToast('All notifications marked as read', 'success');
        loadNotifications();
    })
    .catch(error => console.error('Error marking all as read:', error));
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
        }
    }
    
    return 'Just now';
}
