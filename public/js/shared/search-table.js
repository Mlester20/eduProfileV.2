document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#searchTable tr');

    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();

        if(rowText.includes(searchValue)){
            row.style.display = '';
        } else {
            row.style.display = 'none';

        }
    })
})
