document.addEventListener('DOMContentLoaded', function(){

    // ───────────────────────── Tab 1: Attendance History filters ─────────────────────────
    const studentFilter = document.getElementById('historyStudentFilter')
    const sessionFilter = document.getElementById('historySessionFilter')
    const statusFilter = document.getElementById('historyStatusFilter')
    const dateFilter = document.getElementById('historyDateFilter')
    const clearBtn = document.getElementById('historyFilterClear')
    const historyBody = document.getElementById('historyTableBody')
    const historyPagination = document.getElementById('historyPagination')
    const historyPaginationInfo = document.getElementById('historyPaginationInfo')

    const HISTORY_PAGE_SIZE = 10
    let historyCurrentPage = 1

    function historyRowMatchesFilters(row){
        const studentValue = studentFilter ? studentFilter.value : ''
        const sessionValue = sessionFilter ? sessionFilter.value : ''
        const statusValue = statusFilter ? statusFilter.value : ''
        const dateValue = dateFilter ? dateFilter.value : ''
        const matchesStudent = !studentValue || row.getAttribute('data-student') === studentValue
        const matchesSession = !sessionValue || row.getAttribute('data-session') === sessionValue
        const matchesStatus = !statusValue || row.getAttribute('data-status') === statusValue
        const matchesDate = !dateValue || row.getAttribute('data-date') === dateValue
        return matchesStudent && matchesSession && matchesStatus && matchesDate
    }

    function renderHistoryPagination(totalItems, totalPages){
        if(!historyPagination) return
        historyPagination.innerHTML = ''

        if(historyPaginationInfo){
            if(totalItems === 0){
                historyPaginationInfo.textContent = ''
            }else{
                const start = (historyCurrentPage - 1) * HISTORY_PAGE_SIZE + 1
                const end = Math.min(historyCurrentPage * HISTORY_PAGE_SIZE, totalItems)
                historyPaginationInfo.textContent = 'Showing ' + start + ' to ' + end + ' of ' + totalItems + ' entries'
            }
        }

        if(totalPages <= 1) return

        function addPageItem(label, page, options){
            options = options || {}
            const li = document.createElement('li')
            li.className = 'page-item' + (options.disabled ? ' disabled' : '') + (options.active ? ' active' : '')
            const link = document.createElement('a')
            link.className = 'page-link'
            link.href = '#'
            link.textContent = label
            if(!options.disabled && !options.active){
                link.addEventListener('click', function(e){
                    e.preventDefault()
                    historyCurrentPage = page
                    renderHistoryPage()
                })
            }else{
                link.addEventListener('click', function(e){ e.preventDefault() })
            }
            li.appendChild(link)
            historyPagination.appendChild(li)
        }

        addPageItem('«', historyCurrentPage - 1, { disabled: historyCurrentPage <= 1 })
        for(let p = 1; p <= totalPages; p++){
            addPageItem(String(p), p, { active: p === historyCurrentPage })
        }
        addPageItem('»', historyCurrentPage + 1, { disabled: historyCurrentPage >= totalPages })
    }

    function renderHistoryPage(){
        const rows = historyBody ? Array.prototype.slice.call(historyBody.querySelectorAll('.history-row')) : []
        const matchingRows = rows.filter(historyRowMatchesFilters)
        const totalItems = matchingRows.length
        const totalPages = Math.max(1, Math.ceil(totalItems / HISTORY_PAGE_SIZE))

        if(historyCurrentPage > totalPages) historyCurrentPage = totalPages
        if(historyCurrentPage < 1) historyCurrentPage = 1

        const start = (historyCurrentPage - 1) * HISTORY_PAGE_SIZE
        const pageRows = matchingRows.slice(start, start + HISTORY_PAGE_SIZE)

        rows.forEach(function(row){ row.style.display = 'none' })
        pageRows.forEach(function(row){ row.style.display = '' })

        let emptyRow = document.getElementById('historyFilterEmptyRow')
        if(rows.length > 0 && totalItems === 0){
            if(!emptyRow){
                emptyRow = document.createElement('tr')
                emptyRow.id = 'historyFilterEmptyRow'
                emptyRow.innerHTML = '<td colspan="7" class="text-center text-muted">No records match your filters.</td>'
                historyBody.appendChild(emptyRow)
            }
            emptyRow.style.display = ''
        }else if(emptyRow){
            emptyRow.style.display = 'none'
        }

        renderHistoryPagination(totalItems, totalPages)
    }

    function applyHistoryFilters(){
        historyCurrentPage = 1
        renderHistoryPage()
    }

    if(studentFilter) studentFilter.addEventListener('change', applyHistoryFilters)
    if(sessionFilter) sessionFilter.addEventListener('change', applyHistoryFilters)
    if(statusFilter) statusFilter.addEventListener('change', applyHistoryFilters)
    if(dateFilter) dateFilter.addEventListener('change', applyHistoryFilters)
    if(clearBtn){
        clearBtn.addEventListener('click', function(){
            if(studentFilter) studentFilter.value = ''
            if(sessionFilter) sessionFilter.value = ''
            if(statusFilter) statusFilter.value = ''
            if(dateFilter) dateFilter.value = ''
            applyHistoryFilters()
        })
    }

    renderHistoryPage()

    // ───────────────────────── Tab 2: Take Attendance grid ─────────────────────────
    const gridDateInput = document.getElementById('attendanceGridDate')
    const gridBody = document.getElementById('attendanceGridBody')
    const gridAlert = document.getElementById('attendanceGridAlert')
    const saveBtn = document.getElementById('saveAttendanceBtn')
    const takeTabBtn = document.getElementById('take-tab-btn')

    const STATUSES = ['Present', 'Absent', 'Late', 'Excused']
    const SESSIONS = [
        { key: 'morning', label: 'Morning', field: 'morning_status' },
        { key: 'afternoon', label: 'Afternoon', field: 'afternoon_status' }
    ]
    // attendanceState = { [student_id]: { morning: 'Present'|null, afternoon: 'Absent'|null } }
    const attendanceState = {}
    let gridLoadedForDate = null

    function todayIso(){
        return new Date().toISOString().split('T')[0]
    }

    if(gridDateInput) gridDateInput.value = todayIso()

    function showGridAlert(message){
        if(!gridAlert) return
        gridAlert.textContent = message
        gridAlert.classList.remove('d-none')
    }

    function hideGridAlert(){
        if(!gridAlert) return
        gridAlert.classList.add('d-none')
        gridAlert.textContent = ''
    }

    function statusClass(status){
        return 'status-' + status.toLowerCase()
    }

    function renderGrid(students){
        gridBody.innerHTML = ''

        if(!students || students.length === 0){
            gridBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No students found under your advisory.</td></tr>'
            return
        }

        students.forEach(function(student){
            const studentId = student.student_id
            const fullName = [student.student_first_name, student.student_middle_name, student.student_last_name, student.student_suffix]
                .filter(Boolean)
                .join(' ')

            // Locked sessions (already saved in the database) are left out of
            // attendanceState entirely so they never end up in the save payload.
            attendanceState[studentId] = {
                morning: null,
                afternoon: null
            }

            const row = document.createElement('tr')

            const nameCell = document.createElement('td')
            nameCell.textContent = fullName
            row.appendChild(nameCell)

            SESSIONS.forEach(function(session, sessionIndex){
                const isLocked = !!student[session.field]

                STATUSES.forEach(function(status, statusIndex){
                    const cell = document.createElement('td')
                    cell.className = 'text-center'
                    if(sessionIndex > 0 && statusIndex === 0){
                        cell.classList.add('session-divider')
                    }
                    if(isLocked){
                        cell.classList.add('attendance-locked-cell')
                    }

                    const btn = document.createElement('button')
                    btn.type = 'button'
                    btn.className = 'btn btn-outline-secondary btn-sm attendance-status-btn ' + statusClass(status)
                    btn.textContent = status
                    btn.dataset.studentId = studentId
                    btn.dataset.session = session.key
                    btn.dataset.status = status

                    const isSavedStatus = student[session.field] === status
                    if(isSavedStatus){
                        btn.classList.add('active')
                    }

                    if(isLocked){
                        btn.disabled = true
                        btn.classList.add('locked')
                        btn.title = 'Already recorded for this session — cannot be changed.'
                        if(isSavedStatus){
                            btn.innerHTML = '<i class="bx bx-lock-alt me-1"></i>' + status
                        }
                    }else{
                        btn.addEventListener('click', function(){
                            attendanceState[studentId][session.key] = status
                            row.querySelectorAll('.attendance-status-btn[data-session="' + session.key + '"]').forEach(function(b){
                                b.classList.remove('active')
                            })
                            btn.classList.add('active')
                        })
                    }

                    cell.appendChild(btn)
                    row.appendChild(cell)
                })
            })

            gridBody.appendChild(row)
        })
    }

    function loadGrid(date){
        hideGridAlert()
        gridBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading students...</td></tr>'

        fetch('../../../app/api/teacher/attendance-grid.php?date=' + encodeURIComponent(date))
            .then(function(response){ return response.json() })
            .then(function(data){
                if(!data.success){
                    gridBody.innerHTML = ''
                    showGridAlert(data.message || 'Unable to load attendance grid.')
                    return
                }
                gridLoadedForDate = data.date
                renderGrid(data.students)
            })
            .catch(function(error){
                gridBody.innerHTML = ''
                showGridAlert('Failed to load attendance grid: ' + error.message)
            })
    }

    if(gridDateInput){
        gridDateInput.addEventListener('change', function(){
            loadGrid(gridDateInput.value || todayIso())
        })
    }

    if(takeTabBtn){
        takeTabBtn.addEventListener('shown.bs.tab', function(){
            const date = gridDateInput ? (gridDateInput.value || todayIso()) : todayIso()
            if(gridLoadedForDate !== date){
                loadGrid(date)
            }
        })
    }

    if(saveBtn){
        saveBtn.addEventListener('click', function(){
            const date = gridDateInput ? (gridDateInput.value || todayIso()) : todayIso()
            const records = []

            Object.keys(attendanceState).forEach(function(studentId){
                const sessions = attendanceState[studentId]
                SESSIONS.forEach(function(session){
                    const status = sessions[session.key]
                    if(status){
                        records.push({
                            student_id: parseInt(studentId, 10),
                            session: session.label,
                            status: status
                        })
                    }
                })
            })

            if(records.length === 0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Nothing to save',
                    text: 'Mark at least one student\'s status before saving.',
                    confirmButtonColor: '#696cff'
                })
                return
            }

            saveBtn.disabled = true
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'

            fetch('../../../app/api/teacher/attendance-save-bulk.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ attendance_date: date, records: records })
            })
                .then(function(response){ return response.json() })
                .then(function(data){
                    if(data.success){
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: data.message,
                            confirmButtonColor: '#696cff'
                        }).then(function(){
                            window.location.reload()
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to save attendance.',
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
                .finally(function(){
                    saveBtn.disabled = false
                    saveBtn.innerHTML = 'Save Attendance'
                })
        })
    }
})
