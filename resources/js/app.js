document.querySelector('[data-menu-toggle]')?.addEventListener('click', (event) => {
    const button = event.currentTarget;
    const menu = document.querySelector('#mobile-menu');
    const isOpen = button.getAttribute('aria-expanded') === 'true';

    button.setAttribute('aria-expanded', String(!isOpen));
    menu?.classList.toggle('hidden', isOpen);
});

document.querySelectorAll('[data-dashboard-nav-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const sidebar = document.querySelector('#dashboard-sidebar');
        const isOpen = !sidebar?.classList.contains('hidden');

        sidebar?.classList.toggle('hidden', isOpen);
        sidebar?.classList.toggle('flex', !isOpen);
        button.setAttribute('aria-expanded', String(!isOpen));
    });
});
