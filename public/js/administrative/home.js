/**
 * Administrative Assistant Dashboard — Student Profile Viewer
 */
document.addEventListener('DOMContentLoaded', function () {

    const searchInput    = document.getElementById('studentSearchInput');
    const searchResults  = document.getElementById('searchResults');
    const searchStatus   = document.getElementById('searchStatus');
    const clearBtn       = document.getElementById('clearSearch');
    const profileSection = document.getElementById('profileSection');
    const emptyState     = document.getElementById('emptyProfileState');

    let debounceTimer = null;

    // ── Live search (debounced 300ms) ──
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const q = this.value.trim();

            if (q.length < 2) {
                hideSearchResults();
                return;
            }

            debounceTimer = setTimeout(function () {
                performSearch(q);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideSearchResults();
            }
        });
    }

    // ── Clear search button ──
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            hideSearchResults();
            profileSection.style.display  = 'none';
            emptyState.style.display      = 'block';
        });
    }

    // ── Fetch search results ──
    function performSearch(q) {
        showStatus('Searching...');

        fetch('../../../app/api/administrative/search-students.php?q=' + encodeURIComponent(q))
            .then(function (res) {
                return res.text().then(function (text) {
                    try { return JSON.parse(text); }
                    catch (e) { throw new Error('Invalid server response.'); }
                });
            })
            .then(function (data) {
                hideStatus();
                if (!data.success) { throw new Error(data.message || 'Search failed.'); }
                renderSearchResults(data.students);
            })
            .catch(function (err) {
                hideStatus();
                showStatus('Error: ' + err.message);
            });
    }

    // ── Render search dropdown ──
    function renderSearchResults(students) {
        if (!students || students.length === 0) {
            searchResults.innerHTML = '<div class="search-item text-muted small">No students found.</div>';
            searchResults.style.display = 'block';
            return;
        }

        let html = '';
        students.forEach(function (s) {
            const statusBadge = s.enrollment_status === 'Enrolled'
                ? '<span class="badge bg-label-success ms-1 small">' + escHtml(s.enrollment_status) + '</span>'
                : '<span class="badge bg-label-secondary ms-1 small">' + escHtml(s.enrollment_status) + '</span>';

            const gradeSection = s.section
                ? escHtml(s.grade_level) + ' — ' + escHtml(s.section)
                : escHtml(s.grade_level);

            html += '<div class="search-item" data-student-id="' + escHtml(s.id) + '">'
                + '<div class="fw-semibold">' + escHtml(s.full_name) + statusBadge + '</div>'
                + '<small class="text-muted">LRN: ' + escHtml(s.lrn) + ' &nbsp;|&nbsp; ' + gradeSection + '</small>'
                + '</div>';
        });

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';

        // Attach click handlers
        searchResults.querySelectorAll('.search-item[data-student-id]').forEach(function (item) {
            item.addEventListener('click', function () {
                const studentId = this.getAttribute('data-student-id');
                loadStudentProfile(studentId);
                hideSearchResults();
            });
        });
    }

    // ── Load full student profile ──
    function loadStudentProfile(studentId) {
        profileSection.style.display = 'none';
        emptyState.style.display     = 'none';
        showStatus('Loading profile...');

        fetch('../../../app/api/administrative/get-student-profile.php?student_id=' + encodeURIComponent(studentId))
            .then(function (res) {
                return res.text().then(function (text) {
                    try { return JSON.parse(text); }
                    catch (e) { throw new Error('Invalid server response.'); }
                });
            })
            .then(function (data) {
                hideStatus();
                if (!data.success) { throw new Error(data.message || 'Failed to load profile.'); }
                renderProfile(data.student, data.guardian, data.observations);
                profileSection.style.display = 'block';
            })
            .catch(function (err) {
                hideStatus();
                emptyState.style.display = 'block';
                Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonColor: '#696cff' });
            });
    }

    // ── Populate profile UI ──
    function renderProfile(student, guardian, observations) {

        // Header
        const initials = ((student.first_name || '').charAt(0) + (student.last_name || '').charAt(0)).toUpperCase() || '—';
        setText('profileInitial',  initials);
        setText('profileFullName', student.full_name || '—');
        setText('profileLRN',      'LRN: ' + (student.lrn || '—'));

        const grade = [student.grade_level, student.section].filter(Boolean).join(' — ');
        setText('profileGrade', grade || '—');

        const statusEl = document.getElementById('profileStatus');
        if (statusEl) {
            const statusMap = {
                'Enrolled':    'bg-success',
                'Inactive':    'bg-secondary',
                'Transferred': 'bg-warning',
                'Graduated':   'bg-primary',
            };
            statusEl.textContent  = student.enrollment_status || '—';
            statusEl.className    = 'badge ' + (statusMap[student.enrollment_status] || 'bg-secondary');
        }

        setText('profileTeacher', student.teacher_name || 'Not assigned');

        // Personal Info table
        setText('pGender',      student.gender      || '—');
        setText('pBirthDate',   formatDate(student.birth_date) || '—');
        setText('pAge',         student.age         ? student.age + ' yrs' : '—');
        setText('pPlaceOfBirth',student.place_of_birth || '—');
        setText('pNationality', student.nationality || '—');
        setText('pReligion',    student.religion    || '—');
        setText('pAddress',     student.address     || '—');
        setText('pContact',     student.contact_number || '—');
        setText('pEmail',       student.email       || '—');

        // Guardian
        const guardianDiv = document.getElementById('guardianInfo');
        if (!guardian) {
            guardianDiv.innerHTML = '<div class="alert alert-warning d-flex align-items-center gap-2 mb-0">'
                + '<i class="bx bx-info-circle fs-5"></i>'
                + '<span>No guardian linked to this student.</span></div>';
        } else {
            guardianDiv.innerHTML =
                '<table class="table table-sm table-borderless mb-0">'
                + '<tbody>'
                + '<tr><th class="text-muted" style="width:45%">Full Name</th><td>' + escHtml(guardian.full_name || '—') + '</td></tr>'
                + '<tr><th class="text-muted">Relationship</th><td>' + escHtml(guardian.relationship || '—') + '</td></tr>'
                + '<tr><th class="text-muted">Contact No.</th><td>' + escHtml(guardian.contact_number || '—') + '</td></tr>'
                + '<tr><th class="text-muted">Occupation</th><td>' + escHtml(guardian.occupation || '—') + '</td></tr>'
                + '</tbody></table>';
        }

        // Observations timeline
        const timelineDiv = document.getElementById('observationsTimeline');
        const obsCountEl  = document.getElementById('obsCount');

        if (obsCountEl) obsCountEl.textContent = (observations ? observations.length : 0) + ' record(s)';

        if (!observations || observations.length === 0) {
            timelineDiv.innerHTML = '<p class="text-muted small mb-0">No observations have been recorded for this student yet.</p>';
            return;
        }

        let html = '';
        observations.forEach(function (obs) {
            const isBehavioral = obs.category === 'Behavioral';
            const badgeClass   = isBehavioral ? 'bg-label-warning' : 'bg-label-primary';
            const tlClass      = isBehavioral ? 'behavioral' : 'developmental';
            const syLabel      = obs.school_year ? '(S.Y. ' + escHtml(obs.school_year) + ')' : '';

            html += '<div class="timeline-item ' + tlClass + ' mb-4">'
                + '<div class="d-flex justify-content-between align-items-start mb-1">'
                +   '<span class="badge ' + badgeClass + '">' + escHtml(obs.category) + '</span>'
                +   '<small class="text-muted">' + escHtml(formatDate(obs.date)) + ' ' + escHtml(syLabel) + '</small>'
                + '</div>'
                + '<p class="mb-1">' + escHtml(obs.observation) + '</p>'
                + '<small class="text-muted">Recorded by: <em>' + escHtml(obs.recorded_by_name || 'Unknown') + '</em>'
                +   ' &middot; ' + escHtml(formatDateTime(obs.created_at)) + '</small>'
                + '</div>';
        });

        timelineDiv.innerHTML = html;
    }

    // ── Helpers ──

    function hideSearchResults() {
        if (searchResults) searchResults.style.display = 'none';
    }

    function showStatus(msg) {
        if (searchStatus) {
            searchStatus.textContent   = msg;
            searchStatus.style.display = 'block';
        }
    }

    function hideStatus() {
        if (searchStatus) searchStatus.style.display = 'none';
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function escHtml(value) {
        const div = document.createElement('div');
        div.textContent = value != null ? String(value) : '';
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr + 'T00:00:00');
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return '';
        const d = new Date(dateTimeStr);
        if (isNaN(d)) return dateTimeStr;
        return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }
});
