/**
 * Admin Sports Categories Management
 */
document.addEventListener('DOMContentLoaded', function () {
    loadCategories();
});

let allCategories = [];

/**
 * Load categories from API
 */
async function loadCategories() {
    try {
        const response = await fetch((window.BASE_URL || '') + '/api/admin/categories');
        const result = await response.json();

        if (result.success) {
            allCategories = result.data;
            renderCategories(result.data);
            updateStats(result.stats);
        } else {
            showError('Failed to load categories: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Failed to connect to server');
    }
}

/**
 * Render category cards
 */
function renderCategories(categories) {
    const grid = document.getElementById('categoriesGrid');

    if (!categories || categories.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <p>No sports categories found</p>
            </div>`;
        return;
    }

    grid.innerHTML = categories.map(cat => `
        <div class="category-card ${cat.is_active == 0 ? 'inactive' : ''}" data-id="${cat.id}">
            <div class="category-card-header">
                <div class="category-icon-wrapper">
                    <i class="${escapeHtml(cat.icon || 'fas fa-trophy')}"></i>
                </div>
                <span class="category-status ${cat.is_active == 1 ? 'active' : 'inactive'}">
                    ${cat.is_active == 1 ? 'Active' : 'Inactive'}
                </span>
            </div>
            <h3 class="category-name">${escapeHtml(cat.name)}</h3>
            <p class="category-description">${escapeHtml(cat.description || 'No description provided')}</p>
            <div class="category-stats">
                <div class="category-stat">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>${cat.facilities_count || 0}</strong> Facilities
                </div>
                <div class="category-stat">
                    <i class="fas fa-user-tie"></i>
                    <strong>${cat.coaches_count || 0}</strong> Coaches
                </div>
            </div>
            <div class="category-actions">
                <button class="btn btn-secondary btn-sm" onclick="openEditModal(${cat.id})">
                    <i class="fas fa-pencil-alt"></i> Edit
                </button>
                ${cat.is_active == 1 ?
            `<button class="btn btn-danger btn-sm" onclick="toggleCategoryStatus(${cat.id}, false)">
                        <i class="fas fa-ban"></i> Deactivate
                    </button>` :
            `<button class="btn btn-success btn-sm" onclick="toggleCategoryStatus(${cat.id}, true)">
                        <i class="fas fa-check"></i> Activate
                    </button>`
        }
            </div>
        </div>
    `).join('');
}

/**
 * Update stats
 */
function updateStats(stats) {
    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statActive').textContent = stats.active || 0;
    document.getElementById('statInactive').textContent = stats.inactive || 0;
}

/**
 * Open add modal
 */
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Sports Category';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryIcon').value = 'fas fa-trophy';
    document.getElementById('iconPreview').className = 'fas fa-trophy';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Save Category';

    const modal = document.getElementById('categoryModal');
    modal.classList.add('active');
}

/**
 * Open edit modal
 */
function openEditModal(id) {
    const category = allCategories.find(c => c.id == id);
    if (!category) return;

    document.getElementById('modalTitle').textContent = 'Edit Sports Category';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryDescription').value = category.description || '';
    document.getElementById('categoryIcon').value = category.icon || 'fas fa-trophy';
    document.getElementById('iconPreview').className = category.icon || 'fas fa-trophy';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Category';

    const modal = document.getElementById('categoryModal');
    modal.classList.add('active');
}

/**
 * Close modal
 */
function closeModal() {
    document.getElementById('categoryModal').classList.remove('active');
}

/**
 * Handle form submit (create or update)
 */
async function handleFormSubmit(event) {
    event.preventDefault();

    const id = document.getElementById('categoryId').value;
    const name = document.getElementById('categoryName').value.trim();
    const description = document.getElementById('categoryDescription').value.trim();
    const icon = document.getElementById('categoryIcon').value.trim();

    if (!name) {
        showToast('Category name is required', 'error');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        let response;

        if (id) {
            // Update
            response = await fetch(`${window.BASE_URL || ""}/api/admin/categories/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, description, icon })
            });
        } else {
            // Create
            const formData = new FormData();
            formData.append('name', name);
            formData.append('description', description);
            formData.append('icon', icon);

            response = await fetch((window.BASE_URL || '') + '/api/admin/categories', {
                method: 'POST',
                body: formData
            });
        }

        const result = await response.json();

        if (result.success) {
            showToast(result.message || 'Category saved successfully', 'success');
            closeModal();
            loadCategories();
        } else {
            showToast(result.message || 'Failed to save category', 'error');
        }
    } catch (error) {
        console.error('Save error:', error);
        showToast('Failed to save category', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Category';
    }
}

/**
 * Toggle category active/inactive
 */
async function toggleCategoryStatus(id, activate) {
    const action = activate ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${action} this category?`)) return;

    try {
        if (activate) {
            const response = await fetch(`${window.BASE_URL || ""}/api/admin/categories/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_active: true })
            });
            const result = await response.json();
            if (result.success) {
                showToast('Category activated', 'success');
                loadCategories();
            } else {
                showToast(result.message || 'Failed', 'error');
            }
        } else {
            const response = await fetch(`${window.BASE_URL || ""}/api/admin/categories/${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            if (result.success) {
                showToast('Category deactivated', 'success');
                loadCategories();
            } else {
                showToast(result.message || 'Failed', 'error');
            }
        }
    } catch (error) {
        showToast('Server error', 'error');
    }
}

/**
 * Live icon preview
 */
document.addEventListener('DOMContentLoaded', function () {
    const iconInput = document.getElementById('categoryIcon');
    if (iconInput) {
        iconInput.addEventListener('input', function () {
            document.getElementById('iconPreview').className = this.value || 'fas fa-trophy';
        });
    }

    // Close modal on backdrop click
    document.getElementById('categoryModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    // Close modal on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
});

/**
 * Utility: escape HTML
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Show error in grid
 */
function showError(message) {
    const grid = document.getElementById('categoriesGrid');
    grid.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
            <p>${escapeHtml(message)}</p>
        </div>`;
}

/**
 * Show toast notification
 */
function showToast(message, type) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const colors = { success: '#10b981', error: '#ef4444', info: '#3b82f6' };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.info}" style="color: ${colors[type] || colors.info}"></i>
        <span>${escapeHtml(message)}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(40px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
