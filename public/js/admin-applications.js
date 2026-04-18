(function() {
    'use strict';

    let currentApplicationId = null;
    let currentPage = 1;
    const itemsPerPage = 10;

    // Escape HTML to prevent XSS
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const d = document.createElement('div');
        d.textContent = String(str);
        return d.innerHTML;
    }

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
            <td>#${parseInt(app.id)}</td>
            <td>
                <div>
                    <strong>${escapeHtml(app.first_name)} ${escapeHtml(app.last_name)}</strong><br>
                    <small style="color: #6b7280;">${escapeHtml(app.email)}</small>
                </div>
            </td>
            <td>
                <span class="type-badge">${formatProviderType(app.provider_type)}</span>
            </td>
            <td>${formatDate(app.created_at)}</td>
            <td>
                <span class="status-badge ${escapeHtml(app.status)}">${escapeHtml(app.status)}</span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-view" onclick="viewApplication(${parseInt(app.id)})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    ${app.status === 'pending' ? `
                        <button class="btn-approve-inline" onclick="quickApprove(${parseInt(app.id)})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-reject-inline" onclick="quickReject(${parseInt(app.id)})">
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
                    <div class="detail-value">${escapeHtml(app.first_name)} ${escapeHtml(app.last_name)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${escapeHtml(app.email)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">${escapeHtml(app.phone)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">City</div>
                    <div class="detail-value">${escapeHtml(app.city)}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Address</div>
                <div class="detail-value">${escapeHtml(app.address)}</div>
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
    html += renderDocumentsSection(app);

    detailsDiv.innerHTML = html;
}

// Render all documents/uploads for an application
function renderDocumentsSection(app) {
    const base = window.BASE_URL || '';
    const uploadBase = base + '/';

    // Collect all document fields
    const singleDocs = [
        { key: 'nic_document', label: 'NIC / ID Document' },
        { key: 'ownership_proof', label: 'Ownership Proof' },
        { key: 'profile_photo', label: 'Profile Photo' },
        { key: 'business_registration', label: 'Business Registration' },
        { key: 'tax_document', label: 'Tax Document' },
        { key: 'identification_document', label: 'Identification Document' },
    ];

    const arrayDocs = [
        { key: 'facility_images', label: 'Facility Images' },
        { key: 'shop_images', label: 'Shop Images' },
        { key: 'certifications', label: 'Certifications' },
        { key: 'additional_documents', label: 'Additional Documents' },
    ];

    let hasAny = false;
    let items = '';

    // Single file documents
    singleDocs.forEach(doc => {
        if (app[doc.key]) {
            hasAny = true;
            items += renderDocItem(doc.label, app[doc.key], uploadBase);
        }
    });

    // Array (JSON) documents
    arrayDocs.forEach(doc => {
        let arr = app[doc.key];
        if (!arr) return;
        if (typeof arr === 'string') {
            try { arr = JSON.parse(arr); } catch(e) { arr = null; }
        }
        if (Array.isArray(arr) && arr.length > 0) {
            hasAny = true;
            items += renderDocGallery(doc.label, arr, uploadBase);
        }
    });

    if (!hasAny) {
        return `
            <div class="detail-section">
                <h4><i class="fas fa-folder-open" style="margin-right:8px;color:#6b7280;"></i>Documents</h4>
                <p style="color:#9ca3af;font-size:14px;padding:12px 0;">No documents were submitted with this application.</p>
            </div>`;
    }

    return `
        <div class="detail-section">
            <h4><i class="fas fa-folder-open" style="margin-right:8px;color:#3b82f6;"></i>Documents & Uploads</h4>
            <div style="display:flex;flex-direction:column;gap:16px;margin-top:12px;">
                ${items}
            </div>
        </div>`;
}

function renderDocItem(label, filePath, uploadBase) {
    const url = uploadBase + filePath;
    const ext = filePath.split('.').pop().toLowerCase();
    const isImage = ['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext);
    const isPdf = ext === 'pdf';

    if (isImage) {
        return `
            <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fafafa;">
                <div style="padding:10px 16px;background:#f3f4f6;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">
                    <i class="fas fa-image" style="color:#3b82f6;margin-right:6px;"></i>${escapeHtml(label)}
                </div>
                <div style="padding:12px;text-align:center;">
                    <img src="${escapeHtml(url)}" alt="${escapeHtml(label)}" 
                         style="max-width:100%;max-height:300px;border-radius:8px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.1);"
                         onclick="window.open('${escapeHtml(url)}','_blank')"
                         onerror="this.parentElement.innerHTML='<div style=\\'padding:20px;color:#ef4444;\\'>Image failed to load</div>'" />
                </div>
                <div style="padding:8px 16px;border-top:1px solid #e5e7eb;text-align:right;">
                    <a href="${escapeHtml(url)}" target="_blank" rel="noopener" style="font-size:12px;color:#3b82f6;text-decoration:none;font-weight:500;">
                        <i class="fas fa-external-link-alt"></i> Open full size
                    </a>
                </div>
            </div>`;
    }

    // PDF or other file
    const icon = isPdf ? 'fa-file-pdf' : 'fa-file-alt';
    const iconColor = isPdf ? '#ef4444' : '#6b7280';
    return `
        <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fafafa;">
            <div style="padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                    <i class="fas ${icon}" style="font-size:20px;color:${iconColor};"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:600;color:#374151;">${escapeHtml(label)}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">${escapeHtml(filePath.split('/').pop())}</div>
                </div>
                <a href="${escapeHtml(url)}" target="_blank" rel="noopener" 
                   style="padding:8px 16px;background:#3b82f6;color:#fff;border-radius:8px;font-size:12px;text-decoration:none;font-weight:500;white-space:nowrap;">
                    <i class="fas fa-download" style="margin-right:4px;"></i> View
                </a>
            </div>
        </div>`;
}

function renderDocGallery(label, files, uploadBase) {
    const thumbs = files.map(f => {
        const url = uploadBase + f;
        const ext = f.split('.').pop().toLowerCase();
        const isImage = ['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext);

        if (isImage) {
            return `<div style="width:120px;flex-shrink:0;">
                <img src="${escapeHtml(url)}" alt="" 
                     style="width:120px;height:90px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid #e5e7eb;transition:border-color 0.2s;"
                     onclick="window.open('${escapeHtml(url)}','_blank')"
                     onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#e5e7eb'"
                     onerror="this.style.display='none'" />
            </div>`;
        }
        // Non-image file
        const isPdf = ext === 'pdf';
        return `<div style="width:120px;flex-shrink:0;">
            <a href="${escapeHtml(url)}" target="_blank" rel="noopener" style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:120px;height:90px;border-radius:8px;border:2px solid #e5e7eb;background:#f9fafb;text-decoration:none;transition:border-color 0.2s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#e5e7eb'">
                <i class="fas ${isPdf ? 'fa-file-pdf' : 'fa-file-alt'}" style="font-size:24px;color:${isPdf ? '#ef4444' : '#6b7280'};margin-bottom:4px;"></i>
                <span style="font-size:10px;color:#6b7280;text-align:center;padding:0 4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:110px;">${escapeHtml(f.split('/').pop())}</span>
            </a>
        </div>`;
    }).join('');

    return `
        <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fafafa;">
            <div style="padding:10px 16px;background:#f3f4f6;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">
                <i class="fas fa-images" style="color:#3b82f6;margin-right:6px;"></i>${escapeHtml(label)}
                <span style="font-weight:400;color:#9ca3af;margin-left:4px;">(${files.length})</span>
            </div>
            <div style="padding:12px;display:flex;gap:10px;overflow-x:auto;">
                ${thumbs}
            </div>
        </div>`;
}

// Render ground owner details
function renderGroundOwnerDetails(app) {
    return `
        <div class="detail-section">
            <h4>Facility Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Facility Name</div>
                    <div class="detail-value">${escapeHtml(app.facility_name || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Number of Courts</div>
                    <div class="detail-value">${escapeHtml(app.number_of_courts || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Hourly Rate</div>
                    <div class="detail-value">LKR ${escapeHtml(app.proposed_hourly_rate || 'N/A')}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Description</div>
                <div class="detail-value">${escapeHtml(app.facility_description || 'N/A')}</div>
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
                    <div class="detail-value">${escapeHtml(app.sport_specialization || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Experience</div>
                    <div class="detail-value">${escapeHtml(app.experience_years || 'N/A')} years</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Session Rate</div>
                    <div class="detail-value">LKR ${escapeHtml(app.session_rate || 'N/A')}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Bio</div>
                <div class="detail-value">${escapeHtml(app.bio || 'N/A')}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Qualifications</div>
                <div class="detail-value">${escapeHtml(app.qualifications || 'N/A')}</div>
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
                    <div class="detail-value">${escapeHtml(app.shop_name || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Business Type</div>
                    <div class="detail-value">${escapeHtml(app.business_type || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Year Established</div>
                    <div class="detail-value">${escapeHtml(app.year_established || 'N/A')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Registration Number</div>
                    <div class="detail-value">${escapeHtml(app.business_registration_number || 'N/A')}</div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Description</div>
                <div class="detail-value">${escapeHtml(app.business_description || 'N/A')}</div>
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

    // Disable all action buttons to prevent double-click
    const actionBtns = document.querySelectorAll('.btn-approve, .btn-approve-inline, .btn-reject, .btn-reject-inline');
    actionBtns.forEach(b => b.disabled = true);

    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/provider-applications/approve/${currentApplicationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showSuccess(data.message || 'Application approved successfully!');
            closeModal();
            loadApplications(currentPage);
            loadStatistics();
        } else {
            showError(data.message || 'Failed to approve application');
        }
    } catch (error) {
        console.error('Error approving application:', error);
        showError('An error occurred while approving the application');
    } finally {
        actionBtns.forEach(b => b.disabled = false);
    }
}

// Reject application
async function rejectApplication() {
    if (!currentApplicationId) return;

    const reason = document.getElementById('rejectionReason').value.trim();

    if (!reason) {
        showError('Please provide a reason for rejection');
        return;
    }

    // Disable buttons to prevent double-click
    const submitBtn = document.querySelector('#rejectModal .btn-reject, #rejectModal button[type=\"submit\"], #rejectModal .btn-confirm-reject');
    const actionBtns = document.querySelectorAll('.btn-approve, .btn-approve-inline, .btn-reject, .btn-reject-inline');
    actionBtns.forEach(b => b.disabled = true);
    if (submitBtn) submitBtn.disabled = true;

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
            showSuccess(data.message || 'Application rejected');
            closeRejectModal();
            loadApplications(currentPage);
            loadStatistics();
        } else {
            showError(data.message || 'Failed to reject application');
        }
    } catch (error) {
        console.error('Error rejecting application:', error);
        showError('An error occurred while rejecting the application');
    } finally {
        actionBtns.forEach(b => b.disabled = false);
        if (submitBtn) submitBtn.disabled = false;
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
    showAdminToast('success', message);
}

function showError(message) {
    showAdminToast('error', message);
}

function showAdminToast(type, message) {
    document.querySelectorAll('.admin-toast').forEach(t => t.remove());
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }
    }, 4000);
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
