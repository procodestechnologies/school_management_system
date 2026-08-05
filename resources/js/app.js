function initScrollReveal() {
    const items = document.querySelectorAll('[data-animate]:not([data-animate-in])');

    if (! items.length) {
        return;
    }

    if (! ('IntersectionObserver' in window)) {
        items.forEach((el) => el.setAttribute('data-animate-in', ''));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.setAttribute('data-animate-in', '');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

    items.forEach((el) => observer.observe(el));
}

// Vite emits this as a `type="module"` script, which loads deferred - by
// the time it runs, `DOMContentLoaded` may have already fired, so a plain
// listener would never call back. Run immediately if the document is
// already parsed, otherwise wait for it like normal.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollReveal);
} else {
    initScrollReveal();
}

document.addEventListener('livewire:navigated', initScrollReveal);
