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

document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('student_search')
    const select = document.getElementById('student_id')
    const suggestions = document.getElementById('student_suggestions')
    const form = document.getElementById('learnerSearchForm')
    if(!searchInput || !select || !form) return

    const options = Array.from(select.options).filter(function(option){
        return option.value !== ''
    })

    function hideSuggestions(){
        suggestions.innerHTML = ''
        suggestions.style.display = 'none'
    }

    function renderSuggestions(filter){
        if(filter === ''){
            hideSuggestions()
            return
        }
        const matches = options.filter(function(option){
            return option.text.toLowerCase().includes(filter)
        })
        suggestions.innerHTML = ''
        if(matches.length === 0){
            const empty = document.createElement('div')
            empty.className = 'list-group-item bg-white text-muted'
            empty.textContent = 'No learners found'
            suggestions.appendChild(empty)
        }else{
            matches.slice(0, 30).forEach(function(option){
                const item = document.createElement('button')
                item.type = 'button'
                item.className = 'list-group-item list-group-item-action bg-white'
                item.textContent = option.text
                item.addEventListener('click', function(){
                    select.value = option.value
                    hideSuggestions()
                    form.submit()
                })
                suggestions.appendChild(item)
            })
        }
        suggestions.style.display = 'block'
    }

    searchInput.addEventListener('input', debounce(function(){
        renderSuggestions(searchInput.value.trim().toLowerCase())
    }, 300))

    searchInput.addEventListener('focus', function(){
        if(searchInput.value.trim() !== ''){
            renderSuggestions(searchInput.value.trim().toLowerCase())
        }
    })

    document.addEventListener('click', function(e){
        if(e.target !== searchInput && !suggestions.contains(e.target)){
            hideSuggestions()
        }
    })
})
