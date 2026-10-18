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
    const searchInput = document.getElementById('student_table_search')
    if(!searchInput) return

    searchInput.addEventListener('input', debounce(function(){
        const term = searchInput.value.trim()
        const url = new URL(window.location.href)
        if(term === ''){
            url.searchParams.delete('search')
        }else{
            url.searchParams.set('search', term)
        }
        url.searchParams.set('page', '1')
        window.location.href = url.toString()
    }, 400))
});

/**
 * Everything below drives the single Add/Edit Student modal: Student Info
 * is tab 1 and must be saved first for a new student (it's what creates the
 * row and returns the id the other four tabs need); editing an existing
 * student unlocks every tab immediately. Parent/Guardian and Health Profile
 * are upsert-style (one row per student); Reading Level and Academic
 * Records use a small add/edit form plus a list of existing entries.
 */
(function(){
    const RELIGION_PRESETS = Array.from(document.querySelectorAll('#religion option')).map(function(o){ return o.value }).filter(Boolean)

    let currentStudentId = null
    let needsReload = false

    function csrfToken(){
        const meta = document.querySelector('meta[name="csrf-token"]')
        return meta ? meta.getAttribute('content') : ''
    }

    function notifySuccess(message){
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true
        })
    }

    function notifyError(message){
        Swal.fire({ icon: 'error', title: 'Error', text: message || 'Something went wrong.', confirmButtonColor: '#696cff' })
    }

    function setTabsEnabled(enabled){
        ['tab-btn-parent', 'tab-btn-health', 'tab-btn-reading', 'tab-btn-academic'].forEach(function(id){
            const btn = document.getElementById(id)
            if(!btn) return
            if(enabled){
                btn.removeAttribute('disabled')
            }else{
                btn.setAttribute('disabled', 'disabled')
            }
        })
        const hint = document.getElementById('studentModalHint')
        if(hint) hint.style.display = enabled ? 'none' : 'block'
    }

    function activateInfoTab(){
        const trigger = document.getElementById('tab-btn-info')
        if(trigger) bootstrap.Tab.getOrCreateInstance(trigger).show()
    }

    function resetStudentInfoForm(){
        const form = document.getElementById('studentInfoForm')
        form.reset()
        document.getElementById('student_id_field').value = ''
        document.getElementById('student_picture_preview').src = '../../../public/assets/img/avatars/1.png'
        document.getElementById('religion_other_wrapper').classList.add('d-none')
        document.getElementById('religion_other').value = ''
        resetAddressFields()
    }

    function populateStudentInfoForm(student){
        document.getElementById('student_id_field').value = student.id
        document.getElementById('lrn').value = student.lrn || ''
        document.getElementById('birth_date').value = student.birth_date || ''
        document.getElementById('first_name').value = student.first_name || ''
        document.getElementById('middle_name').value = student.middle_name || ''
        document.getElementById('last_name').value = student.last_name || ''
        document.getElementById('suffix').value = student.suffix || ''
        document.getElementById('gender').value = student.gender || ''
        document.getElementById('age_as_of_june').value = student.age_as_of_june || ''
        document.getElementById('mother_tongue').value = student.mother_tongue || ''
        document.getElementById('ip_ethnic_group').value = student.ip_ethnic_group || ''

        const religionSelect = document.getElementById('religion')
        const religionOtherWrapper = document.getElementById('religion_other_wrapper')
        const religionOther = document.getElementById('religion_other')
        if(student.religion && !RELIGION_PRESETS.includes(student.religion)){
            religionSelect.value = '__other__'
            religionOtherWrapper.classList.remove('d-none')
            religionOther.value = student.religion
        }else{
            religionSelect.value = student.religion || ''
            religionOtherWrapper.classList.add('d-none')
            religionOther.value = ''
        }

        document.getElementById('house_number').value = student.house_number || ''
        document.getElementById('street').value = student.street || ''
        document.getElementById('sitio').value = student.sitio || ''
        document.getElementById('purok').value = student.purok || ''
        populateAddressFields(student)
        document.getElementById('learning_modality').value = student.learning_modality || ''
        document.getElementById('remarks').value = student.remarks || ''

        const sectionSelect = document.getElementById('section_id')
        if(sectionSelect) sectionSelect.value = student.section_id || ''
        const gradeLevelInput = document.getElementById('grade_level_id')
        if(gradeLevelInput) gradeLevelInput.value = student.grade_level_id || ''

        document.getElementById('student_picture_preview').src = student.picture
            ? '../../../' + student.picture
            : '../../../public/assets/img/avatars/1.png'
    }

    document.getElementById('religion').addEventListener('change', function(){
        const wrapper = document.getElementById('religion_other_wrapper')
        if(this.value === '__other__'){
            wrapper.classList.remove('d-none')
        }else{
            wrapper.classList.add('d-none')
            document.getElementById('religion_other').value = ''
        }
    })

    document.getElementById('picture').addEventListener('change', function(){
        if(this.files && this.files[0]){
            document.getElementById('student_picture_preview').src = URL.createObjectURL(this.files[0])
        }
    })

    // LRN must be exactly 12 digits, numbers only. Strip anything non-numeric
    // as the teacher types (so letters can't even land in the field) and cap
    // length at 12, mirroring the server-side check in StudentsController.
    const lrnInput = document.getElementById('lrn')
    lrnInput.addEventListener('input', function(){
        const digitsOnly = this.value.replace(/\D/g, '').slice(0, 12)
        if(digitsOnly !== this.value) this.value = digitsOnly
        this.classList.remove('is-invalid')
    })

    function isValidLrn(value){
        return /^\d{12}$/.test(value)
    }

    // ===== Cascading Province -> City/Municipality -> Barangay =====

    let provincesLoaded = false

    function populateSelectOptions(selectEl, items, placeholderText){
        selectEl.innerHTML = ''
        const placeholder = document.createElement('option')
        placeholder.value = ''
        placeholder.textContent = placeholderText
        selectEl.appendChild(placeholder)
        items.forEach(function(item){
            const opt = document.createElement('option')
            opt.value = item.name
            opt.dataset.code = item.code
            opt.textContent = item.name
            selectEl.appendChild(opt)
        })
        const other = document.createElement('option')
        other.value = '__other__'
        other.textContent = 'Other (type manually)'
        selectEl.appendChild(other)
    }

    function loadProvinces(){
        return fetch('../../../app/controllers/AddressLookupController.php?level=provinces')
            .then(function(r){ return r.json() })
            .then(function(res){
                populateSelectOptions(document.getElementById('province'), res.data || [], 'Select Province')
                provincesLoaded = true
            })
    }

    function resetCitySelect(){
        const citySelect = document.getElementById('city_municipality')
        citySelect.innerHTML = '<option value="">Select province first</option>'
        citySelect.setAttribute('disabled', 'disabled')
        document.getElementById('city_municipality_other').classList.add('d-none')
        document.getElementById('city_municipality_other').value = ''
    }

    function resetBarangaySelect(){
        const brgySelect = document.getElementById('barangay')
        brgySelect.innerHTML = '<option value="">Select city/municipality first</option>'
        brgySelect.setAttribute('disabled', 'disabled')
        document.getElementById('barangay_other').classList.add('d-none')
        document.getElementById('barangay_other').value = ''
    }

    function resetAddressFields(){
        const provinceSelect = document.getElementById('province')
        if(provincesLoaded){
            provinceSelect.value = ''
        }
        document.getElementById('province_other').classList.add('d-none')
        document.getElementById('province_other').value = ''
        resetCitySelect()
        resetBarangaySelect()
    }

    function loadCitiesForProvince(code){
        return fetch('../../../app/controllers/AddressLookupController.php?level=cities&province_code=' + encodeURIComponent(code))
            .then(function(r){ return r.json() })
            .then(function(res){ return res.data || [] })
    }

    function loadBarangaysForCity(code){
        return fetch('../../../app/controllers/AddressLookupController.php?level=barangays&muncity_code=' + encodeURIComponent(code))
            .then(function(r){ return r.json() })
            .then(function(res){ return res.data || [] })
    }

    document.getElementById('province').addEventListener('change', function(){
        resetCitySelect()
        resetBarangaySelect()
        const otherInput = document.getElementById('province_other')
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
            return
        }
        otherInput.classList.add('d-none')
        otherInput.value = ''
        if(!this.value) return
        const code = this.options[this.selectedIndex].dataset.code
        loadCitiesForProvince(code).then(function(cities){
            const citySelect = document.getElementById('city_municipality')
            populateSelectOptions(citySelect, cities, 'Select City/Municipality')
            citySelect.removeAttribute('disabled')
        })
    })

    document.getElementById('city_municipality').addEventListener('change', function(){
        resetBarangaySelect()
        const otherInput = document.getElementById('city_municipality_other')
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
            return
        }
        otherInput.classList.add('d-none')
        otherInput.value = ''
        if(!this.value) return
        const code = this.options[this.selectedIndex].dataset.code
        loadBarangaysForCity(code).then(function(barangays){
            const brgySelect = document.getElementById('barangay')
            populateSelectOptions(brgySelect, barangays, 'Select Barangay')
            brgySelect.removeAttribute('disabled')
        })
    })

    document.getElementById('barangay').addEventListener('change', function(){
        const otherInput = document.getElementById('barangay_other')
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
        }else{
            otherInput.classList.add('d-none')
            otherInput.value = ''
        }
    })

    function selectOptionByName(selectEl, name){
        if(!name) return null
        const match = Array.from(selectEl.options).find(function(o){
            return o.value !== '' && o.value !== '__other__' && o.value.toLowerCase() === name.toLowerCase()
        })
        if(match) selectEl.value = match.value
        return match || null
    }

    function setAddressOther(selectId, otherId, rawValue){
        document.getElementById(selectId).value = '__other__'
        const otherInput = document.getElementById(otherId)
        otherInput.classList.remove('d-none')
        otherInput.value = rawValue || ''
    }

    /**
     * Edit mode only has the student's saved address as plain text (no PSGC
     * codes stored), so each level is matched by name against the loaded
     * options. A level that doesn't match falls back to "Other" with the
     * raw saved text — legacy/imported records won't always line up with
     * the PSGC list, and this keeps their data visible instead of blanking
     * it out. Everything below an unmatched level does the same, since
     * without a matched parent code there's nothing to cascade-fetch from.
     */
    async function populateAddressFields(student){
        resetAddressFields()
        if(!student.province) return

        if(!provincesLoaded){
            await loadProvinces()
        }
        const provinceSelect = document.getElementById('province')
        const provinceMatch = selectOptionByName(provinceSelect, student.province)
        if(!provinceMatch){
            setAddressOther('province', 'province_other', student.province)
            if(student.city_municipality) setAddressOther('city_municipality', 'city_municipality_other', student.city_municipality)
            if(student.barangay) setAddressOther('barangay', 'barangay_other', student.barangay)
            return
        }

        const citySelect = document.getElementById('city_municipality')
        const cities = await loadCitiesForProvince(provinceMatch.dataset.code)
        populateSelectOptions(citySelect, cities, 'Select City/Municipality')
        citySelect.removeAttribute('disabled')

        if(!student.city_municipality) return
        const cityMatch = selectOptionByName(citySelect, student.city_municipality)
        if(!cityMatch){
            setAddressOther('city_municipality', 'city_municipality_other', student.city_municipality)
            if(student.barangay) setAddressOther('barangay', 'barangay_other', student.barangay)
            return
        }

        const brgySelect = document.getElementById('barangay')
        const barangays = await loadBarangaysForCity(cityMatch.dataset.code)
        populateSelectOptions(brgySelect, barangays, 'Select Barangay')
        brgySelect.removeAttribute('disabled')

        if(!student.barangay) return
        const brgyMatch = selectOptionByName(brgySelect, student.barangay)
        if(!brgyMatch){
            setAddressOther('barangay', 'barangay_other', student.barangay)
        }
    }

    loadProvinces()

    window.openStudentModal = function(id){
        currentStudentId = id
        resetStudentInfoForm()
        resetReadingLevelForm()
        resetAcademicRecordForm()
        clearParentGuardianForm()
        clearHealthProfileForm()
        document.getElementById('readingLevelList').innerHTML = '<tr><td colspan="5" class="text-center text-muted">No reading level records yet.</td></tr>'
        document.getElementById('academicRecordList').innerHTML = '<tr><td colspan="5" class="text-center text-muted">No academic records yet.</td></tr>'

        if(id){
            document.getElementById('studentModalLabel').textContent = 'Edit Student'
            setTabsEnabled(true)
            fetch('../../../app/controllers/teacher/StudentsController.php?action=get&id=' + id)
                .then(function(r){ return r.json() })
                .then(function(res){
                    if(res.success && res.data){
                        populateStudentInfoForm(res.data)
                    }
                })
        }else{
            document.getElementById('studentModalLabel').textContent = 'Add Student'
            setTabsEnabled(false)
        }

        activateInfoTab()
        bootstrap.Modal.getOrCreateInstance(document.getElementById('studentModal')).show()
    }

    document.getElementById('studentModal').addEventListener('hidden.bs.modal', function(){
        if(needsReload){
            window.location.reload()
        }
    })

    document.getElementById('studentInfoForm').addEventListener('submit', function(e){
        e.preventDefault()
        const form = e.target

        if(!isValidLrn(lrnInput.value)){
            lrnInput.classList.add('is-invalid')
            lrnInput.focus()
            return
        }

        const formData = new FormData(form)

        const religionValue = document.getElementById('religion').value
        if(religionValue === '__other__'){
            formData.set('religion', document.getElementById('religion_other').value)
        }

        ;[['province', 'province_other'], ['city_municipality', 'city_municipality_other'], ['barangay', 'barangay_other']].forEach(function(pair){
            if(document.getElementById(pair[0]).value === '__other__'){
                formData.set(pair[0], document.getElementById(pair[1]).value)
            }
        })

        formData.set('csrf_token', csrfToken())
        formData.set('action', currentStudentId ? 'update' : 'create')

        const btn = document.getElementById('studentInfoSaveBtn')
        btn.disabled = true

        fetch('../../../app/controllers/teacher/StudentsController.php', {
            method: 'POST',
            body: formData
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                btn.disabled = false
                if(res.success){
                    needsReload = true
                    notifySuccess(res.message || 'Saved.')
                    if(!currentStudentId && res.student_id){
                        currentStudentId = res.student_id
                        document.getElementById('student_id_field').value = res.student_id
                        setTabsEnabled(true)
                    }
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){
                btn.disabled = false
                notifyError(err.message)
            })
    })

    // ===== Parent / Guardian (upsert) =====

    function clearParentGuardianForm(){
        ['pg_father_name', 'pg_father_occupation', 'pg_father_contact', 'pg_mother_name', 'pg_mother_occupation', 'pg_mother_contact', 'pg_guardian_name', 'pg_guardian_relationship', 'pg_guardian_contact'].forEach(function(id){
            document.getElementById(id).value = ''
        })
    }

    function loadParentGuardian(){
        clearParentGuardianForm()
        if(!currentStudentId) return
        fetch('../../../app/controllers/teacher/ParentGuardianController.php?student_id=' + currentStudentId)
            .then(function(r){ return r.json() })
            .then(function(res){
                const pg = res.data
                if(!pg) return
                document.getElementById('pg_father_name').value = pg.father_name || ''
                document.getElementById('pg_father_occupation').value = pg.father_occupation || ''
                document.getElementById('pg_father_contact').value = pg.father_contact || ''
                document.getElementById('pg_mother_name').value = pg.mother_name || ''
                document.getElementById('pg_mother_occupation').value = pg.mother_occupation || ''
                document.getElementById('pg_mother_contact').value = pg.mother_contact || ''
                document.getElementById('pg_guardian_name').value = pg.guardian_name || ''
                document.getElementById('pg_guardian_relationship').value = pg.guardian_relationship || ''
                document.getElementById('pg_guardian_contact').value = pg.guardian_contact || ''
            })
    }

    document.getElementById('parentGuardianForm').addEventListener('submit', function(e){
        e.preventDefault()
        if(!currentStudentId) return
        const payload = {
            student_id: currentStudentId,
            father_name: document.getElementById('pg_father_name').value,
            father_occupation: document.getElementById('pg_father_occupation').value,
            father_contact: document.getElementById('pg_father_contact').value,
            mother_name: document.getElementById('pg_mother_name').value,
            mother_occupation: document.getElementById('pg_mother_occupation').value,
            mother_contact: document.getElementById('pg_mother_contact').value,
            guardian_name: document.getElementById('pg_guardian_name').value,
            guardian_relationship: document.getElementById('pg_guardian_relationship').value,
            guardian_contact: document.getElementById('pg_guardian_contact').value,
            csrf_token: csrfToken()
        }
        fetch('../../../app/controllers/teacher/ParentGuardianController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Saved.')
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })

    // ===== Health Profile (upsert, with live BMI) =====

    const BMI_THRESHOLDS = [
        { max: 16.0, classification: 'Severely Wasted' },
        { max: 18.5, classification: 'Wasted' },
        { max: 25.0, classification: 'Normal' },
        { max: 30.0, classification: 'Overweight' },
        { max: Infinity, classification: 'Obese' }
    ]

    function classifyBmi(bmi){
        for(let i = 0; i < BMI_THRESHOLDS.length; i++){
            if(bmi < BMI_THRESHOLDS[i].max) return BMI_THRESHOLDS[i].classification
        }
        return 'Obese'
    }

    function badgeClass(classification){
        return 'bmi-badge-' + classification.toLowerCase().replace(/ /g, '-')
    }

    function recalcBmi(){
        const height = parseFloat(document.getElementById('hp_height_cm').value)
        const weight = parseFloat(document.getElementById('hp_weight_kg').value)
        const bmiInput = document.getElementById('hp_bmi')
        const classificationHidden = document.getElementById('hp_bmi_classification')
        const badge = document.getElementById('hp_bmi_classification_badge')

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

    document.getElementById('hp_height_cm').addEventListener('input', recalcBmi)
    document.getElementById('hp_weight_kg').addEventListener('input', recalcBmi)

    function toggleMedicalConditionDetail(){
        const hasCondition = document.getElementById('hp_has_medical_condition').value === 'Yes'
        document.getElementById('hp_medical_conditions_wrapper').classList.toggle('d-none', !hasCondition)
    }

    document.getElementById('hp_has_medical_condition').addEventListener('change', toggleMedicalConditionDetail)

    function clearHealthProfileForm(){
        ['hp_height_cm', 'hp_weight_kg', 'hp_bmi', 'hp_vision_screening_result', 'hp_hearing_screening_result', 'hp_allergies', 'hp_medical_conditions', 'hp_immunization_status'].forEach(function(id){
            document.getElementById(id).value = ''
        })
        document.getElementById('hp_blood_type').value = ''
        document.getElementById('hp_has_medical_condition').value = 'No'
        toggleMedicalConditionDetail()
        recalcBmi()
    }

    function loadHealthProfile(){
        clearHealthProfileForm()
        if(!currentStudentId) return
        fetch('../../../app/controllers/teacher/StudentHealthController.php?student_id=' + currentStudentId)
            .then(function(r){ return r.json() })
            .then(function(res){
                const hp = res.data
                if(!hp) return
                document.getElementById('hp_height_cm').value = hp.height_cm || ''
                document.getElementById('hp_weight_kg').value = hp.weight_kg || ''
                document.getElementById('hp_blood_type').value = hp.blood_type || ''
                document.getElementById('hp_vision_screening_result').value = hp.vision_screening_result || ''
                document.getElementById('hp_hearing_screening_result').value = hp.hearing_screening_result || ''
                document.getElementById('hp_allergies').value = hp.allergies || ''
                document.getElementById('hp_has_medical_condition').value = hp.has_medical_condition === 'Yes' ? 'Yes' : 'No'
                document.getElementById('hp_medical_conditions').value = hp.medical_conditions || ''
                toggleMedicalConditionDetail()
                document.getElementById('hp_immunization_status').value = hp.immunization_status || ''
                recalcBmi()
            })
    }

    document.getElementById('healthProfileForm').addEventListener('submit', function(e){
        e.preventDefault()
        if(!currentStudentId) return
        const payload = {
            student_id: currentStudentId,
            school_year_id: document.getElementById('school_year_id') ? document.getElementById('school_year_id').value : null,
            height_cm: document.getElementById('hp_height_cm').value,
            weight_kg: document.getElementById('hp_weight_kg').value,
            blood_type: document.getElementById('hp_blood_type').value,
            vision_screening_result: document.getElementById('hp_vision_screening_result').value,
            hearing_screening_result: document.getElementById('hp_hearing_screening_result').value,
            allergies: document.getElementById('hp_allergies').value,
            has_medical_condition: document.getElementById('hp_has_medical_condition').value,
            medical_conditions: document.getElementById('hp_medical_conditions').value,
            immunization_status: document.getElementById('hp_immunization_status').value,
            csrf_token: csrfToken()
        }
        fetch('../../../app/controllers/teacher/StudentHealthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Saved.')
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })

    // ===== Reading Level (mini add-form + list) =====

    function resetReadingLevelForm(){
        document.getElementById('readingLevelForm').reset()
        document.getElementById('rl_edit_id').value = ''
        document.getElementById('rl_submit_btn').textContent = 'Add'
        document.getElementById('rl_cancel_btn').classList.add('d-none')
    }

    function renderReadingLevelList(rows){
        const tbody = document.getElementById('readingLevelList')
        tbody.innerHTML = ''
        if(!rows || rows.length === 0){
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No reading level records yet.</td></tr>'
            return
        }
        rows.forEach(function(row){
            const tr = document.createElement('tr')
            tr.innerHTML =
                '<td>' + escapeHtml(row.assessment_date || '') + '</td>' +
                '<td>' + escapeHtml(row.reading_level) + '</td>' +
                '<td>' + escapeHtml(row.reading_language) + '</td>' +
                '<td>' + escapeHtml(row.remarks || '') + '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-sm btn-warning rl-edit-btn"><i class="bx bx-edit"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-danger rl-delete-btn"><i class="bx bx-trash"></i></button>' +
                '</td>'
            tr.querySelector('.rl-edit-btn').addEventListener('click', function(){
                editReadingLevel(row.id, row.assessment_date, row.reading_level, row.reading_language, row.remarks)
            })
            tr.querySelector('.rl-delete-btn').addEventListener('click', function(){
                deleteReadingLevel(row.id)
            })
            tbody.appendChild(tr)
        })
    }

    function escapeHtml(value){
        const div = document.createElement('div')
        div.textContent = value == null ? '' : value
        return div.innerHTML
    }

    function loadReadingLevels(){
        if(!currentStudentId) return
        fetch('../../../app/controllers/teacher/ReadingLevelController.php?student_id=' + currentStudentId)
            .then(function(r){ return r.json() })
            .then(function(res){ renderReadingLevelList(res.data) })
    }

    function editReadingLevel(id, assessmentDate, readingLevel, readingLanguage, remarks){
        document.getElementById('rl_edit_id').value = id
        document.getElementById('rl_assessment_date').value = assessmentDate
        document.getElementById('rl_reading_level').value = readingLevel
        document.getElementById('rl_reading_language').value = readingLanguage
        document.getElementById('rl_remarks').value = remarks || ''
        document.getElementById('rl_submit_btn').textContent = 'Update'
        document.getElementById('rl_cancel_btn').classList.remove('d-none')
    }

    document.getElementById('rl_cancel_btn').addEventListener('click', resetReadingLevelForm)

    function deleteReadingLevel(id){
        Swal.fire({
            icon: 'warning', title: 'Delete this record?', text: 'This action cannot be undone.',
            showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete'
        }).then(function(result){
            if(!result.isConfirmed) return
            fetch('../../../app/controllers/teacher/ReadingLevelController.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, csrf_token: csrfToken() })
            })
                .then(function(r){ return r.json() })
                .then(function(res){
                    if(res.success){
                        notifySuccess(res.message || 'Deleted.')
                        loadReadingLevels()
                    }else{
                        notifyError(res.message)
                    }
                })
        })
    }

    document.getElementById('readingLevelForm').addEventListener('submit', function(e){
        e.preventDefault()
        if(!currentStudentId) return
        const editId = document.getElementById('rl_edit_id').value
        const payload = {
            student_id: currentStudentId,
            school_year_id: document.getElementById('school_year_id') ? document.getElementById('school_year_id').value : null,
            assessment_date: document.getElementById('rl_assessment_date').value,
            reading_level: document.getElementById('rl_reading_level').value,
            reading_language: document.getElementById('rl_reading_language').value,
            remarks: document.getElementById('rl_remarks').value,
            csrf_token: csrfToken()
        }
        if(editId){
            payload.id = editId
        }
        fetch('../../../app/controllers/teacher/ReadingLevelController.php', {
            method: editId ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Saved.')
                    resetReadingLevelForm()
                    loadReadingLevels()
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })

    // ===== Academic Records (mini add-form + list) =====

    function resetAcademicRecordForm(){
        document.getElementById('academicRecordForm').reset()
        document.getElementById('ap_edit_id').value = ''
        document.getElementById('ap_submit_btn').textContent = 'Add'
        document.getElementById('ap_cancel_btn').classList.add('d-none')
    }

    function renderAcademicRecordList(rows){
        const tbody = document.getElementById('academicRecordList')
        tbody.innerHTML = ''
        if(!rows || rows.length === 0){
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No academic records yet.</td></tr>'
            return
        }
        rows.forEach(function(row){
            const tr = document.createElement('tr')
            tr.innerHTML =
                '<td>' + escapeHtml(row.subject_name) + '</td>' +
                '<td>' + escapeHtml(row.grading_period) + '</td>' +
                '<td>' + escapeHtml(row.grade) + '</td>' +
                '<td>' + escapeHtml(row.remarks || '') + '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-sm btn-warning ap-edit-btn"><i class="bx bx-edit"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-danger ap-delete-btn"><i class="bx bx-trash"></i></button>' +
                '</td>'
            tr.querySelector('.ap-edit-btn').addEventListener('click', function(){
                editAcademicRecord(row.id, row.subject_name, row.grading_period, row.grade, row.remarks)
            })
            tr.querySelector('.ap-delete-btn').addEventListener('click', function(){
                deleteAcademicRecord(row.id)
            })
            tbody.appendChild(tr)
        })
    }

    function loadAcademicRecords(){
        if(!currentStudentId) return
        fetch('../../../app/controllers/teacher/AcademicProfileController.php?student_id=' + currentStudentId)
            .then(function(r){ return r.json() })
            .then(function(res){ renderAcademicRecordList(res.data) })
    }

    function editAcademicRecord(id, subjectName, gradingPeriod, grade, remarks){
        document.getElementById('ap_edit_id').value = id
        document.getElementById('ap_subject_name').value = subjectName
        document.getElementById('ap_grading_period').value = gradingPeriod
        document.getElementById('ap_grade').value = grade
        document.getElementById('ap_remarks').value = remarks || ''
        document.getElementById('ap_submit_btn').textContent = 'Update'
        document.getElementById('ap_cancel_btn').classList.remove('d-none')
    }

    document.getElementById('ap_cancel_btn').addEventListener('click', resetAcademicRecordForm)

    function deleteAcademicRecord(id){
        Swal.fire({
            icon: 'warning', title: 'Delete this record?', text: 'This action cannot be undone.',
            showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete'
        }).then(function(result){
            if(!result.isConfirmed) return
            fetch('../../../app/controllers/teacher/AcademicProfileController.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, csrf_token: csrfToken() })
            })
                .then(function(r){ return r.json() })
                .then(function(res){
                    if(res.success){
                        notifySuccess(res.message || 'Deleted.')
                        loadAcademicRecords()
                    }else{
                        notifyError(res.message)
                    }
                })
        })
    }

    document.getElementById('academicRecordForm').addEventListener('submit', function(e){
        e.preventDefault()
        if(!currentStudentId) return
        const editId = document.getElementById('ap_edit_id').value
        const payload = {
            student_id: currentStudentId,
            school_year_id: document.getElementById('school_year_id') ? document.getElementById('school_year_id').value : null,
            subject_name: document.getElementById('ap_subject_name').value,
            grading_period: document.getElementById('ap_grading_period').value,
            grade: document.getElementById('ap_grade').value,
            remarks: document.getElementById('ap_remarks').value,
            csrf_token: csrfToken()
        }
        if(editId){
            payload.id = editId
        }
        fetch('../../../app/controllers/teacher/AcademicProfileController.php', {
            method: editId ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Saved.')
                    resetAcademicRecordForm()
                    loadAcademicRecords()
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })

    // Lazy-load each tab's data the moment it's shown.
    document.getElementById('tab-btn-parent').addEventListener('shown.bs.tab', loadParentGuardian)
    document.getElementById('tab-btn-health').addEventListener('shown.bs.tab', loadHealthProfile)
    document.getElementById('tab-btn-reading').addEventListener('shown.bs.tab', function(){
        resetReadingLevelForm()
        loadReadingLevels()
    })
    document.getElementById('tab-btn-academic').addEventListener('shown.bs.tab', function(){
        resetAcademicRecordForm()
        loadAcademicRecords()
    })
})()
