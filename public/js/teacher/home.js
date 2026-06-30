/**
 * Teacher Dashboard — My Students & Observations
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Set today's date as default in observation date field ──
    const obsDateInput = document.getElementById('obs_date');
    if (obsDateInput) {
        obsDateInput.value = new Date().toISOString().split('T')[0];
    }

    // ── Populate modal when "Add Observation" button is clicked ──
    document.querySelectorAll('.add-obs-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const studentId   = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');

            document.getElementById('obs_student_id').value = studentId;
            document.getElementById('modalStudentName').textContent = studentName;

            // Reset form state
            const form = document.getElementById('observationForm');
            form.classList.remove('was-validated');
            form.reset();
            obsDateInput.value = new Date().toISOString().split('T')[0];
        });
    });

    // ── Save Observation via Fetch ──
    const saveBtn = document.getElementById('saveObservationBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            const form = document.getElementById('observationForm');

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

            fetch('../../../app/api/teacher/save-observation.php', {
                method: 'POST',
                body: formData,
            })
                .then(function (response) {
                    return response.text().then(function (text) {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
                        }
                    });
                })
                .then(function (data) {
                    if (data.success) {
                        // Close modal
                        const modalEl = document.getElementById('observationModal');
                        const modal   = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: data.message || 'Observation saved successfully.',
                            confirmButtonColor: '#696cff',
                            confirmButtonText: 'OK',
                        });

                        // Reset form
                        form.reset();
                        form.classList.remove('was-validated');
                        obsDateInput.value = new Date().toISOString().split('T')[0];
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to save observation.',
                            confirmButtonColor: '#696cff',
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Observation save error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: error.message,
                        confirmButtonColor: '#696cff',
                    });
                })
                .finally(function () {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bx bx-save me-1"></i>Save Observation';
                });
        });
    }

    // ── Client-side table search ──
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#studentsTable .student-row');
            let visibleCount = 0;

            rows.forEach(function (row) {
                const name = row.getAttribute('data-name') || '';
                const lrn  = row.getAttribute('data-lrn')  || '';
                const match = !q || name.includes(q) || lrn.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            // Toggle empty state row
            let emptyRow = document.getElementById('searchEmptyRow');
            if (visibleCount === 0 && q) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.id = 'searchEmptyRow';
                    emptyRow.innerHTML = '<td colspan="6" class="text-center text-muted py-3"><i class="bx bx-search me-1"></i>No students match your search.</td>';
                    document.getElementById('studentsTableBody').appendChild(emptyRow);
                }
                emptyRow.style.display = '';
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        });
    }
});
