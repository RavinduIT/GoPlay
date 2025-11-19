// Ground Owner - Reviews Management JavaScript
class ReviewsManager {
    constructor() {
        this.reviews = [];
        this.filteredReviews = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.filters = {
            rating: '',
            ground: '',
            status: '',
            sortBy: 'newest'
        };
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadReviews();
        this.updateOverview();
    }

    bindEvents() {
        // Filter events
        document.getElementById('ratingFilter')?.addEventListener('change', (e) => {
            this.filters.rating = e.target.value;
            this.applyFilters();
        });

        document.getElementById('groundFilter')?.addEventListener('change', (e) => {
            this.filters.ground = e.target.value;
            this.applyFilters();
        });

        document.getElementById('statusFilter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.applyFilters();
        });

        document.getElementById('sortBy')?.addEventListener('change', (e) => {
            this.filters.sortBy = e.target.value;
            this.applyFilters();
        });

        // Search
        document.getElementById('reviewSearch')?.addEventListener('input', (e) => {
            this.searchReviews(e.target.value);
        });

        // Time filter
        document.getElementById('timeFilter')?.addEventListener('change', (e) => {
            this.loadReviews(e.target.value);
        });

        // Response modal character counter
        document.getElementById('responseText')?.addEventListener('input', (e) => {
            document.getElementById('charCount').textContent = e.target.value.length;
        });

        // Load more button
        document.getElementById('loadMoreBtn')?.addEventListener('click', () => {
            this.loadMoreReviews();
        });

