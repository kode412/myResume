// script.js - small UI helpers for progressku

function filterProjects(type) {
    // redirect with query param (server will handle)
    const query = new URLSearchParams(window.location.search);
    query.set('filter', type);
    window.location.search = query.toString();
}

// add fade-in on scroll
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.card, .service-card, .content-block').forEach(el => observer.observe(el));
});
