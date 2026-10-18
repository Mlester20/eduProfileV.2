function editSection(id, grade_level_id, section_name, adviser_id) {
    document.getElementById('edit_section_id').value = id;
    document.getElementById('edit_section_grade_level_id').value = grade_level_id;
    document.getElementById('edit_section_name').value = section_name;
    document.getElementById('edit_adviser_id').value = adviser_id;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('select[data-section-id]').forEach(function (select) {
        select.setAttribute('data-previous-value', select.value);
    });
});

function assignTeacherInline(selectEl) {
    const sectionId = selectEl.getAttribute('data-section-id');
    const adviserId = selectEl.value;
    const previousValue = selectEl.getAttribute('data-previous-value') || '';

    if (adviserId === '') {
        return;
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const role = document.body.getAttribute('data-role') || 'administrative';

    const formData = new FormData();
    formData.set('action', 'quick_assign_teacher');
    formData.set('section_id', sectionId);
    formData.set('adviser_id', adviserId);
    formData.set('csrf_token', csrfToken);

    selectEl.disabled = true;

    fetch('../../../app/controllers/' + role + '/SectionsController.php', {
        method: 'POST',
        body: formData
    })
        .then(function (r) { return r.json() })
        .then(function (res) {
            selectEl.disabled = false;
            if (res.success) {
                selectEl.setAttribute('data-previous-value', adviserId);
                if (window.sectionTeacherHistory) {
                    window.sectionTeacherHistory[sectionId] = res.history || [];
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message || 'Saved.',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true
                });
            } else {
                selectEl.value = previousValue;
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Something went wrong.' });
            }
        })
        .catch(function (err) {
            selectEl.disabled = false;
            selectEl.value = previousValue;
            Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Something went wrong.' });
        });
}

function formatAssignedDate(mysqlDateTime) {
    if (!mysqlDateTime) return '—';
    const d = new Date(mysqlDateTime.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '—';
    return d.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
}

function showTeacherHistory(sectionId, sectionLabel) {
    document.getElementById('teacherHistoryLabel').textContent = 'Teacher Assignment History — ' + sectionLabel;

    const history = (window.sectionTeacherHistory && window.sectionTeacherHistory[sectionId]) || [];
    const body = document.getElementById('teacherHistoryBody');
    body.innerHTML = '';

    if (history.length === 0) {
        body.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No assignment history recorded yet.</td></tr>';
        return;
    }

    history.forEach(function (row, index) {
        const tr = document.createElement('tr');
        const syCell = document.createElement('td');
        syCell.textContent = (row.school_year || '—') + (index === 0 ? ' (current)' : '');
        const teacherCell = document.createElement('td');
        teacherCell.textContent = row.teacher_name || '—';
        const dateCell = document.createElement('td');
        dateCell.textContent = formatAssignedDate(row.assigned_at);
        tr.appendChild(syCell);
        tr.appendChild(teacherCell);
        tr.appendChild(dateCell);
        body.appendChild(tr);
    });
}
