(function() {
    'use strict';

    let currentApplicationId = null;
    let currentPage = 1;
    const itemsPerPage = 10;

    // Load applications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        loadApplications();

    // Add filter event listeners
    document.getElementById('statusFilter').addEventListener('change', loadApplications);
    document.getElementById('typeFilter').addEventListener('change', loadApplications);

    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loadApplications();
        }
    });
});

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/provider-applications/statistics');
        const data = await response.json();

        if (data.success) {
            document.getElementById('pendingCount').textContent = data.stats.pending || 0;
            document.getElementById('approvedCount').textContent = data.stats.approved_today || 0;
            document.getElementById('rejectedCount').textContent = data.stats.rejected || 0;
            document.getElementById('totalCount').textContent = data.stats.total || 0;
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Load applications
async function loadApplications(page = 1) {
    currentPage = page;

    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const search = document.getElementById('searchInput').value;

    const params = new URLSearchParams({
        page: page,
        limit: itemsPerPage,
        status: status,
        type: type,
        search: search
    });

    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/provider-applications/list?${params}`);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', response.status, errorText);
            showError(`Server error: ${response.status}`);
            return;
        }

        const data = await response.json();

        if (data.success) {
            renderApplicationsTable(data.applications);
            renderPagination(data.pagination);
        } else {
            console.error('API error:', data);
            showError(data.message || 'Failed to load applications');
        }
    } catch (error) {
        console.error('Error loading applications:', error);
        showError('An error occurred while loading applications');
    }
}

// Render applications table
function renderApplicationsTable(applications) {
    const tbody = document.getElementById('applicationsTableBody');

    if (applications.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="loading-row">
                    <p>No applications found</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = applications.map(app => `
        <tr>
            <td>#${app.id}</td>
            <td>
                <div>
                    <strong>${app.first_name} ${app.last_name}</strong><br>
                    <small style="color: #6b7280;">${app.email}</small>
                </div>
            </td>
            <td>
                <span class="type-badge">${formatProviderType(app.provider_type)}</span>
            </td>
            <td>${formatDate(app.created_at)}</td>
            <td>
                <span class="status-badge ${app.status}">${app.status}</span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-view" onclick="viewApplication(${app.id})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    ${app.status === 'pending' ? `
                        <button class="btn-approve-inline" onclick="quickApprove(${app.id})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-reject-inline" onclick="quickReject(${app.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

// Render pagination
function renderPagination(pagination) {
    const paginationDiv = document.getElementById('pagination');

    if (pagination.total_pages <= 1) {
        paginationDiv.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<button class="page-btn" onclick="loadApplications(${pagination.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }

    // Page numbers
    for (let i = 1; i <= pagination.total_pages; i++) {
        if (
            i === 1 ||
            i === pagination.total_pages ||
            (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
        ) {
            html += `<button class="page-btn ${i === pagination.current_page ? 'active' : ''}"
                     onclick="loadApplications(${i})">${i}</button>`;
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<span style="padding: 8px;">...</span>';
        }
    }

    // Next button
    if (pagination.current_page < pagination.total_pages) {
        html += `<button class="page-btn" onclick="loadApplications(${pagination.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }

    paginationDiv.innerHTML = html;
}

// View application details
async function viewApplication(id) {
    currentApplicationId = id;

    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/provider-applications/details/${id}`);
        const data = await response.json();

        if (data.success) {
            renderApplicationDetails(data.application);
            document.getElementById('applicationModal').classList.add('active');
        } else {
            showError('Failed to load application details');
        }
    } catch (error) {
        console.error('Error loading application details:', error);
        showError('An error occurred while loading details');
    }
}

// Render application details
function renderApplicationDetails(app) {
    const detailsDiv = document.getElementById('applicationDetails');

    let html = `
        <div class="detail-section">
            <h4>Personal Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">${app.first_name} ${app.last_name}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${app.email}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">${app.phone}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">City</div>
                    <div class="detail-value">${app.city}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Address</div>
                <div class="detail-value">${app.address}</div>
            </div>
        </div>
    `;

    // Add provider-specific details
    if (app.provider_type === 'ground_owner') {
        html += renderGroundOwnerDetails(app);
    } else if (app.provider_type === 'coach') {
        html += renderCoachDetails(app);
    } else if (app.provider_type === 'shop_owner') {
        html += renderShopOwnerDetails(app);
    }

    // Add documents section
    html += `
        <div class="detail-section">
            <h4>Documents</h4>
            <div class="detail-grid">
                ${app.nic_document ? `
                    <div class="detail-item">
                        <div class="detail-label">NIC Document</div>
                        <div class="detail-value">
                            <a href="${window.BASE_URL||''}/public/uploads/provider-applications/${app.nic_document}" target="_blank">
                                <i class="fas fa-file-download"></i> View Document
                            </a>
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    detailsDiv.innerHTML = html;
}

// Render ground owner details
function renderGroundOwnerDetails(app) {
    return `
        <div class="detail-section">
            <h4>Facility Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Facility Name</div>
                    <div class="detail-value">${app.facility_name || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Number of Courts</div>
                    <div class="detail-value">${app.number_of_courts || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Hourly Rate</div>
                    <div class="detail-value">LKR ${app.proposed_hourly_rate || 'N/A'}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Description</div>
                <div class="detail-value">${app.facility_description || 'N/A'}</div>
            </div>
        </div>
    `;
}

// Render coach details
function renderCoachDetails(app) {
    return `
        <div class="detail-section">
            <h4>Professional Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Sport Specialization</div>
                    <div class="detail-value">${app.sport_specialization || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Experience</div>
                    <div class="detail-value">${app.experience_years || 'N/A'} years</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Session Rate</div>
                    <div class="detail-value">LKR ${app.session_rate || 'N/A'}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Bio</div>
                <div class="detail-value">${app.bio || 'N/A'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Qualifications</div>
                <div class="detail-value">${app.qualifications || 'N/A'}</div>
            </div>
        </div>
    `;
}

// Render shop owner details
function renderShopOwnerDetails(app) {
    return `
        <div class="detail-section">
            <h4>Business Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Shop Name</div>
                    <div class="detail-value">${app.shop_name || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Business Type</div>
                    <div class="detail-value">${app.business_type || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Year Established</div>
                    <div class="detail-value">${app.year_established || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Registration Number</div>
                    <div class="detail-value">${app.business_registration_number || 'N/A'}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Description</div>
                <div class="detail-value">${app.business_description || 'N/A'}</div>
            </div>
        </div>
    `;
}

// Quick approve
function quickApprove(id) {
    currentApplicationId = id;
    if (confirm('Are you sure you want to approve this application?')) {
        approveApplication();
    }
}

// Quick reject
function quickReject(id) {
    currentApplicationId = id;
    document.getElementById('rejectModal').classList.add('active');
}

// Show reject form
function showRejectForm() {
    document.getElementById('applicationModal').classList.remove('active');
    document.getElementById('rejectModal').classList.add('active');
}

// Approve application
async function approveApplication() {
    if (!currentApplicationId) return;

    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/provider-applications/approve/${currentApplicationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Application approved successfully!');
            closeModal();
            loadApplications(currentPage);
            loadStatistics();
        } else {
            showError(data.message || 'Failed to approve application');
        }
    } catch (error) {
        console.error('Error approving application:', error);
        showError('An error occurred while approving the application');
    }
}

// Reject application
async function rejectApplication() {
    if (!currentApplicationId) return;

    const reason = document.getElementById('rejectionReason').value.trim();

    if (!reason) {
        alert('Please provide a reason for rejection');
        return;
    }

    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/provider-applications/reject/${currentApplicationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason })
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Application rejected');
            closeRejectModal();
            loadApplications(currentPage);
            loadStatistics();
        } else {
            showError(data.message || 'Failed to reject application');
        }
    } catch (error) {
        console.error('Error rejecting application:', error);
        showError('An error occurred while rejecting the application');
    }
}

// Close modal
function closeModal() {
    document.getElementById('applicationModal').classList.remove('active');
    currentApplicationId = null;
}

// Close reject modal
function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.getElementById('rejectionReason').value = '';
}

// Utility functions
function formatProviderType(type) {
    const types = {
        'ground_owner': 'Ground Owner',
        'coach': 'Coach',
        'shop_owner': 'Shop Owner'
    };
    return types[type] || type;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showSuccess(message) {
    // You can implement a toast notification here
    alert(message);
}

function showError(message) {
    // You can implement a toast notification here
    alert(message);
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const applicationModal = document.getElementById('applicationModal');
    const rejectModal = document.getElementById('rejectModal');

    if (e.target === applicationModal) {
        closeModal();
    }

    if (e.target === rejectModal) {
        closeRejectModal();
    }
});

// Make functions available globally for onclick handlers
window.loadApplications = loadApplications;
window.viewApplication = viewApplication;
window.quickApprove = quickApprove;
window.quickReject = quickReject;
window.showRejectForm = showRejectForm;
window.approveApplication = approveApplication;
window.rejectApplication = rejectApplication;
window.closeModal = closeModal;
window.closeRejectModal = closeRejectModal;

})(); // End of IIFE
