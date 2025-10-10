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

    // Dynamic venues rendering from local JSON
    async function renderVenues() {
        const grid = document.getElementById('venuesGrid');
        if (!grid) return;
        try {
            const res = await fetch('/public/data/grounds.json');
            if (!res.ok) throw new Error('Failed to load grounds');
            const data = await res.json();
            const venues = (data.grounds || []).slice(0, 6);
            grid.innerHTML = venues.map(v => `
                <div class="venue-card">
                    <div class="venue-image">
                        <img src="${v.image || '/public/assets/images/ground.jpeg'}" alt="${v.name}" loading="lazy" onerror="this.src='/public/assets/images/ground.jpeg'" />
                        <div class="venue-overlay"></div>
                        <div class="venue-rating"><i class="fas fa-star"></i> ${Number(v.rating || 4.5).toFixed(1)}</div>
                        <div class="venue-info">
                            <div class="venue-name">${v.name}</div>
                            <div class="venue-type">${(v.sport || '').toString().charAt(0).toUpperCase() + (v.sport || '').toString().slice(1)} • ${v.location}</div>
                        </div>
                    </div>
                    <div class="venue-details">
                        <div class="venue-pricing">
                            <div class="venue-price"><i class="fas fa-tag"></i> LKR ${Number(v.price || 0).toLocaleString()}</div>
                            <div class="venue-availability">Available</div>
                        </div>
                        <a class="venue-book-btn" href="/book-ground?id=${v.id}"><i class="fas fa-calendar"></i> Book Now</a>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.error('Venues load error:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', renderVenues);
})();


