/**
 * Handle student view modal functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-student-btn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const studentId = this.getAttribute('data-student-id');
            fetchStudentDetails(studentId);
        });
    });
});

/**
 * Fetch student details from API endpoint
 * @param {number} studentId - The ID of the student
 */
function fetchStudentDetails(studentId) {
    const apiUrl = '../../../app/api/shared/get-student-details.php?student_id=' + studentId;

    fetch(apiUrl)
        .then(response => {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!response.ok) {
                        throw new Error(data.message || 'Network response was not ok');
                    }
                    return data;
                } catch (e) {
                    console.error('Response text:', text);
                    throw new Error('Invalid response from server: ' + text.substring(0, 100));
                }
            });
        })
        .then(data => {
            if (data.success) {
                populateStudentModal(data.student, data.guardian, data.observations || []);
            } else {
                alert('Error: ' + (data.message || 'Unable to load student details'));
            }
        })
        .catch(error => {
            console.error('Error fetching student details:', error);
            alert('Error loading student details: ' + error.message);
        });
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

/**
 * Populate the modal with student, guardian, and recent observation information
 * @param {object} student - Student data object
 * @param {object} guardian - Guardian data object or null
 * @param {array} observations - Recent behavior/developmental observations
 */
function populateStudentModal(student, guardian, observations) {
    document.getElementById('studentFullName').textContent = student.full_name || '-';
    document.getElementById('studentLRN').textContent = student.lrn || '-';
    document.getElementById('studentGender').textContent = student.gender || '-';
    document.getElementById('studentBirthDate').textContent = student.birth_date || '-';
    document.getElementById('studentAge').textContent = student.age || '-';
    document.getElementById('studentContactNumber').textContent = student.contact_number || '-';
    document.getElementById('studentEmail').textContent = student.email || '-';
    document.getElementById('studentAddress').textContent = student.address || '-';
    document.getElementById('studentPlaceOfBirth').textContent = student.place_of_birth || '-';
    document.getElementById('studentNationality').textContent = student.nationality || '-';
    document.getElementById('studentReligion').textContent = student.religion || '-';

    const guardianInfoDiv = document.getElementById('guardianInfo');
    if (!guardian) {
        guardianInfoDiv.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="bx bx-info-circle fs-5"></i>
                <span>No guardian has been linked to this student yet.</span>
            </div>`;
    } else {
        const fullName = [guardian.first_name, guardian.middle_name, guardian.last_name].filter(Boolean).join(' ');
        guardianInfoDiv.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name:</label>
                        <p>${escapeHtml(fullName) || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Relationship:</label>
                        <p>${escapeHtml(guardian.relationship) || '-'}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contact Number:</label>
                        <p>${escapeHtml(guardian.contact_number) || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Occupation:</label>
                        <p>${escapeHtml(guardian.occupation) || '-'}</p>
                    </div>
                </div>
            </div>`;
    }

    const observationsDiv = document.getElementById('observationsInfo');
    if (!observationsDiv) {
        return;
    }

    if (!observations || observations.length === 0) {
        observationsDiv.innerHTML = `<p class="text-muted mb-0">No behavior or developmental observations recorded yet.</p>`;
        return;
    }

    let html = '<ul class="list-unstyled mb-0">';
    observations.forEach(obs => {
        const badgeClass = obs.category === 'Developmental' ? 'bg-label-primary' : 'bg-label-warning';
        html += `
            <li class="mb-3 pb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge ${badgeClass}">${escapeHtml(obs.category)}</span>
                    <small class="text-muted">${escapeHtml(obs.date)}</small>
                </div>
                <p class="mb-0">${escapeHtml(obs.observation)}</p>
            </li>`;
    });
    html += '</ul>';
    observationsDiv.innerHTML = html;
}
