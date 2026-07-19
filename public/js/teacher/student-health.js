document.addEventListener('DOMContentLoaded', function(){

    // Simplified adult BMI cutoffs (mirrors StudentHealthService::computeBmi on
    // the server, which is the value that actually gets saved). Not DepEd's
    // BMI-for-age percentile chart — swap both sides together if that changes.
    const BMI_THRESHOLDS = [
        { max: 16.0, classification: 'Severely Wasted' },
        { max: 18.5, classification: 'Wasted' },
        { max: 25.0, classification: 'Normal' },
        { max: 30.0, classification: 'Overweight' },
        { max: Infinity, classification: 'Obese' }
    ]

    function classifyBmi(bmi){
        for(let i = 0; i < BMI_THRESHOLDS.length; i++){
            if(bmi < BMI_THRESHOLDS[i].max){
                return BMI_THRESHOLDS[i].classification
            }
        }
        return 'Obese'
    }

    function badgeClass(classification){
        return 'bmi-badge-' + classification.toLowerCase().replace(/ /g, '-')
    }

    function wireBmiCalculation(heightId, weightId, bmiId, classificationHiddenId, badgeId){
        const heightInput = document.getElementById(heightId)
        const weightInput = document.getElementById(weightId)
        const bmiInput = document.getElementById(bmiId)
        const classificationHidden = document.getElementById(classificationHiddenId)
        const badge = document.getElementById(badgeId)
        if(!heightInput || !weightInput || !bmiInput || !classificationHidden || !badge) return

        function recalc(){
            const height = parseFloat(heightInput.value)
            const weight = parseFloat(weightInput.value)

            if(!height || !weight){
                bmiInput.value = ''
                classificationHidden.value = ''
                badge.textContent = '—'
                badge.className = 'bmi-badge'
                return
            }

            const heightM = height / 100
            const bmi = Math.round((weight / (heightM * heightM)) * 100) / 100
            const classification = classifyBmi(bmi)

            bmiInput.value = bmi
            classificationHidden.value = classification
            badge.textContent = classification
            badge.className = 'bmi-badge ' + badgeClass(classification)
        }

        heightInput.addEventListener('input', recalc)
        weightInput.addEventListener('input', recalc)

        return recalc
    }

    const recalcCreate = wireBmiCalculation('height_cm', 'weight_kg', 'bmi', 'bmi_classification', 'bmi_classification_badge')
    const recalcEdit = wireBmiCalculation('edit_height_cm', 'edit_weight_kg', 'edit_bmi', 'edit_bmi_classification', 'edit_bmi_classification_badge')

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

    // Swaps the plain <select> student picker for a search box + suggestion
    // list, since advisories can run long enough that scrolling a dropdown
    // is painful. The select stays in the DOM (hidden) so form submission —
    // and the rest of this file, which reads student_id off it — is unaffected.
    function setupStudentAutocomplete(searchInputId, selectId, suggestionsId){
        const searchInput = document.getElementById(searchInputId)
        const select = document.getElementById(selectId)
        const suggestions = document.getElementById(suggestionsId)
        if(!searchInput || !select || !suggestions) return null

        select.classList.add('d-none')
        select.removeAttribute('required')

        function currentOptions(){
            return Array.from(select.options).filter(function(option){
                return option.value !== ''
            })
        }

        function hideSuggestions(){
            suggestions.innerHTML = ''
            suggestions.style.display = 'none'
        }

        function renderSuggestions(filter){
            if(filter === ''){
                hideSuggestions()
                return
            }
            const matches = currentOptions().filter(function(option){
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

    function formToPayload(form){
        const formData = new FormData(form)
        const payload = {}
        formData.forEach(function(value, key){
            payload[key] = value
        })
        return payload
    }

    function submitHealthProfile(url, method, payload){
        payload.csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
            .then(function(response){ return response.json() })
            .then(function(data){
                if(data.success){
                    // Success is surfaced via FlashMessage::showFlash() after the
                    // reload below (same convention as the rest of the app), so
                    // no SweetAlert here — otherwise the teacher sees it twice.
                    window.location.reload()
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to save health profile.',
                        confirmButtonColor: '#696cff'
                    })
                }
            })
            .catch(function(error){
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: error.message,
                    confirmButtonColor: '#696cff'
                })
            })
    }

    const createForm = document.getElementById('createHealthProfileForm')
    if(createForm){
        createForm.addEventListener('submit', function(e){
            e.preventDefault()
            const studentSelect = document.getElementById('student_id')
            if(studentSelect && !studentSelect.value){
                return
            }
            const payload = formToPayload(createForm)
            submitHealthProfile('student-health.php', 'POST', payload)
        })
    }

    const editForm = document.getElementById('editHealthProfileForm')
    if(editForm){
        editForm.addEventListener('submit', function(e){
            e.preventDefault()
            const studentSelect = document.getElementById('edit_student_id')
            if(studentSelect && !studentSelect.value){
                return
            }
            const payload = formToPayload(editForm)
            submitHealthProfile('student-health.php', 'PUT', payload)
        })
    }

    window.editHealthProfile = function(id, studentId, schoolYearId, heightCm, weightKg, bloodType, allergies, medicalConditions, vision, hearing, immunization){
        document.getElementById('edit_health_profile_id').value = id
        if(editStudentAutocomplete){
            editStudentAutocomplete.setSelection(studentId)
        }else{
            document.getElementById('edit_student_id').value = studentId
        }
        document.getElementById('edit_school_year_id').value = schoolYearId
        document.getElementById('edit_height_cm').value = heightCm
        document.getElementById('edit_weight_kg').value = weightKg
        document.getElementById('edit_blood_type').value = bloodType || ''
        document.getElementById('edit_vision_screening_result').value = vision || ''
        document.getElementById('edit_hearing_screening_result').value = hearing || ''
        document.getElementById('edit_allergies').value = allergies || ''
        document.getElementById('edit_medical_conditions').value = medicalConditions || ''
        document.getElementById('edit_immunization_status').value = immunization || ''

        // Recompute from the stored height/weight rather than trusting the
        // saved bmi/bmi_classification display values.
        if(recalcEdit) recalcEdit()
    }

    window.viewHealthProfile = function(studentName, schoolYear, bloodType, bmi, bmiClassification, allergies, medicalConditions, vision, hearing, immunization){
        document.getElementById('view_student_name').textContent = studentName
        document.getElementById('view_school_year').textContent = schoolYear
        document.getElementById('view_blood_type').textContent = bloodType || '—'
        document.getElementById('view_bmi').textContent = bmi || '—'
        document.getElementById('view_bmi_classification').textContent = bmiClassification || '—'
        document.getElementById('view_allergies').textContent = allergies || '—'
        document.getElementById('view_medical_conditions').textContent = medicalConditions || '—'
        document.getElementById('view_vision_screening_result').textContent = vision || '—'
        document.getElementById('view_hearing_screening_result').textContent = hearing || '—'
        document.getElementById('view_immunization_status').textContent = immunization || '—'
    }

    // The row itself opens the view-details modal on click, but Edit/Delete
    // buttons live inside that same row. Bootstrap's data-bs-toggle would
    // normally toggle the modal for ANY click that bubbles up to the row
    // regardless of stopPropagation() timing, so instead of relying on
    // data-bs-toggle on the <tr>, we open the modal manually here and bail
    // out entirely whenever the click originated from a button.
    window.handleHealthRowClick = function(event, studentName, schoolYear, bloodType, bmi, bmiClassification, allergies, medicalConditions, vision, hearing, immunization){
        if(event.target.closest('button')) return

        viewHealthProfile(studentName, schoolYear, bloodType, bmi, bmiClassification, allergies, medicalConditions, vision, hearing, immunization)

        const modalEl = document.getElementById('viewHealthProfileModal')
        if(modalEl){
            bootstrap.Modal.getOrCreateInstance(modalEl).show()
        }
    }

    window.deleteHealthProfile = function(id){
        Swal.fire({
            icon: 'warning',
            title: 'Delete this record?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then(function(result){
            if(!result.isConfirmed) return

            fetch('student-health.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: id, csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content') })
            })
                .then(function(response){ return response.json() })
                .then(function(data){
                    if(data.success){
                        // Same reasoning as submitHealthProfile: flash shows the
                        // confirmation once the page reloads.
                        window.location.reload()
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to delete health profile.',
                            confirmButtonColor: '#696cff'
                        })
                    }
                })
                .catch(function(error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: error.message,
                        confirmButtonColor: '#696cff'
                    })
                })
        })
    }
})