        // Global functions
        window.exportReviews = this.exportReviews.bind(this);
        window.closeResponseModal = this.closeResponseModal.bind(this);
        window.closeReportModal = this.closeReportModal.bind(this);
        window.submitResponse = this.submitResponse.bind(this);
        window.submitReport = this.submitReport.bind(this);
        window.loadMoreReviews = this.loadMoreReviews.bind(this);
    }

    async loadReviews(timeFilter = 'all') {
        try {
            const response = await fetch(`/api/ground-owner/reviews?period=${timeFilter}`);
            if (response.ok) {
                const data = await response.json();
                this.reviews = data.reviews || [];
                this.filteredReviews = [...this.reviews];
                this.renderReviews();
                this.updateOverview();
                this.updateRatingDistribution();
                this.populateGroundFilter();
            }
        } catch (error) {
            console.error('Error loading reviews:', error);
            this.showToast('Failed to load reviews', 'error');
        }
    }

    updateOverview() {
        const stats = this.calculateStats();

        // Safely update elements with null checks
        const setTextContent = (id, value) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        };

        setTextContent('overallRating', stats.averageRating.toFixed(1));
        setTextContent('totalReviews', `Based on ${stats.totalReviews} reviews`);
        setTextContent('reviewsCount', stats.totalReviews);
        setTextContent('pendingResponses', stats.pendingResponses);
        setTextContent('recentCount', stats.recentReviews);

        // Update growth indicator
        const growthElement = document.getElementById('reviewsGrowth');
        if (growthElement) {
            growthElement.textContent = `${stats.growth >= 0 ? '+' : ''}${stats.growth}%`;
            growthElement.parentElement.className = `stat-change ${stats.growth >= 0 ? 'positive' : 'negative'}`;
        }

        // Update overall stars display
        this.updateStarsDisplay('overallStars', stats.averageRating);
    }

    calculateStats() {
        const now = new Date();
        const thisMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

        const thisMonthReviews = this.reviews.filter(r => new Date(r.created_at) >= thisMonth);
        const lastMonthReviews = this.reviews.filter(r => {
            const date = new Date(r.created_at);
            return date >= lastMonth && date < thisMonth;
        });
        const recentReviews = this.reviews.filter(r => new Date(r.created_at) >= weekAgo);

        const totalRating = this.reviews.reduce((sum, r) => sum + r.rating, 0);
        const averageRating = this.reviews.length > 0 ? totalRating / this.reviews.length : 0;
        
        const pendingResponses = this.reviews.filter(r => !r.response).length;
        
        const growth = lastMonthReviews.length > 0 
            ? ((thisMonthReviews.length - lastMonthReviews.length) / lastMonthReviews.length * 100)
            : 0;

        return {
            totalReviews: this.reviews.length,
            averageRating,
            pendingResponses,
            recentReviews: recentReviews.length,
            growth: Math.round(growth)
        };
    }

    updateRatingDistribution() {
        const distribution = [0, 0, 0, 0, 0]; // Index 0 = 1 star, Index 4 = 5 stars
        
        this.reviews.forEach(review => {
            if (review.rating >= 1 && review.rating <= 5) {
                distribution[review.rating - 1]++;
            }
        });

        const total = this.reviews.length || 1;
        
        for (let i = 1; i <= 5; i++) {
            const count = distribution[i - 1];
            const percentage = (count / total) * 100;
            
            document.getElementById(`rating${i}Bar`).style.width = `${percentage}%`;
            document.getElementById(`rating${i}Count`).textContent = count;
        }
    }

    renderReviews() {
        const reviewsList = document.getElementById('reviewsList');
        if (!reviewsList) return;

        if (this.filteredReviews.length === 0) {
            reviewsList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h3>No reviews found</h3>
                    <p>No reviews match your current filters.</p>
                </div>
            `;
            return;
        }

        // Group reviews by facility
        const groupedReviews = this.groupReviewsByFacility(this.filteredReviews);

        // Render grouped reviews
        reviewsList.innerHTML = Object.entries(groupedReviews).map(([facilityName, facilityData]) =>
            this.createFacilitySection(facilityName, facilityData)
        ).join('');

        // Hide load more button when using grouped view
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.style.display = 'none';
        }
    }

    groupReviewsByFacility(reviews) {
        const grouped = {};

        reviews.forEach(review => {
            const facilityKey = review.facility_name || review.ground_name;

            if (!grouped[facilityKey]) {
                grouped[facilityKey] = {
                    facility_id: review.facility_id || review.ground_id,
                    facility_name: review.facility_name || review.ground_name,
                    reviews: [],
                    averageRating: 0,
                    totalReviews: 0
                };
            }

            grouped[facilityKey].reviews.push(review);
        });

        // Calculate stats for each facility
        Object.values(grouped).forEach(facility => {
            facility.totalReviews = facility.reviews.length;
            const totalRating = facility.reviews.reduce((sum, r) => sum + r.rating, 0);
            facility.averageRating = totalRating / facility.totalReviews;

            // Sort reviews by date (newest first)
            facility.reviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        });

        return grouped;
    }

    createFacilitySection(facilityName, facilityData) {
        const sectionId = `facility-${facilityData.facility_id}`;
        const isExpanded = true; // Default expanded

        return `
            <div class="facility-reviews-section">
                <div class="facility-header" onclick="reviewsManager.toggleFacilitySection('${sectionId}')">
                    <div class="facility-info">
                        <h3 class="facility-name">
                            <i class="fas fa-building"></i>
                            ${facilityData.facility_name}
                        </h3>
                        <div class="facility-stats">
                            <span class="facility-rating">
                                <i class="fas fa-star"></i>
                                ${facilityData.averageRating.toFixed(1)}
                            </span>
                            <span class="facility-review-count">
                                ${facilityData.totalReviews} review${facilityData.totalReviews !== 1 ? 's' : ''}
                            </span>
                        </div>
                    </div>
                    <div class="facility-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="facility-reviews-content" id="${sectionId}" style="display: ${isExpanded ? 'block' : 'none'}">
                    ${facilityData.reviews.map(review => this.createReviewCard(review)).join('')}
                </div>
            </div>
        `;
    }

    toggleFacilitySection(sectionId) {
        const section = document.getElementById(sectionId);
        const header = section.previousElementSibling;
        const chevron = header.querySelector('.facility-toggle i');

        if (section.style.display === 'none') {
            section.style.display = 'block';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            section.style.display = 'none';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    createReviewCard(review) {
        const hasResponse = review.response && review.response.text;
        const statusClass = hasResponse ? 'responded' : 'pending';
        const customerName = review.customer_name || `${review.first_name || ''} ${review.last_name || ''}`.trim() || 'Anonymous';
        const customerAvatar = review.customer_avatar || review.profile_picture || '/public/assets/images/default-avatar.jpg';

        return `
            <div class="review-card rating-${review.rating}">
                <div class="review-header">
                    <div class="reviewer-info">
                        <img src="${customerAvatar}"
                             alt="Reviewer" class="reviewer-avatar">
                        <div class="reviewer-details">
                            <h5>${customerName}</h5>
                            <div class="review-rating">
                                ${this.generateStars(review.rating)}
                            </div>
                            <div class="review-meta">
                                <span class="review-date">${this.formatDate(review.created_at)}</span>
                                ${review.booking_date ? `<span>Booking: ${this.formatDate(review.booking_date)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="review-actions">
                        ${!hasResponse ? `
                            <button class="review-action-btn respond" onclick="reviewsManager.openResponseModal(${review.id})" title="Respond">
                                <i class="fas fa-reply"></i>
                            </button>
                        ` : ''}
                        <button class="review-action-btn report" onclick="reviewsManager.openReportModal(${review.id})" title="Report">
                            <i class="fas fa-flag"></i>
                        </button>
                    </div>
                </div>
                <div class="review-content">
                    <p class="review-text">${review.review_text || 'No review text provided.'}</p>
                    ${review.images && review.images.length > 0 ? `
                        <div class="review-images">
                            ${review.images.map(img => `
                                <img src="${img}" alt="Review image" class="review-image"
                                     onclick="reviewsManager.viewImage('${img}')">
                            `).join('')}
                        </div>
                    ` : ''}
                    ${hasResponse ? `
                        <div class="review-response">
                            <div class="response-header">
                                <i class="fas fa-reply"></i>
                                <span>Your Response</span>
                            </div>
                            <p class="response-text">${review.response.text}</p>
                            <div class="response-date">${this.formatDate(review.response.created_at)}</div>
                        </div>
                    ` : ''}
                </div>
                <div class="review-status">
                    <span class="status-indicator ${statusClass}">
                        ${hasResponse ? 'Responded' : 'Pending Response'}
                    </span>
                </div>
            </div>
        `;
    }

    generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star${i <= rating ? '' : ' far'}"></i>`;
        }
        return stars;
    }

    updateStarsDisplay(elementId, rating) {
        const element = document.getElementById(elementId);
        if (!element) return;

        element.innerHTML = this.generateStars(Math.round(rating));
    }

    applyFilters() {
        this.filteredReviews = this.reviews.filter(review => {
            let matches = true;

            if (this.filters.rating && review.rating !== parseInt(this.filters.rating)) {
                matches = false;
            }

            if (this.filters.ground) {
                const facilityId = review.facility_id || review.ground_id;
                if (facilityId !== parseInt(this.filters.ground)) {
                    matches = false;
                }
            }

            if (this.filters.status) {
                const hasResponse = review.response && review.response.text;
                if (this.filters.status === 'responded' && !hasResponse) matches = false;
                if (this.filters.status === 'pending' && hasResponse) matches = false;
                if (this.filters.status === 'flagged' && !review.flagged) matches = false;
            }

            return matches;
        });

        this.sortReviews();
        this.currentPage = 1;
        this.renderReviews();
    }

    sortReviews() {
        this.filteredReviews.sort((a, b) => {
            switch (this.filters.sortBy) {
                case 'newest':
                    return new Date(b.created_at) - new Date(a.created_at);
                case 'oldest':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'highest':
                    return b.rating - a.rating;
                case 'lowest':
                    return a.rating - b.rating;
                default:
                    return 0;
            }
        });
    }

    searchReviews(query) {
        if (!query.trim()) {
            this.filteredReviews = [...this.reviews];
        } else {
            this.filteredReviews = this.reviews.filter(review => {
                const reviewText = review.review_text || '';
                const customerName = `${review.first_name || ''} ${review.last_name || ''}`.trim() || review.customer_name || '';
                const facilityName = review.facility_name || review.ground_name || '';

                return reviewText.toLowerCase().includes(query.toLowerCase()) ||
                       customerName.toLowerCase().includes(query.toLowerCase()) ||
                       facilityName.toLowerCase().includes(query.toLowerCase());
            });
        }

        this.currentPage = 1;
        this.renderReviews();
    }

    loadMoreReviews() {
        this.currentPage++;
        this.renderReviews();
    }

    populateGroundFilter() {
        const groundFilter = document.getElementById('groundFilter');
        if (!groundFilter) return;

        // Create unique facilities map
        const facilitiesMap = new Map();
        this.reviews.forEach(r => {
            const facilityId = r.facility_id || r.ground_id;
            const facilityName = r.facility_name || r.ground_name;
            if (!facilitiesMap.has(facilityId)) {
                facilitiesMap.set(facilityId, facilityName);
            }
        });

        // Clear existing options except first one
        groundFilter.innerHTML = '<option value="">All Grounds</option>';

        // Add facilities as options
        facilitiesMap.forEach((name, id) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            groundFilter.appendChild(option);
        });
    }

    openResponseModal(reviewId) {
        const review = this.reviews.find(r => r.id === reviewId);
        if (!review) return;

        // Populate modal with review data
        const customerName = review.customer_name || `${review.first_name || ''} ${review.last_name || ''}`.trim() || 'Anonymous';
        const customerAvatar = review.customer_avatar || review.profile_picture || '/public/assets/images/default-avatar.jpg';

        document.getElementById('modalReviewerAvatar').src = customerAvatar;
        document.getElementById('modalReviewerName').textContent = customerName;
        document.getElementById('modalReviewRating').innerHTML = this.generateStars(review.rating);
        document.getElementById('modalReviewDate').textContent = this.formatDate(review.created_at);
        document.getElementById('modalReviewText').textContent = review.review_text || 'No review text provided.';

        // Clear response text and reset character count
        document.getElementById('responseText').value = '';
        document.getElementById('charCount').textContent = '0';

        // Store current review ID
        this.currentReviewId = reviewId;

        document.getElementById('responseModal').classList.add('active');
    }

    closeResponseModal() {
        document.getElementById('responseModal').classList.remove('active');
        this.currentReviewId = null;
    }

    async submitResponse() {
        const responseText = document.getElementById('responseText').value.trim();
        
        if (!responseText) {
            this.showToast('Please enter a response', 'error');
            return;
        }

        if (responseText.length > 500) {
            this.showToast('Response must be 500 characters or less', 'error');
            return;
        }

        try {
            const response = await fetch(`/api/ground-owner/reviews/${this.currentReviewId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ response: responseText })
            });

            if (response.ok) {
                this.showToast('Response submitted successfully', 'success');
                this.closeResponseModal();
                this.loadReviews();
            } else {
                throw new Error('Failed to submit response');
            }
        } catch (error) {
            console.error('Error submitting response:', error);
            this.showToast('Failed to submit response', 'error');
        }
    }

    openReportModal(reviewId) {
        this.currentReviewId = reviewId;
        
        // Clear form
        document.querySelectorAll('input[name="reportReason"]').forEach(input => {
            input.checked = false;
        });
        document.getElementById('reportDetails').value = '';
        
        document.getElementById('reportModal').classList.add('active');
    }

    closeReportModal() {
        document.getElementById('reportModal').classList.remove('active');
        this.currentReviewId = null;
    }

    async submitReport() {
        const reason = document.querySelector('input[name="reportReason"]:checked')?.value;
        const details = document.getElementById('reportDetails').value.trim();
        
        if (!reason) {
            this.showToast('Please select a reason for reporting', 'error');
            return;
        }

        try {
            const response = await fetch(`/api/ground-owner/reviews/${this.currentReviewId}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason, details })
            });

            if (response.ok) {
                this.showToast('Review reported successfully', 'success');
                this.closeReportModal();
            } else {
                throw new Error('Failed to report review');
            }
        } catch (error) {
            console.error('Error reporting review:', error);
            this.showToast('Failed to report review', 'error');
        }
    }

    viewImage(imageSrc) {
        // Create image modal
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            cursor: pointer;
        `;
        
        const img = document.createElement('img');
        img.src = imageSrc;
        img.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        `;
        
        modal.appendChild(img);
        document.body.appendChild(modal);
        
        modal.addEventListener('click', () => {
            document.body.removeChild(modal);
        });
    }

    exportReviews() {
        const csvContent = this.generateReviewsCSV();
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reviews-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    generateReviewsCSV() {
        const headers = ['Review ID', 'Customer', 'Ground', 'Rating', 'Review Text', 'Date', 'Response Status'];
        const rows = this.filteredReviews.map(review => {
            const customerName = review.customer_name || `${review.first_name || ''} ${review.last_name || ''}`.trim() || 'Anonymous';
            const facilityName = review.facility_name || review.ground_name || 'Unknown';
            const reviewText = (review.review_text || '').replace(/"/g, '""'); // Escape quotes

            return [
                review.id,
                customerName,
                facilityName,
                review.rating,
                reviewText,
                review.created_at,
                review.response && review.response.text ? 'Responded' : 'Pending'
            ];
        });

        return [headers, ...rows].map(row =>
            row.map(field => `"${field}"`).join(',')
        ).join('\n');
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        
        toast.style.cssText = `
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        
        container.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => container.removeChild(toast), 300);
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.reviewsManager = new ReviewsManager();
});

// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    sidebar.classList.toggle('collapsed');
}