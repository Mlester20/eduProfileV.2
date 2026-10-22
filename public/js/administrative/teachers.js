function forceUppercase(input){
    const start = input.selectionStart
    const end = input.selectionEnd
    input.value = input.value.toUpperCase()
    input.setSelectionRange(start, end)
}

document.addEventListener('DOMContentLoaded', function(){
    ['fullName', 'editFullName'].forEach(function(id){
        const input = document.getElementById(id)
        if(input){
            input.addEventListener('input', function(){ forceUppercase(input) })
        }
    })

    // Name/word-only fields -- strip digits as typed. Position's custom
    // "Other" text still allows Roman numerals (Teacher II) since those are
    // letters, not digits. Left alone on purpose: Email, Contact Number,
    // Birth Date, Sex, Province/City/Barangay (Philippine address names
    // routinely include numbers, e.g. "Barangay 176"), and both password
    // fields.
    ;['fullName', 'editFullName', 'position_other', 'editPosition_other'].forEach(function(id){
        const input = document.getElementById(id)
        if(!input) return
        input.addEventListener('input', function(){
            const noDigits = this.value.replace(/[0-9]/g, '')
            if(noDigits !== this.value) this.value = noDigits
        })
    })
})

// ===== Position (preset dropdown + "Other" fallback) =====
// Same Other-fallback idea as the address pickers below, just without any
// cascading -- a flat preset list plus a free-text escape hatch.

const POSITION_PRESETS = Array.from(document.querySelectorAll('#position option')).map(function(o){ return o.value }).filter(Boolean)

function positionValueOrOther(selectId, otherId){
    const select = document.getElementById(selectId)
    return select.value === '__other__' ? document.getElementById(otherId).value : select.value
}

function populatePosition(selectId, otherId, value){
    const select = document.getElementById(selectId)
    const otherInput = document.getElementById(otherId)
    if(value && !POSITION_PRESETS.includes(value)){
        select.value = '__other__'
        otherInput.classList.remove('d-none')
        otherInput.value = value
    }else{
        select.value = value || ''
        otherInput.classList.add('d-none')
        otherInput.value = ''
    }
}

function wirePositionToggle(selectId, otherId){
    document.getElementById(selectId).addEventListener('change', function(){
        const otherInput = document.getElementById(otherId)
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
        }else{
            otherInput.classList.add('d-none')
            otherInput.value = ''
        }
    })
}

wirePositionToggle('position', 'position_other')
wirePositionToggle('editPosition', 'editPosition_other')

// ===== Cascading Province -> City/Municipality -> Barangay =====
// Same PSGC-backed picker used for students/parents-guardians, trimmed to
// just these three levels (no house#/street/sitio/purok for teachers).
// These forms are plain native POST (no fetch/AJAX), so each picker also
// writes its resolved value into a hidden input right before submit.

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

const teacherAddressPickers = []
let teacherProvincesCache = null
let teacherProvincesLoaded = false

