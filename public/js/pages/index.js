/**
 * Purpose: Home page enhancements (animations, parallax, interactions)
 *          kept minimal and resilient to missing elements.
 */
(function() {
    // Scroll progress bar
    const progress = document.createElement('div');
    progress.style.cssText = 'position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#2563eb,#0891b2);z-index:9999;transition:width .1s ease';
    document.addEventListener('DOMContentLoaded', () => document.body.appendChild(progress));
    window.addEventListener('scroll', () => {
        const h = document.documentElement.scrollHeight - window.innerHeight;
        const p = Math.max(0, (window.scrollY / (h || 1)) * 100);
        progress.style.width = p + '%';
    });

    // Parallax hero subtle
    const hero = document.querySelector('.hero-section');
    if (hero) {
        window.addEventListener('scroll', () => {
            hero.classList.toggle('scrolled', window.scrollY > 120);
        });
    }

    // Feature cards hover tilt (lightweight)
    document.querySelectorAll('.feature-card').forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const r = card.getBoundingClientRect();
            const x = e.clientX - r.left - r.width/2;
            const y = e.clientY - r.top - r.height/2;
            card.style.transform = `perspective(800px) rotateY(${x/r.width*6}deg) rotateX(${-(y/r.height*6)}deg)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    // Dynamic venues rendering from API (database-backed)
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
    

    async function renderVenues() {
        const grid = document.getElementById('venuesGrid');
        if (!grid) return;
        try {
            const res = await fetch((window.BASE_URL||'')+'/api/grounds?limit=6&sort=rating');
            if (!res.ok) throw new Error('Failed to load grounds');
            const json = await res.json();
            const venues = json.data || [];
            if (!venues.length) {
                grid.innerHTML = '<p style="text-align:center;color:#64748b;grid-column:1/-1;">No venues available yet. Check back soon!</p>';
                return;
            }
            grid.innerHTML = venues.map(v => {
                const name = escapeHtml(v.name || 'Venue');
                const sport = escapeHtml(v.category_name || v.sport || '');
                const city = escapeHtml(v.city || v.location || '');
                const rate = Number(v.hourly_rate || v.price || 0).toLocaleString();
                const rating = Number(v.rating || 0).toFixed(1);
                const imgSrc = v.images && v.images[0]
                    ? (window.BASE_URL||'')+'/'+v.images[0]
                    : (window.BASE_URL||'')+'/public/assets/images/ground.jpeg';
                const detailUrl = (window.BASE_URL||'')+'/ground-details?id='+parseInt(v.id);
                return `
                <div class="venue-card">
                    <div class="venue-image">
                        <img src="${imgSrc}" alt="${name}" loading="lazy" onerror="this.src=(window.BASE_URL||'')+'/public/assets/images/ground.jpeg'" />
                        <div class="venue-overlay"></div>
                        <div class="venue-rating"><i class="fas fa-star"></i> ${rating}</div>
                        <div class="venue-info">
                            <div class="venue-name">${name}</div>
                            <div class="venue-type">${sport}${sport && city ? ' • ' : ''}${city}</div>
                        </div>
                    </div>
                    <div class="venue-details">
                        <div class="venue-pricing">
                            <div class="venue-price"><i class="fas fa-tag"></i> LKR ${rate}/hr</div>
                        </div>
                        <a class="venue-book-btn" href="${detailUrl}"><i class="fas fa-eye"></i> View Details</a>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            console.error('Venues load error:', e);
            const grid2 = document.getElementById('venuesGrid');
            if (grid2) grid2.innerHTML = '<p style="text-align:center;color:#64748b;grid-column:1/-1;">Unable to load venues. Please refresh the page.</p>';
        }
    }

    document.addEventListener('DOMContentLoaded', renderVenues);
})();


