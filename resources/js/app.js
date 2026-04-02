import './bootstrap';
document.addEventListener("DOMContentLoaded", () => {
    const html = document.documentElement;

    // aktifkan dark mode default
    html.classList.add('dark');

    // atau auto-ikut sistem:
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        html.classList.add('dark');
    }
});