function createTeacherAddressPicker(ids){
    function resetCity(){
        const citySelect = document.getElementById(ids.city)
        citySelect.innerHTML = '<option value="">Select province first</option>'
        citySelect.setAttribute('disabled', 'disabled')
        document.getElementById(ids.cityOther).classList.add('d-none')
        document.getElementById(ids.cityOther).value = ''
    }

    function resetBarangay(){
        const brgySelect = document.getElementById(ids.barangay)
        brgySelect.innerHTML = '<option value="">Select city/municipality first</option>'
        brgySelect.setAttribute('disabled', 'disabled')
        document.getElementById(ids.barangayOther).classList.add('d-none')
        document.getElementById(ids.barangayOther).value = ''
    }

    function reset(){
        if(teacherProvincesLoaded){
            document.getElementById(ids.province).value = ''
        }
        document.getElementById(ids.provinceOther).classList.add('d-none')
        document.getElementById(ids.provinceOther).value = ''
        resetCity()
        resetBarangay()
    }

    function populateProvinces(){
        populateSelectOptions(document.getElementById(ids.province), teacherProvincesCache || [], 'Select Province')
    }

    document.getElementById(ids.province).addEventListener('change', function(){
        resetCity()
        resetBarangay()
        const otherInput = document.getElementById(ids.provinceOther)
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
            return
        }
        otherInput.classList.add('d-none')
        otherInput.value = ''
        if(!this.value) return
        const code = this.options[this.selectedIndex].dataset.code
        loadCitiesForProvince(code).then(function(cities){
            const citySelect = document.getElementById(ids.city)
            populateSelectOptions(citySelect, cities, 'Select City/Municipality')
            citySelect.removeAttribute('disabled')
        })
    })

    document.getElementById(ids.city).addEventListener('change', function(){
        resetBarangay()
        const otherInput = document.getElementById(ids.cityOther)
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
            return
        }
        otherInput.classList.add('d-none')
        otherInput.value = ''
        if(!this.value) return
        const code = this.options[this.selectedIndex].dataset.code
        loadBarangaysForCity(code).then(function(barangays){
            const brgySelect = document.getElementById(ids.barangay)
            populateSelectOptions(brgySelect, barangays, 'Select Barangay')
            brgySelect.removeAttribute('disabled')
        })
    })

    document.getElementById(ids.barangay).addEventListener('change', function(){
        const otherInput = document.getElementById(ids.barangayOther)
        if(this.value === '__other__'){
            otherInput.classList.remove('d-none')
        }else{
            otherInput.classList.add('d-none')
            otherInput.value = ''
        }
    })

    async function populate(provinceVal, cityVal, barangayVal){
        reset()
        if(!provinceVal) return

        if(!teacherProvincesLoaded){
            await loadTeacherProvinces()
        }
        const provinceSelect = document.getElementById(ids.province)
        const provinceMatch = selectOptionByName(provinceSelect, provinceVal)
        if(!provinceMatch){
            setAddressOther(ids.province, ids.provinceOther, provinceVal)
            if(cityVal) setAddressOther(ids.city, ids.cityOther, cityVal)
            if(barangayVal) setAddressOther(ids.barangay, ids.barangayOther, barangayVal)
            return
        }

        const citySelect = document.getElementById(ids.city)
        const cities = await loadCitiesForProvince(provinceMatch.dataset.code)
        populateSelectOptions(citySelect, cities, 'Select City/Municipality')
        citySelect.removeAttribute('disabled')

        if(!cityVal) return
        const cityMatch = selectOptionByName(citySelect, cityVal)
        if(!cityMatch){
            setAddressOther(ids.city, ids.cityOther, cityVal)
            if(barangayVal) setAddressOther(ids.barangay, ids.barangayOther, barangayVal)
            return
        }

        const brgySelect = document.getElementById(ids.barangay)
        const barangays = await loadBarangaysForCity(cityMatch.dataset.code)
        populateSelectOptions(brgySelect, barangays, 'Select Barangay')
        brgySelect.removeAttribute('disabled')

        if(!barangayVal) return
        const brgyMatch = selectOptionByName(brgySelect, barangayVal)
        if(!brgyMatch){
            setAddressOther(ids.barangay, ids.barangayOther, barangayVal)
        }
    }

    function valueOrOther(selectId, otherId){
        const select = document.getElementById(selectId)
        return select.value === '__other__' ? document.getElementById(otherId).value : select.value
    }

    function getValues(){
        return {
            province: valueOrOther(ids.province, ids.provinceOther),
            city_municipality: valueOrOther(ids.city, ids.cityOther),
            barangay: valueOrOther(ids.barangay, ids.barangayOther)
        }
    }

    const picker = { reset, populateProvinces, populate, getValues }
    teacherAddressPickers.push(picker)
    return picker
}

function loadTeacherProvinces(){
    return fetch('../../../app/controllers/AddressLookupController.php?level=provinces')
        .then(function(r){ return r.json() })
        .then(function(res){
            teacherProvincesCache = res.data || []
            teacherProvincesLoaded = true
            teacherAddressPickers.forEach(function(picker){ picker.populateProvinces() })
        })
}

const createAddressPicker = createTeacherAddressPicker({
    province: 'province', provinceOther: 'province_other',
    city: 'city_municipality', cityOther: 'city_municipality_other',
    barangay: 'barangay', barangayOther: 'barangay_other'
})

const editAddressPicker = createTeacherAddressPicker({
    province: 'editProvince', provinceOther: 'editProvince_other',
    city: 'editCityMunicipality', cityOther: 'editCityMunicipality_other',
    barangay: 'editBarangay', barangayOther: 'editBarangay_other'
})

document.addEventListener('DOMContentLoaded', function(){
    loadTeacherProvinces()

    function fillHiddenAddressInputs(picker, hiddenIds){
        const values = picker.getValues()
        document.getElementById(hiddenIds.province).value = values.province
        document.getElementById(hiddenIds.city).value = values.city_municipality
        document.getElementById(hiddenIds.barangay).value = values.barangay
    }

    const teacherForm = document.getElementById('teacherForm')
    if(teacherForm){
        teacherForm.addEventListener('submit', function(){
            fillHiddenAddressInputs(createAddressPicker, {
                province: 'province_value', city: 'city_municipality_value', barangay: 'barangay_value'
            })
            document.getElementById('position_value').value = positionValueOrOther('position', 'position_other')
        })
    }

    const editTeacherForm = document.getElementById('editTeacherForm')
    if(editTeacherForm){
        editTeacherForm.addEventListener('submit', function(){
            fillHiddenAddressInputs(editAddressPicker, {
                province: 'editProvince_value', city: 'editCityMunicipality_value', barangay: 'editBarangay_value'
            })
            document.getElementById('editPosition_value').value = positionValueOrOther('editPosition', 'editPosition_other')
        })
    }
})

