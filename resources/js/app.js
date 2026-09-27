import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    dark: document.documentElement.classList.contains('dark'),

    toggle() {
        this.dark = ! this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
    },
}));

Alpine.data('confirmAction', (message = 'Are you sure?') => ({
    message,

    ask(event) {
        if (! window.confirm(this.message)) {
            event.preventDefault();
        }
    },
}));

Alpine.start();
