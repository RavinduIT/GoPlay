// News Detail JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeNewsDetail();
});

function initializeNewsDetail() {
    // Initialize reading progress
    initializeReadingProgress();
    
    // Initialize table of contents (if needed)
    generateTableOfContents();
    
    // Initialize image zoom functionality
    initializeImageZoom();
    
    // Initialize copy code blocks (if any)
    initializeCodeBlocks();
    
    // Track reading time
    trackReadingTime();
    
    // Initialize related articles hover effects
    initializeRelatedArticles();
}

// Social sharing functions
function shareOnFacebook() {
    const url = encodeURIComponent(articleData.url);
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
    openShareWindow(shareUrl);
}

function shareOnTwitter() {
    const url = encodeURIComponent(articleData.url);
    const text = encodeURIComponent(articleData.title);
    const shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}&via=GoPlaySports`;
    openShareWindow(shareUrl);
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(articleData.url);
    const title = encodeURIComponent(articleData.title);
    const summary = encodeURIComponent(articleData.description);
    const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}&summary=${summary}`;
    openShareWindow(shareUrl);
}

function copyToClipboard() {
    const tempInput = document.createElement('input');
    tempInput.value = articleData.url;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    // Show success message
    showCopySuccess();
}

function openShareWindow(url) {
    window.open(
        url,
        'share-window',
        'width=600,height=400,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes'
    );
}

function showCopySuccess() {
    const copyBtn = document.querySelector('.share-copy');
    const originalText = copyBtn.textContent;
    
    copyBtn.textContent = 'Copied!';
    copyBtn.style.background = '#27ae60';
    
    setTimeout(() => {
        copyBtn.textContent = originalText;
        copyBtn.style.background = '#95a5a6';
    }, 2000);
}

// Reading progress indicator
function initializeReadingProgress() {
    // Create progress bar
    const progressBar = document.createElement('div');
    progressBar.id = 'reading-progress';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(to right, #3498db, #2ecc71);
        z-index: 1000;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(progressBar);
    
    // Update progress on scroll
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateReadingProgress);
            ticking = true;
        }
    });
}

function updateReadingProgress() {
    const article = document.querySelector('.article-content');
    if (!article) return;
    
    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const windowHeight = window.innerHeight;
    const scrollTop = window.scrollY;
    
    const progress = Math.min(100, Math.max(0, 
        ((scrollTop - articleTop + windowHeight / 3) / articleHeight) * 100
    ));
    
    document.getElementById('reading-progress').style.width = progress + '%';
    ticking = false;
}

// Generate table of contents from headings
function generateTableOfContents() {
    const article = document.querySelector('.article-body');
    const headings = article.querySelectorAll('h2, h3');
    
    if (headings.length < 3) return; // Only show TOC if there are enough headings
    
    const toc = document.createElement('div');
    toc.className = 'table-of-contents';
    toc.style.cssText = `
        background: #ecf0f1;
        border-left: 4px solid #3498db;
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
    `;
    
    let tocHTML = '<h4 style="margin-bottom: 15px; color: #2c3e50;">Table of Contents</h4><ul style="list-style: none; padding: 0;">';
    
    headings.forEach((heading, index) => {
        const id = `heading-${index}`;
        heading.id = id;
        
        const level = heading.tagName.toLowerCase();
        const indent = level === 'h3' ? 'margin-left: 20px;' : '';
        
        tocHTML += `
            <li style="${indent} margin-bottom: 8px;">
                <a href="#${id}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">
                    ${heading.textContent}
                </a>
            </li>
        `;
    });
    
    tocHTML += '</ul>';
    toc.innerHTML = tocHTML;
    
    // Insert TOC after the first paragraph
    const firstParagraph = article.querySelector('p');
    if (firstParagraph) {
        firstParagraph.insertAdjacentElement('afterend', toc);
    }
    
    // Smooth scroll for TOC links
    toc.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// Image zoom functionality
function initializeImageZoom() {
    const images = document.querySelectorAll('.article-body img, .featured-image');
    
    images.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function() {
            createImageModal(this);
        });
    });
}

