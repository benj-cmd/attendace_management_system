    </main>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const normalize = (v) => (v || '').toString().toLowerCase().trim();

    const applyFilter = (key, value) => {
        const needle = normalize(value);

        document.querySelectorAll(`[data-search-table="${key}"]`).forEach((tbody) => {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((tr) => {
                if (!needle) {
                    tr.style.display = '';
                    return;
                }
                const hay = normalize(tr.innerText);
                tr.style.display = hay.includes(needle) ? '' : 'none';
            });
        });

        document.querySelectorAll(`[data-search-cards="${key}"]`).forEach((container) => {
            const items = container.querySelectorAll('[data-search-item]');
            items.forEach((el) => {
                if (!needle) {
                    el.style.display = '';
                    return;
                }
                const hay = normalize(el.innerText);
                el.style.display = hay.includes(needle) ? '' : 'none';
            });
        });
    };

    document.querySelectorAll('[data-search-input]').forEach((input) => {
        const key = input.getAttribute('data-search-input');
        input.addEventListener('input', () => applyFilter(key, input.value));
    });
})();
</script>
</body>
</html>
