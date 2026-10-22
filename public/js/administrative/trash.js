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

function notifySuccess(message){
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true
    })
}

function notifyError(message){
    Swal.fire({ icon: 'error', title: 'Error', text: message || 'Something went wrong.', confirmButtonColor: 'var(--primary)' })
}

const TRASH_ENDPOINT = '../../../app/controllers/administrative/TrashController.php'

document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('trash_search')
    if(searchInput){
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
    }

    const selectAll = document.getElementById('selectAllCheckbox')
    const rowCheckboxes = function(){ return Array.from(document.querySelectorAll('.trash-row-checkbox')) }

    function updateBulkButtons(){
        const anyChecked = rowCheckboxes().some(function(cb){ return cb.checked })
        document.getElementById('bulkRestoreBtn').disabled = !anyChecked
        document.getElementById('bulkDeleteBtn').disabled = !anyChecked
    }

    if(selectAll){
        selectAll.addEventListener('change', function(){
            rowCheckboxes().forEach(function(cb){ cb.checked = selectAll.checked })
            updateBulkButtons()
        })
    }
    rowCheckboxes().forEach(function(cb){
        cb.addEventListener('change', updateBulkButtons)
    })

    function selectedIds(){
        return rowCheckboxes().filter(function(cb){ return cb.checked }).map(function(cb){ return parseInt(cb.value, 10) })
    }

    function postAction(action, extra){
        return fetch(TRASH_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(Object.assign({ action: action, csrf_token: csrfToken() }, extra || {}))
        }).then(function(r){ return r.json() })
    }

    document.getElementById('bulkRestoreBtn').addEventListener('click', function(){
        const ids = selectedIds()
        if(ids.length === 0) return
        Swal.fire({
            icon: 'question',
            title: 'Restore ' + ids.length + ' student(s)?',
            showCancelButton: true,
            confirmButtonText: 'Restore',
            confirmButtonColor: 'var(--primary)'
        }).then(function(result){
            if(!result.isConfirmed) return
            postAction('bulk_restore', { ids: ids }).then(function(res){
                if(res.failed && res.failed.length){
                    notifyError(res.restored + ' restored, ' + res.failed.length + ' failed (conflicting LRN).')
                }else{
                    notifySuccess(res.restored + ' student(s) restored.')
                }
                window.location.reload()
            }).catch(function(err){ notifyError(err.message) })
        })
    })

    document.getElementById('bulkDeleteBtn').addEventListener('click', function(){
        const ids = selectedIds()
        if(ids.length === 0) return
        Swal.fire({
            icon: 'warning',
            title: 'Permanently delete ' + ids.length + ' student(s)?',
            text: 'This cannot be undone. All related records and photos will be removed.',
            showCancelButton: true,
            confirmButtonText: 'Delete Permanently',
            confirmButtonColor: 'var(--danger-strong)',
            cancelButtonColor: 'var(--primary)'
        }).then(function(result){
            if(!result.isConfirmed) return
            postAction('bulk_permanent_delete', { ids: ids }).then(function(res){
                notifySuccess((res.deleted || 0) + ' student(s) permanently deleted.')
                window.location.reload()
            }).catch(function(err){ notifyError(err.message) })
        })
    })

    document.getElementById('emptyTrashBtn').addEventListener('click', function(){
        Swal.fire({
            icon: 'warning',
            title: 'Empty Trash for ALL teachers?',
            text: 'Every trashed student school-wide will be permanently deleted, along with their records and photos. This cannot be undone.',
            input: 'text',
            inputPlaceholder: 'Type DELETE to confirm',
            showCancelButton: true,
            confirmButtonText: 'Empty Trash',
            confirmButtonColor: 'var(--danger-strong)',
            cancelButtonColor: 'var(--primary)',
            preConfirm: function(value){
                if(value !== 'DELETE'){
                    Swal.showValidationMessage('Type DELETE (all caps) to confirm.')
                    return false
                }
                return true
            }
        }).then(function(result){
            if(!result.isConfirmed) return
            postAction('empty_trash').then(function(res){
                notifySuccess('Trash emptied (' + (res.deleted || 0) + ' student(s) permanently deleted).')
                window.location.reload()
            }).catch(function(err){ notifyError(err.message) })
        })
    })
})

function restoreStudent(id, name){
    Swal.fire({
        icon: 'question',
        title: 'Restore this student?',
        text: name + ' will be restored and will appear in their teacher\'s Manage Students again.',
        showCancelButton: true,
        confirmButtonText: 'Restore',
        confirmButtonColor: 'var(--primary)'
    }).then(function(result){
        if(!result.isConfirmed) return
        fetch(TRASH_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'restore', id: id, csrf_token: csrfToken() })
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Restored.')
                    window.location.reload()
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })
}

function permanentDeleteStudent(id, name, lrn){
    Swal.fire({
        icon: 'warning',
        title: 'Permanently delete ' + name + '?',
        html: 'This <strong>cannot be undone</strong>. All related academic, health, reading level, and parent/guardian records, plus their photo, will be permanently removed.<br><br>Type the student\'s LRN (<strong>' + lrn + '</strong>) to confirm.',
        input: 'text',
        inputPlaceholder: 'Enter LRN to confirm',
        showCancelButton: true,
        confirmButtonText: 'Delete Permanently',
        confirmButtonColor: 'var(--danger-strong)',
        cancelButtonColor: 'var(--primary)',
        preConfirm: function(value){
            if(value !== lrn){
                Swal.showValidationMessage('LRN does not match.')
                return false
            }
            return true
        }
    }).then(function(result){
        if(!result.isConfirmed) return
        fetch(TRASH_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'permanent_delete', id: id, csrf_token: csrfToken() })
        })
            .then(function(r){ return r.json() })
            .then(function(res){
                if(res.success){
                    notifySuccess(res.message || 'Deleted.')
                    window.location.reload()
                }else{
                    notifyError(res.message)
                }
            })
            .catch(function(err){ notifyError(err.message) })
    })
}
