document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('feeding_program_search')
    const table = document.getElementById('feeding_program_table')
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
