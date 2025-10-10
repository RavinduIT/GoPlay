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
})();


