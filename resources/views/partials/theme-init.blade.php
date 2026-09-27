<script>
    (function () {
        const stored = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const useDark = stored === 'dark' || (stored === null && prefersDark);

        document.documentElement.classList.toggle('dark', useDark);
    })();
</script>
