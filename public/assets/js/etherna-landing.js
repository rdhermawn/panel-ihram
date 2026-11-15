document.documentElement.classList.add('has-js');

const preloader = document.getElementById('pw-preloader');
const hidePreloader = () => preloader?.classList.add('is-done');

// The script is deferred, so the DOM is ready: lift the cover quickly instead of waiting for every image.
window.setTimeout(hidePreloader, 300);

const menuButton = document.querySelector('.pw-nav-toggle');
const mobileMenu = document.getElementById('pw-mobile-nav');

if (menuButton && mobileMenu) {
    const closeMenu = () => {
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Open navigation');
        mobileMenu.hidden = true;
    };

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', String(!isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Open navigation' : 'Close navigation');
        mobileMenu.hidden = isOpen;
    });

    mobileMenu.addEventListener('click', (event) => {
        if (event.target.closest('a')) closeMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !mobileMenu.hidden) {
            closeMenu();
            menuButton.focus();
        }
    });

    window.matchMedia('(min-width: 761px)').addEventListener('change', (event) => {
        if (event.matches) closeMenu();
    });
}

const revealItems = document.querySelectorAll('[data-reveal]');
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
} else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealItems.forEach((item) => revealObserver.observe(item));
}

const carousel = document.querySelector('[data-character-carousel]');
if (carousel) {
    const track = carousel.querySelector('.pw-character-track');
    const cards = [...track.children];
    const previous = carousel.parentElement.querySelector('[data-carousel-prev]');
    const next = carousel.parentElement.querySelector('[data-carousel-next]');
    const progress = carousel.querySelector('.pw-carousel-progress');
    const progressFill = progress.querySelector('span');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    const updateCarousel = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;
        const atEnd = track.scrollLeft >= maxScroll - 1;
        const index = atEnd ? cards.length : Math.min(cards.length, Math.round(track.scrollLeft / (cards[1].offsetLeft - cards[0].offsetLeft)) + 1);
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = atEnd;
        progress.setAttribute('aria-valuenow', String(index));
        progressFill.style.width = `${maxScroll ? (track.scrollLeft / maxScroll) * 100 : 100}%`;
    };

    previous.addEventListener('click', () => track.scrollBy({ left: -(cards[1].offsetLeft - cards[0].offsetLeft), behavior: reduceMotion.matches ? 'instant' : 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left: cards[1].offsetLeft - cards[0].offsetLeft, behavior: reduceMotion.matches ? 'instant' : 'smooth' }));
    track.addEventListener('scroll', updateCarousel, { passive: true });
    window.addEventListener('resize', updateCarousel);
    updateCarousel();
}
