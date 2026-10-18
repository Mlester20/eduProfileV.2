function editGradeLevel(id, grade_name){
    document.getElementById('edit_grade_level_id').value = id;
    document.getElementById('edit_grade_name').value = grade_name
}

// Grade Levels no longer have their own management table — these three
// functions back the "+ Add New Grade Level" option and the rename/delete
// icons next to the Grade Level dropdown inside the Add/Edit Section forms.

function toggleNewGradeLevelInput(selectEl, wrapperId){
    const wrapper = document.getElementById(wrapperId)
    const input = wrapper.querySelector('input')
    const show = selectEl.value === '__new__'
    wrapper.classList.toggle('d-none', !show)
    if(input) input.required = show
}

function renameSelectedGradeLevel(selectId){
    const select = document.getElementById(selectId)
    if(!select.value || select.value === '__new__'){
        alert('Please select an existing grade level first.')
        return
    }
    const name = select.options[select.selectedIndex].text
    editGradeLevel(select.value, name)
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editGradeLevel')).show()
}

function deleteSelectedGradeLevel(selectId){
    const select = document.getElementById(selectId)
    if(!select.value || select.value === '__new__'){
        alert('Please select an existing grade level first.')
        return
    }
    const name = select.options[select.selectedIndex].text
    if(!confirm('Delete grade level "' + name + '"? Any sections still using it may be affected. This action cannot be undone.')){
        return
    }

    const form = document.createElement('form')
    form.method = 'post'
    form.action = '../../../app/controllers/' + document.body.dataset.role + '/GradeLevelsController.php'

    const fields = {
        id: select.value,
        delete_grade_level: '1',
        csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
    Object.keys(fields).forEach(function(key){
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = key
        input.value = fields[key]
        form.appendChild(input)
    })

    document.body.appendChild(form)
    form.submit()
}
