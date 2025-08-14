// Admin News Management System
class AdminNewsManager {
    constructor() {
        this.newsData = [];
        this.currentEditingNews = null;
        this.imagePreviewContainer = null;
        this.init();
    }

    async init() {
        await this.loadNewsData();
        this.setupEventListeners();
        this.renderNewsGrid();
    }

    async loadNewsData() {
        try {
            const response = await fetch('/data/news.json');
            if (response.ok) {
                const data = await response.json();
                this.newsData = data.news || [];
            } else {
                this.newsData = [];
            }
        } catch (error) {
            console.error('Error loading news data:', error);
            this.newsData = [];
        }
    }

    setupEventListeners() {
        // Add News Button
        const addNewsBtn = document.getElementById('add-news-btn');
        if (addNewsBtn) {
            addNewsBtn.addEventListener('click', () => this.showNewsModal());
        }

        // News Form Submit
        const newsForm = document.getElementById('news-form');
        if (newsForm) {
            newsForm.addEventListener('submit', (e) => this.handleNewsSubmit(e));
        }

        // Image Upload Handler
        const imageUpload = document.getElementById('news-images');
        if (imageUpload) {
            imageUpload.addEventListener('change', (e) => this.handleImageUpload(e));
        }

        // Modal Close Events
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const closeBtn = modal.querySelector('.modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(modal.id));
            }
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });
    }

    renderNewsGrid() {
        const newsGrid = document.getElementById('news-grid');
        if (!newsGrid) return;

        if (this.newsData.length === 0) {
            newsGrid.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <h3>No News Articles</h3>
                    <p>Get started by creating your first news article</p>
                    <button class="btn primary" onclick="adminNews.showNewsModal()">
                        <i class="fas fa-plus"></i>
                        Add First Article
                    </button>
                </div>
            `;
            return;
        }

        newsGrid.innerHTML = this.newsData.map(news => `
            <div class="news-card" data-id="${news.id}">
                <div class="news-card-image">
                    <img 
                        src="${this.getNewsImage(news)}" 
                        alt="${news.title}"
                        onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=300&h=200&fit=crop'"
                    />
                    <div class="news-card-overlay">
                        <div class="news-card-actions">
                            <button 
                                class="action-btn view" 
                                onclick="adminNews.viewNews('${news.id}')"
                                title="View Article"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                            <button 
                                class="action-btn edit" 
                                onclick="adminNews.editNews('${news.id}')"
                                title="Edit Article"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button 
                                class="action-btn delete" 
                                onclick="adminNews.deleteNews('${news.id}')"
                                title="Delete Article"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="news-status ${news.status || 'published'}">
                        ${news.status || 'Published'}
                    </div>
                </div>
                <div class="news-card-content">
                    <div class="news-card-meta">
                        <span class="category">${news.category || 'Sports'}</span>
                        <span class="date">${this.formatDate(news.date || news.publishedAt)}</span>
                    </div>
                    <h3 class="news-card-title">${news.title}</h3>
                    <p class="news-card-summary">${this.truncateText(news.summary, 100)}</p>
                    <div class="news-card-stats">
                        <span><i class="fas fa-eye"></i> ${news.views || 0} views</span>
                        <span><i class="fas fa-clock"></i> ${news.readTime || '5 min read'}</span>
                    </div>
                </div>
            </div>
        `).join('');

        this.updateNewsStats();
    }

    showNewsModal(newsId = null) {
        const modal = document.getElementById('news-modal');
        const form = document.getElementById('news-form');
        const modalTitle = document.getElementById('news-modal-title');
        
        if (!modal || !form) return;

        // Reset form
        form.reset();
        this.clearImagePreviews();
        this.currentEditingNews = null;

        if (newsId) {
            // Edit mode
            const news = this.newsData.find(n => n.id.toString() === newsId.toString());
            if (news) {
                this.currentEditingNews = news;
                modalTitle.textContent = 'Edit News Article';
                this.populateForm(news);
            }
        } else {
            // Add mode
            modalTitle.textContent = 'Add News Article';
        }

        this.showModal('news-modal');
    }

    populateForm(news) {
        const form = document.getElementById('news-form');
        if (!form) return;

        form.elements['title'].value = news.title || '';
        form.elements['slug'].value = news.slug || '';
        form.elements['summary'].value = news.summary || '';
        form.elements['content'].value = news.content || '';
        form.elements['category'].value = news.category || 'Sports';
        form.elements['author'].value = news.author || '';
        form.elements['status'].value = news.status || 'published';
        form.elements['featured'].checked = news.featured || false;

        // Handle tags
        if (news.tags && news.tags.length > 0) {
            form.elements['tags'].value = news.tags.join(', ');
        }

        // Show existing images
        if (news.images && news.images.length > 0) {
            this.showImagePreviews(news.images);
        }
    }

    async handleNewsSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Saving...';

        try {
            const newsData = {
                title: formData.get('title'),
                slug: this.generateSlug(formData.get('title')),
                summary: formData.get('summary'),
                content: formData.get('content'),
                category: formData.get('category'),
                author: formData.get('author') || 'Admin',
                status: formData.get('status'),
                featured: formData.has('featured'),
                tags: formData.get('tags').split(',').map(tag => tag.trim()).filter(tag => tag),
                readTime: this.calculateReadTime(formData.get('content'))
            };

            // Handle images
            const imageFiles = formData.getAll('images');
            if (imageFiles.length > 0) {
                newsData.images = await this.processImages(imageFiles);
                newsData.image = newsData.images[0]; // Set first image as main image
            } else if (this.currentEditingNews && this.currentEditingNews.images) {
                // Keep existing images if no new ones uploaded
                newsData.images = this.currentEditingNews.images;
                newsData.image = this.currentEditingNews.image;
            }

            if (this.currentEditingNews) {
                // Update existing news
                newsData.id = this.currentEditingNews.id;
                newsData.date = this.currentEditingNews.date;
                newsData.publishedAt = this.currentEditingNews.publishedAt;
                newsData.views = this.currentEditingNews.views || 0;
                
                const index = this.newsData.findIndex(n => n.id === this.currentEditingNews.id);
                this.newsData[index] = newsData;
            } else {
                // Add new news
                newsData.id = this.generateId();
                newsData.date = new Date().toISOString().split('T')[0];
                newsData.publishedAt = new Date().toISOString();
                newsData.views = 0;
                
                this.newsData.unshift(newsData); // Add to beginning
            }

            // Save to JSON (in real app, this would be an API call)
            await this.saveNewsData();
            
            // Success feedback
            this.showToast(
                this.currentEditingNews ? 'News article updated successfully!' : 'News article created successfully!',
                'success'
            );
            
            // Close modal and refresh
            this.closeModal('news-modal');
            this.renderNewsGrid();

        } catch (error) {
            console.error('Error saving news:', error);
            this.showToast('Error saving news article. Please try again.', 'error');
        } finally {
            submitBtn.classList.remove('loading');
            submitBtn.textContent = this.currentEditingNews ? 'Update Article' : 'Create Article';
        }
    }

    async processImages(imageFiles) {
        const images = [];
        
        for (let file of imageFiles) {
            if (file && file.size > 0) {
                try {
                    // In a real app, you would upload to a server
                    // For demo, we'll use placeholder URLs
                    const imageUrl = await this.convertToBase64(file);
                    images.push(imageUrl);
                } catch (error) {
                    console.error('Error processing image:', error);
                }
            }
        }
        
        return images;
    }

    convertToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    handleImageUpload(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const imageUrls = [];
            let filesProcessed = 0;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imageUrls.push(e.target.result);
                    filesProcessed++;
                    
                    if (filesProcessed === files.length) {
                        this.showImagePreviews(imageUrls);
                    }
                };
                reader.readAsDataURL(file);
            });
        }
    }

    showImagePreviews(imageUrls) {
        let container = document.getElementById('image-previews');
        if (!container) {
            container = document.createElement('div');
            container.id = 'image-previews';
            container.className = 'image-previews';
            
            const imageUpload = document.getElementById('news-images');
            if (imageUpload) {
                imageUpload.parentNode.insertBefore(container, imageUpload.nextSibling);
            }
        }

        container.innerHTML = imageUrls.map((url, index) => `
            <div class="image-preview">
                <img src="${url}" alt="Preview ${index + 1}">
                <button type="button" class="remove-image" onclick="adminNews.removeImagePreview(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        this.imagePreviewContainer = container;
    }

    clearImagePreviews() {
        const container = document.getElementById('image-previews');
        if (container) {
            container.innerHTML = '';
        }
    }

    removeImagePreview(index) {
        // In a real implementation, you'd manage the file list here
        console.log('Remove image at index:', index);
    }

    viewNews(newsId) {
        window.open(`/pages/news-detail.html?id=${newsId}`, '_blank');
    }

    editNews(newsId) {
        this.showNewsModal(newsId);
    }

    async deleteNews(newsId) {
        const news = this.newsData.find(n => n.id.toString() === newsId.toString());
        if (!news) return;

        const confirmed = confirm(`Are you sure you want to delete "${news.title}"? This action cannot be undone.`);
        if (!confirmed) return;

        try {
            this.newsData = this.newsData.filter(n => n.id.toString() !== newsId.toString());
            await this.saveNewsData();
            
            this.showToast('News article deleted successfully!', 'success');
            this.renderNewsGrid();
        } catch (error) {
            console.error('Error deleting news:', error);
            this.showToast('Error deleting news article. Please try again.', 'error');
        }
    }

    async saveNewsData() {
        // In a real app, this would be an API call to save to server
        // For demo purposes, we'll simulate saving to localStorage
        try {
            const newsJson = {
                news: this.newsData,
                lastUpdated: new Date().toISOString()
            };
            
            localStorage.setItem('newsData', JSON.stringify(newsJson));
            console.log('News data saved successfully');
            return true;
        } catch (error) {
            console.error('Error saving news data:', error);
            throw error;
        }
    }

    updateNewsStats() {
        const totalNews = this.newsData.length;
        const publishedNews = this.newsData.filter(n => n.status === 'published' || !n.status).length;
        const draftNews = this.newsData.filter(n => n.status === 'draft').length;
        const totalViews = this.newsData.reduce((sum, news) => sum + (news.views || 0), 0);

        // Update stats in the UI
        this.updateElementText('total-news-count', totalNews);
        this.updateElementText('published-news-count', publishedNews);
        this.updateElementText('draft-news-count', draftNews);
        this.updateElementText('total-news-views', totalViews.toLocaleString());
    }

    // Utility functions
    generateId() {
        return Date.now() + Math.random().toString(36).substr(2, 9);
    }

    generateSlug(title) {
        return title
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    }

    calculateReadTime(content) {
        if (!content) return '1 min read';
        
        const wordsPerMinute = 200;
        const wordCount = content.split(/\s+/).length;
        const minutes = Math.ceil(wordCount / wordsPerMinute);
        
        return `${minutes} min read`;
    }

    getNewsImage(news) {
        if (news.image) return news.image;
        if (news.images && news.images.length > 0) return news.images[0];
        return 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=300&h=200&fit=crop';
    }

    formatDate(dateString) {
        if (!dateString) return 'Unknown date';
        
        try {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (error) {
            return dateString;
        }
    }

    truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength).trim() + '...';
    }

    updateElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    }

    showToast(message, type = 'success') {
        // Use the existing admin toast system
        if (window.admin && window.admin.showToast) {
            window.admin.showToast(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
            alert(message);
        }
    }
}

// Initialize and export
window.adminNews = new AdminNewsManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminNewsManager;
}