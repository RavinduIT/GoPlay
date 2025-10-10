/**
 * Purpose: Enhance the server-rendered news carousel with navigation,
 *          autoplay, swipe support, and responsive behavior.
 */
(function() {
    const container = document.getElementById('newsCarousel');
    if (!container) return;

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    let currentScroll = 0;
    const getItemWidth = () => {
        const first = container.querySelector('.news-item');
        if (!first) return 320;
        const style = window.getComputedStyle(first);
        const marginRight = parseFloat(style.marginRight || '0');
        return first.offsetWidth + marginRight + 24; // gap approximation
    };

    const scrollTo = (delta) => {
        const w = getItemWidth();
        currentScroll = Math.max(0, container.scrollLeft + delta * w);
        container.scrollTo({ left: currentScroll, behavior: 'smooth' });
        updateButtons();
    };

    const updateButtons = () => {
        if (!prevBtn || !nextBtn) return;
        prevBtn.disabled = container.scrollLeft <= 0;
        const maxScroll = container.scrollWidth - container.clientWidth - 4;
        nextBtn.disabled = container.scrollLeft >= maxScroll;
    };

    prevBtn && prevBtn.addEventListener('click', () => scrollTo(-1));
    nextBtn && nextBtn.addEventListener('click', () => scrollTo(1));

    // Swipe support
    let startX = 0, isDragging = false;
    container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
    }, { passive: true });
    container.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        const dx = e.touches[0].clientX - startX;
        if (Math.abs(dx) > 30) {
            scrollTo(dx > 0 ? -1 : 1);
            isDragging = false;
        }
    }, { passive: true });
    container.addEventListener('touchend', () => { isDragging = false; });

    // Autoplay
    let autoplay = setInterval(() => {
        const maxScroll = container.scrollWidth - container.clientWidth - 4;
        if (container.scrollLeft >= maxScroll) {
            container.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            scrollTo(1);
        }
    }, 6000);

    container.addEventListener('mouseenter', () => clearInterval(autoplay));
    container.addEventListener('mouseleave', () => {
        clearInterval(autoplay);
        autoplay = setInterval(() => {
            const maxScroll = container.scrollWidth - container.clientWidth - 4;
            if (container.scrollLeft >= maxScroll) {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                scrollTo(1);
            }
        }, 6000);
    });

    window.addEventListener('resize', updateButtons);
    container.addEventListener('scroll', updateButtons);
    updateButtons();
})();


