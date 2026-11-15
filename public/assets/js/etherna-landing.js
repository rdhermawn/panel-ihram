document.documentElement.classList.add('has-js');

const preloader = document.getElementById('pw-preloader');
const hidePreloader = () => preloader?.classList.add('is-done');

// The script is deferred, so the DOM is ready: lift the cover once the name has been written in
// (about 1s) instead of waiting for every image. Reduced motion has nothing to watch, so it goes at once.
window.setTimeout(hidePreloader, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 150 : 1000);

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

// Hero emblem drifts against the cursor on mouse devices, giving the logo depth over the backdrop.
const hero = document.querySelector('.pw-hero');
if (hero?.querySelector('.pw-hero-emblem') && window.matchMedia('(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)').matches) {
    hero.addEventListener('pointermove', (event) => {
        const box = hero.getBoundingClientRect();
        hero.style.setProperty('--pw-hx', (((event.clientX - box.left) / box.width) * 2 - 1).toFixed(3));
        hero.style.setProperty('--pw-hy', (((event.clientY - box.top) / box.height) * 2 - 1).toFixed(3));
    });
    hero.addEventListener('pointerleave', () => {
        hero.style.removeProperty('--pw-hx');
        hero.style.removeProperty('--pw-hy');
    });
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

    if (!reduceMotion.matches && 'IntersectionObserver' in window) {
        // Entrance: cards rise one after another the first time the section is on screen.
        cards.forEach((card, i) => card.style.setProperty('--pw-delay', `${Math.min(i, 5) * 0.09}s`));
        carousel.classList.add('is-armed');
        new IntersectionObserver((entries, observer) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                carousel.classList.add('is-in');
                observer.disconnect();
            }
        }, { threshold: 0.25 }).observe(carousel);

        // Depth: on mouse devices the character and the logo drift opposite ways with the cursor.
        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            cards.forEach((card) => {
                card.addEventListener('pointermove', (event) => {
                    const box = card.getBoundingClientRect();
                    card.style.setProperty('--pw-tilt-x', (((event.clientX - box.left) / box.width) * 2 - 1).toFixed(3));
                    card.style.setProperty('--pw-tilt-y', (((event.clientY - box.top) / box.height) * 2 - 1).toFixed(3));
                });
                card.addEventListener('pointerleave', () => {
                    card.style.removeProperty('--pw-tilt-x');
                    card.style.removeProperty('--pw-tilt-y');
                });
            });
        }
    }
}