function createImageModal(img) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        cursor: zoom-out;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    const modalImg = img.cloneNode();
    modalImg.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    `;
    
    modal.appendChild(modalImg);
    document.body.appendChild(modal);
    
    // Show modal with animation
    setTimeout(() => modal.style.opacity = '1', 10);
    
    // Close modal on click
    modal.addEventListener('click', () => {
        modal.style.opacity = '0';
        setTimeout(() => modal.remove(), 300);
    });
    
    // Close on Escape key
    const closeOnEscape = (e) => {
        if (e.key === 'Escape') {
            modal.click();
            document.removeEventListener('keydown', closeOnEscape);
        }
    };
    document.addEventListener('keydown', closeOnEscape);
}

// Code blocks functionality
function initializeCodeBlocks() {
    const codeBlocks = document.querySelectorAll('pre code');
    
    codeBlocks.forEach(block => {
        const wrapper = document.createElement('div');
        wrapper.style.cssText = `
            position: relative;
            background: #2c3e50;
            border-radius: 5px;
            margin: 20px 0;
        `;
        
        const copyBtn = document.createElement('button');
        copyBtn.textContent = 'Copy';
        copyBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: #34495e;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8em;
        `;
        
        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(block.textContent).then(() => {
                copyBtn.textContent = 'Copied!';
                setTimeout(() => copyBtn.textContent = 'Copy', 2000);
            });
        });
        
        block.parentNode.insertAdjacentElement('beforebegin', wrapper);
        wrapper.appendChild(block.parentNode);
        wrapper.appendChild(copyBtn);
    });
}

// Track reading time and engagement
function trackReadingTime() {
    let startTime = Date.now();
    let timeOnPage = 0;
    let isReading = true;
    
    // Track when user leaves/returns to tab
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            timeOnPage += Date.now() - startTime;
            isReading = false;
        } else {
            startTime = Date.now();
            isReading = true;
        }
    });
    
    // Track scroll engagement
    let maxScroll = 0;
    window.addEventListener('scroll', () => {
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        maxScroll = Math.max(maxScroll, scrollPercent);
    });
    
    // Send analytics on page unload (if you have analytics)
    window.addEventListener('beforeunload', () => {
        if (isReading) {
            timeOnPage += Date.now() - startTime;
        }
        
        // Log engagement metrics (you can send to your analytics endpoint)
        console.log('Reading engagement:', {
            timeOnPage: Math.round(timeOnPage / 1000), // seconds
            scrollPercent: Math.round(maxScroll),
            articleUrl: window.location.href
        });
    });
}

// Related articles hover effects
function initializeRelatedArticles() {
    const relatedCards = document.querySelectorAll('.related-card');
    
    relatedCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.15)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Press 'B' to go back to news index
    if (e.key === 'b' && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT') {
        window.location.href = '/news';
    }
    
    // Press 'S' to focus on share buttons
    if (e.key === 's' && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT') {
        const shareSection = document.querySelector('.share-buttons');
        if (shareSection) {
            shareSection.scrollIntoView({ behavior: 'smooth' });
            shareSection.querySelector('.share-btn').focus();
        }
    }
});

// Print optimization
function optimizeForPrint() {
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .share-buttons,
            .related-news,
            #reading-progress {
                display: none !important;
            }
            
            .article-content {
                box-shadow: none !important;
                border: 1px solid #ccc;
            }
            
            .featured-image {
                max-height: 300px;
            }
            
            a {
                color: #000 !important;
                text-decoration: underline !important;
            }
            
            .article-body {
                font-size: 12pt !important;
                line-height: 1.6 !important;
            }
        }
    `;
    document.head.appendChild(style);
}

optimizeForPrint();

// Initialize everything when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeNewsDetail);
} else {
    initializeNewsDetail();
}