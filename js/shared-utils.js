// Shared utility functions for SportHub application
// This file consolidates common functions used across multiple pages

class Utils {
    // Validation utilities
    static validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static validatePhone(phone) {
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
    }

    static validateRequired(value, fieldName) {
        if (!value || value.toString().trim() === '') {
            return { isValid: false, error: `${fieldName} is required` };
        }
        return { isValid: true };
    }

    static validateMinLength(value, minLength, fieldName) {
        if (value && value.toString().length < minLength) {
            return { isValid: false, error: `${fieldName} must be at least ${minLength} characters` };
        }
        return { isValid: true };
    }

    // String utilities
    static capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    static truncateText(text, maxLength, suffix = '...') {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + suffix;
    }

    static slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    static formatName(firstName, lastName) {
        const first = firstName ? firstName.trim() : '';
        const last = lastName ? lastName.trim() : '';
        return `${first} ${last}`.trim();
    }

    // Number and currency utilities
    static formatPrice(price, currency = '$') {
        if (isNaN(price)) return 'N/A';
        return `${currency}${parseFloat(price).toFixed(2)}`;
    }

    static formatRating(rating, maxRating = 5) {
        if (isNaN(rating)) return '0.0';
        const clampedRating = Math.max(0, Math.min(maxRating, rating));
        return clampedRating.toFixed(1);
    }

    static generateStars(rating, maxRating = 5) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = maxRating - fullStars - (hasHalfStar ? 1 : 0);