function editTeacher(data){
    document.getElementById('editTeacherId').value = data.id;
    document.getElementById('editFullName').value = data.fullName;
    populatePosition('editPosition', 'editPosition_other', data.position || '');
    document.getElementById('editEmail').value = data.email;
    document.getElementById('editContactNumber').value = data.contactNumber || '';
    document.getElementById('editBirthDate').value = data.birthDate || '';
    document.getElementById('editSex').value = data.sex || '';
    editAddressPicker.populate(data.province || '', data.cityMunicipality || '', data.barangay || '');
}

function resetTeacherPassword(id, fullName){
    document.getElementById('resetTeacherPasswordForm').reset();
    document.getElementById('resetTeacherPasswordId').value = id;
    document.getElementById('resetTeacherPasswordName').textContent = fullName;
}

// ===== Assign Subjects =====
// Each teacher's assigned subjects are independent -- two teachers
// advising the same grade level can end up with different checked
// subjects here, since this is per-teacher, not per-grade-level.

function csrfToken(){
    const meta = document.querySelector('meta[name="csrf-token"]')
    return meta ? meta.getAttribute('content') : ''
}

function escapeHtml(text){
    const div = document.createElement('div')
    div.textContent = text
    return div.innerHTML
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

function openAssignSubjects(teacherId, teacherName){
    document.getElementById('assignSubjectsTeacherId').value = teacherId
    document.getElementById('assignSubjectsTeacherName').textContent = teacherName
    const list = document.getElementById('assignSubjectsList')
    list.innerHTML = '<p class="text-muted small mb-0">Loading...</p>'

    fetch('../../../app/controllers/administrative/TeacherSubjectsController.php?teacher_id=' + encodeURIComponent(teacherId))
        .then(function(r){ return r.json() })
        .then(function(res){
            if(!res.success){
                list.innerHTML = '<p class="text-danger small mb-0">' + (res.message || 'Failed to load subjects.') + '</p>'
                return
            }
            const subjects = res.data || []
            if(subjects.length === 0){
                list.innerHTML = '<p class="text-muted small mb-0">This teacher has no assigned section yet, so there are no subjects to choose from.</p>'
                return
            }

            const byGrade = {}
            subjects.forEach(function(subject){
                if(!byGrade[subject.grade_name]) byGrade[subject.grade_name] = []
                byGrade[subject.grade_name].push(subject)
            })

            let html = ''
            Object.keys(byGrade).forEach(function(gradeName){
                html += '<p class="fw-semibold mb-1">' + escapeHtml(gradeName) + '</p>'
                byGrade[gradeName].forEach(function(subject){
                    const checkedAttr = subject.assigned ? 'checked' : ''
                    html += '<div class="form-check">' +
                        '<input class="form-check-input assign-subject-checkbox" type="checkbox" value="' + subject.id + '" id="assign_subject_' + subject.id + '" ' + checkedAttr + '>' +
                        '<label class="form-check-label" for="assign_subject_' + subject.id + '">' + escapeHtml(subject.subject_name) + '</label>' +
                        '</div>'
                })
            })
            list.innerHTML = html
        })
        .catch(function(){
            list.innerHTML = '<p class="text-danger small mb-0">Something went wrong loading subjects.</p>'
        })
}

function saveAssignSubjects(){
    const teacherId = document.getElementById('assignSubjectsTeacherId').value
    const subjectIds = Array.from(document.querySelectorAll('.assign-subject-checkbox:checked')).map(function(cb){ return cb.value })
    const btn = document.getElementById('assignSubjectsSaveBtn')
    btn.disabled = true

    fetch('../../../app/controllers/administrative/TeacherSubjectsController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ teacher_id: teacherId, subject_ids: subjectIds, csrf_token: csrfToken() })
    })
        .then(function(r){ return r.json() })
        .then(function(res){
            btn.disabled = false
            if(res.success){
                bootstrap.Modal.getOrCreateInstance(document.getElementById('assignSubjectsModal')).hide()
                notifySuccess(res.message || 'Subject assignments updated.')
            }else{
                notifyError(res.message || 'Failed to save subject assignments.')
            }
        })
        .catch(function(err){
            btn.disabled = false
            notifyError(err.message)
        })
}
