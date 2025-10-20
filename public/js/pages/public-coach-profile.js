// Public Coach Profile JavaScript - Static Display

// Page is ready - no dynamic loading needed
document.addEventListener('DOMContentLoaded', () => {
    console.log('Coach profile loaded');
});

// Book coach function
function booksession() {
    // Check if user is logged in
    fetch('/auth/check')
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                // Redirect to login with return URL
                const currentUrl = window.location.pathname;
                window.location.href = `/login?redirect=${encodeURIComponent(currentUrl)}`;
                return;
            }

            // Redirect to booking page
            window.location.href = `/app/views/booking/book-session.php`;
        })
        .catch(error => {
            console.error('Error checking authentication:', error);
            // Redirect to login as fallback
            window.location.href = '/login';
        });
}

// Share profile function
function shareProfile() {
    const url = window.location.href;
    const coachName = document.getElementById('coachName').textContent;
    const title = `Check out ${coachName}'s coaching profile on GoPlay!`;

    if (navigator.share) {
        // Use Web Share API if available
        navigator.share({
            title: title,
            text: title,
            url: url
        }).catch(err => console.error('Error sharing:', err));
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Profile link copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Error copying to clipboard:', err);
            showNotification('Failed to copy link', 'error');
        });
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    notification.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-content i {
        font-size: 1.25rem;
    }
`;
document.head.appendChild(style);