        return {
            full: fullStars,
            half: hasHalfStar ? 1 : 0,
            empty: emptyStars,
            html: '★'.repeat(fullStars) + (hasHalfStar ? '☆' : '') + '☆'.repeat(emptyStars)
        };
    }

    // Date utilities
    static formatDate(date, format = 'YYYY-MM-DD') {
        if (!date) return '';
        
        const d = new Date(date);
        if (isNaN(d.getTime())) return '';

        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');

        switch (format) {
            case 'YYYY-MM-DD':
                return `${year}-${month}-${day}`;
            case 'DD/MM/YYYY':
                return `${day}/${month}/${year}`;
            case 'MM/DD/YYYY':
                return `${month}/${day}/${year}`;
            case 'readable':
                return d.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            default:
                return d.toISOString().split('T')[0];
        }
    }

    static formatTime(time) {
        if (!time) return '';
        
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        
        return `${displayHour}:${minutes} ${ampm}`;
    }

    static getTimeFromNow(date) {
        if (!date) return '';
        
        const now = new Date();
        const targetDate = new Date(date);
        const diffMs = targetDate - now;
        const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Tomorrow';
        if (diffDays === -1) return 'Yesterday';
        if (diffDays > 1) return `In ${diffDays} days`;
        if (diffDays < -1) return `${Math.abs(diffDays)} days ago`;
        
        return this.formatDate(date, 'readable');
    }

    // DOM utilities
    static createElement(tag, className = '', innerHTML = '') {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (innerHTML) element.innerHTML = innerHTML;
        return element;
    }

    static addEventListeners(element, events) {
        if (!element || !events) return;
        
        Object.keys(events).forEach(eventType => {
            element.addEventListener(eventType, events[eventType]);
        });
    }

    static toggleClass(element, className, force = null) {
        if (!element || !className) return;
        
        if (force !== null) {
            element.classList.toggle(className, force);
        } else {
            element.classList.toggle(className);
        }
    }

    static debounce(func, wait, immediate = false) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }

    static throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Image utilities
    static handleImageError(img, fallbackSrc = 'assets/images/placeholder.jpg') {
        if (!img) return;
        
        img.onerror = function() {
            this.onerror = null; // Prevent infinite loop
            this.src = fallbackSrc;
            this.alt = 'Image not available';
        };
    }

    static preloadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = src;
        });
    }

    // Storage utilities
    static saveToStorage(key, data, useSessionStorage = false) {
        try {
            const storage = useSessionStorage ? sessionStorage : localStorage;
            storage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            console.error('Storage save error:', error);
            return false;
        }
    }

    static getFromStorage(key, useSessionStorage = false) {
        try {
            const storage = useSessionStorage ? sessionStorage : localStorage;
            const data = storage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (error) {
            console.error('Storage read error:', error);
            return null;
        }
    }

    static removeFromStorage(key, useSessionStorage = false) {
        try {
            const storage = useSessionStorage ? sessionStorage : localStorage;
            storage.removeItem(key);
            return true;
        } catch (error) {
            console.error('Storage remove error:', error);
            return false;
        }
    }

    // URL utilities
    static getQueryParams() {
        const params = {};
        const urlParams = new URLSearchParams(window.location.search);
        for (const [key, value] of urlParams.entries()) {
            params[key] = value;
        }
        return params;
    }

    static getQueryParam(key, defaultValue = null) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key) || defaultValue;
    }

    static updateQueryParam(key, value) {
        const url = new URL(window.location);
        if (value === null || value === undefined || value === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
        window.history.pushState({}, '', url);
    }

    // Animation utilities
    static animateValue(element, start, end, duration, callback = null) {
        if (!element) return;
        
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const value = start + (end - start) * progress;
            element.textContent = Math.round(value);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else if (callback) {
                callback();
            }
        };
        
        requestAnimationFrame(animate);
    }

    static fadeIn(element, duration = 300) {
        if (!element) return;
        
        element.style.opacity = '0';
        element.style.display = 'block';
        
        const start = performance.now();
        
        const fade = (timestamp) => {
            const elapsed = timestamp - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = progress;
            
            if (progress < 1) {
                requestAnimationFrame(fade);
            }
        };
        
        requestAnimationFrame(fade);
    }

    static fadeOut(element, duration = 300, callback = null) {
        if (!element) return;
        
        const start = performance.now();
        const startOpacity = parseFloat(getComputedStyle(element).opacity) || 1;
        
        const fade = (timestamp) => {
            const elapsed = timestamp - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = startOpacity * (1 - progress);
            
            if (progress < 1) {
                requestAnimationFrame(fade);
            } else {
                element.style.display = 'none';
                if (callback) callback();
            }
        };
        
        requestAnimationFrame(fade);
    }

    // Form utilities
    static serializeForm(form) {
        if (!form) return {};
        
        const formData = new FormData(form);
        const data = {};
        
        for (const [key, value] of formData.entries()) {
            if (data[key]) {
                // Handle multiple values (like checkboxes)
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        return data;
    }

    static resetForm(form) {
        if (!form) return;
        
        form.reset();
        
        // Clear validation classes
        form.querySelectorAll('.error, .success').forEach(element => {
            element.classList.remove('error', 'success');
        });
        
        // Clear error messages
        form.querySelectorAll('.field-error').forEach(error => {
            error.remove();
        });
    }

    // Array utilities
    static shuffle(array) {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }

    static groupBy(array, key) {
        return array.reduce((groups, item) => {
            const group = item[key];
            groups[group] = groups[group] || [];
            groups[group].push(item);
            return groups;
        }, {});
    }

    static sortBy(array, key, direction = 'asc') {
        return [...array].sort((a, b) => {
            const aVal = a[key];
            const bVal = b[key];
            
            if (aVal < bVal) return direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return direction === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // Error handling utilities
    static createErrorMessage(error, fallbackMessage = 'An error occurred') {
        if (typeof error === 'string') return error;
        if (error && error.message) return error.message;
        return fallbackMessage;
    }

    static logError(error, context = '') {
        const timestamp = new Date().toISOString();
        const message = this.createErrorMessage(error);
        console.error(`[${timestamp}] ${context}: ${message}`, error);
    }

    // Loading state utilities
    static showLoading(element, text = 'Loading...') {
        if (!element) return;
        
        element.classList.add('loading');
        element.disabled = true;
        
        const originalContent = element.innerHTML;
        element.dataset.originalContent = originalContent;
        element.innerHTML = `<span class="spinner"></span>${text}`;
    }

    static hideLoading(element) {
        if (!element) return;
        
        element.classList.remove('loading');
        element.disabled = false;
        
        const originalContent = element.dataset.originalContent;
        if (originalContent) {
            element.innerHTML = originalContent;
            delete element.dataset.originalContent;
        }
    }
}

// Make Utils available globally
window.Utils = Utils;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Utils;
}