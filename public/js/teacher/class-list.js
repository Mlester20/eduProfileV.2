function debounce(fn, delay){
    let timer
    return function(...args){
        clearTimeout(timer)
        const context = this
        timer = setTimeout(function(){
            fn.apply(context, args)
        }, delay)
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('class_list_search')
    const perPageSelect = document.getElementById('class_list_per_page')

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            const term = searchInput.value.trim()
            const url = new URL(window.location.href)
            if (term === '') {
                url.searchParams.delete('search')
            } else {
                url.searchParams.set('search', term)
            }
            url.searchParams.set('page', '1')
            window.location.href = url.toString()
        }, 400))
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            const url = new URL(window.location.href)
            url.searchParams.set('per_page', perPageSelect.value)
            url.searchParams.set('page', '1')
            window.location.href = url.toString()
        })
    }
})
