document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('class_list_search')
    const table = document.getElementById('class_list_table')
    if (!searchInput || !table) return

    const rows = Array.from(table.querySelectorAll('tbody tr[data-student-name]'))

    searchInput.addEventListener('input', function () {
        const term = searchInput.value.trim().toLowerCase()
        rows.forEach(function (row) {
            const matches = row.getAttribute('data-student-name').includes(term)
            row.style.display = matches ? '' : 'none'
        })
    })
})
