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

function setupStudentAutocomplete(searchInputId, selectId, suggestionsId){
    const searchInput = document.getElementById(searchInputId)
    const select = document.getElementById(selectId)
    const suggestions = document.getElementById(suggestionsId)
    if(!searchInput || !select || !suggestions) return null

    // Drive selection from the visible search box + suggestion list; the
    // select stays in the DOM (hidden) so form submission is unaffected.
    select.classList.add('d-none')
    select.removeAttribute('required')

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
            empty.textContent = 'No students found'
            suggestions.appendChild(empty)
        }else{
            matches.forEach(function(option){
                const item = document.createElement('button')
                item.type = 'button'
                item.className = 'list-group-item list-group-item-action bg-white'
                item.textContent = option.text
                item.addEventListener('click', function(){
                    select.value = option.value
                    searchInput.value = option.text
                    searchInput.classList.remove('is-invalid')
                    hideSuggestions()
                })
                suggestions.appendChild(item)
            })
        }
        suggestions.style.display = 'block'
    }

    searchInput.addEventListener('input', debounce(function(){
        select.value = ''
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

    const form = searchInput.closest('form')
    if(form){
        form.addEventListener('submit', function(e){
            if(!select.value){
                e.preventDefault()
                searchInput.classList.add('is-invalid')
                searchInput.focus()
            }
        })
    }

    return {
        setSelection: function(studentId){
            select.value = studentId
            const selected = select.options[select.selectedIndex]
            searchInput.value = (selected && selected.value) ? selected.text : ''
            searchInput.classList.remove('is-invalid')
            hideSuggestions()
        }
    }
}

const createStudentAutocomplete = setupStudentAutocomplete('student_id_search', 'student_id', 'student_id_suggestions')
const editStudentAutocomplete = setupStudentAutocomplete('edit_student_id_search', 'edit_student_id', 'edit_student_id_suggestions')

function editAcademicProfile(id, student_id, school_year_id, subject_name, grading_period, grade, remarks){
    document.getElementById('edit_academic_id').value = id;
    if(editStudentAutocomplete){
        editStudentAutocomplete.setSelection(student_id)
    }else{
        document.getElementById('edit_student_id').value = student_id;
    }
    document.getElementById('edit_school_year_id').value = school_year_id;
    document.getElementById('edit_subject_name').value = subject_name;
    document.getElementById('edit_grading_period').value = grading_period;
    document.getElementById('edit_grade').value = grade;
    document.getElementById('edit_remarks').value = remarks;
}
