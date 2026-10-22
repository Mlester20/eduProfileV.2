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

/**
 * Subjects are scoped per grade level AND per teacher assignment (see
 * SubjectsLookupController) -- this refills the dropdown for whichever
 * student's modal is currently open. `keepValue` re-selects whatever was
 * already chosen (used when editing an existing academic record), adding
 * it as a fallback option if it's no longer in the current list.
 */
function loadSubjectsForGradeLevel(gradeLevelId, keepValue){
    const select = document.getElementById('ap_subject_name')
    if(!select) return
    select.innerHTML = '<option value="" selected disabled>-- Choose Subject --</option>'
    if(!gradeLevelId) return
    fetch('../../../app/controllers/SubjectsLookupController.php?grade_level_id=' + encodeURIComponent(gradeLevelId))
        .then(function(r){ return r.json() })
        .then(function(res){
            if(!res.success) return
            ;(res.data || []).forEach(function(subject){
                const option = document.createElement('option')
                option.value = subject.subject_name
                option.textContent = subject.subject_name
                select.appendChild(option)
            })
            if(keepValue){
                if(![...select.options].some(function(o){ return o.value === keepValue })){
                    const fallback = document.createElement('option')
                    fallback.value = keepValue
                    fallback.textContent = keepValue + ' (not in current subject list)'
                    select.appendChild(fallback)
                }
                select.value = keepValue
            }
        })
}

document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('student_table_search')
    if(!searchInput) return

    if(new URL(window.location.href).searchParams.has('search')){
        searchInput.focus()
        const len = searchInput.value.length
        searchInput.setSelectionRange(len, len)
    }

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
})

;(function(){
    let currentStudentId = null
    let currentGradeLevelId = null
    let currentRecordsPage = 1

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
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No academic records yet.</td></tr>'
            return
        }
        rows.forEach(function(row){
            const isPassed = row.remarks === 'Passed'
            const remarksBadge = row.remarks
                ? '<span class="badge ' + (isPassed ? 'bg-label-success' : 'bg-label-danger') + '">' + escapeHtml(row.remarks) + '</span>'
                : ''
            const tr = document.createElement('tr')
            tr.innerHTML =
                '<td>' + escapeHtml(row.subject_name) + '</td>' +
                '<td>' + escapeHtml(row.grading_period) + '</td>' +
                '<td>' + escapeHtml(row.grade) + '</td>' +
                '<td>' + remarksBadge + '</td>' +
                '<td>' + escapeHtml(row.recorded_by_name || '') + '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-sm btn-warning ap-edit-btn"><i class="bx bx-edit"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-danger ap-delete-btn"><i class="bx bx-trash"></i></button>' +
                '</td>'
            tr.querySelector('.ap-edit-btn').addEventListener('click', function(){
                editAcademicRecord(row.id, row.subject_name, row.grading_period, row.grade)
            })
            tr.querySelector('.ap-delete-btn').addEventListener('click', function(){
                deleteAcademicRecord(row.id)
            })
            tbody.appendChild(tr)
        })
    }

    // Keeps the first, last, and a small window around the current page,
    // with "..." gaps -- same compact style used elsewhere in the app
    // (Paginator::pageWindow on the PHP side) instead of printing every
    // page number.
    function pageWindow(current, total, delta){
        delta = delta || 1
        const keep = []
        for(let i = 1; i <= total; i++){
            if(i === 1 || i === total || (i >= current - delta && i <= current + delta)){
                keep.push(i)
            }
        }
        const windowed = []
        let previous = null
        keep.forEach(function(p){
            if(previous !== null && p - previous > 1) windowed.push(null)
            windowed.push(p)
            previous = p
        })
        return windowed
    }

    function renderPagination(currentPage, totalPages){
        const el = document.getElementById('academicRecordPagination')
        el.innerHTML = ''
        if(totalPages <= 1) return

        function addItem(label, page, disabled, active){
            const li = document.createElement('li')
            li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '')
            const a = document.createElement('a')
            a.className = 'page-link'
            a.href = 'javascript:void(0);'
            a.textContent = label
            if(!disabled && !active){
                a.addEventListener('click', function(){ loadAcademicRecords(page) })
            }
            li.appendChild(a)
            el.appendChild(li)
        }

        addItem('«', currentPage - 1, currentPage <= 1, false)
        pageWindow(currentPage, totalPages).forEach(function(p){
            if(p === null){
                addItem('…', null, true, false)
            }else{
                addItem(String(p), p, false, p === currentPage)
            }
        })
        addItem('»', currentPage + 1, currentPage >= totalPages, false)
    }

    function loadAcademicRecords(page){
        if(!currentStudentId) return
        currentRecordsPage = page || 1
        const period = document.getElementById('ap_period_filter').value
        fetch('../../../app/controllers/teacher/AcademicProfileController.php?student_id=' + currentStudentId + '&page=' + currentRecordsPage)
            .then(function(r){ return r.json() })
            .then(function(res){
                let rows = res.data || []
                if(period){
                    rows = rows.filter(function(row){ return row.grading_period === period })
                }
                renderAcademicRecordList(rows)
                renderPagination(res.current_page || 1, res.total_pages || 1)
            })
    }

    document.getElementById('ap_period_filter').addEventListener('change', function(){
        loadAcademicRecords(currentRecordsPage)
    })

    window.openAcademicRecordsModal = function(studentId, studentName, studentMeta, gradeLevelId){
        currentStudentId = studentId
        currentGradeLevelId = gradeLevelId
        document.getElementById('ar_student_id').value = studentId
        document.getElementById('ar_student_name').textContent = studentName
        document.getElementById('ar_student_meta').textContent = ' — ' + studentMeta
        resetAcademicRecordForm()
        document.getElementById('ap_period_filter').value = ''
        loadSubjectsForGradeLevel(gradeLevelId)
        loadAcademicRecords(1)
        bootstrap.Modal.getOrCreateInstance(document.getElementById('academicRecordsModal')).show()
    }

    function editAcademicRecord(id, subjectName, gradingPeriod, grade){
        document.getElementById('ap_edit_id').value = id
        loadSubjectsForGradeLevel(currentGradeLevelId, subjectName)
        document.getElementById('ap_grading_period').value = gradingPeriod
        document.getElementById('ap_grade').value = grade
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
                        loadAcademicRecords(currentRecordsPage)
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
            school_year_id: document.getElementById('active_school_year_id').value,
            subject_name: document.getElementById('ap_subject_name').value,
            grading_period: document.getElementById('ap_grading_period').value,
            grade: document.getElementById('ap_grade').value,
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
                    loadAcademicRecords(editId ? currentRecordsPage : 1)
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })
})()
