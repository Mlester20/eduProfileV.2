/**
 * Search-as-you-type student picker.
 * Wires a text input (#studentSearchInput) + hidden id field (#studentIdInput)
 * + a results dropdown (#studentSearchResults) into a live search against
 * app/api/shared/search-students.php. Used wherever a single student needs
 * to be selected without dumping the entire (school-wide) student list into
 * a <select>.
 */
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('studentSearchInput');
    const hiddenId = document.getElementById('studentIdInput');
    const resultsBox = document.getElementById('studentSearchResults');

    if (!input || !hiddenId || !resultsBox) {
        return;
    }

    let debounceTimer = null;

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function hideResults() {
        resultsBox.innerHTML = '';
        resultsBox.classList.add('d-none');
    }

    function selectStudent(id, label) {
        hiddenId.value = id;
        input.value = label;
        hideResults();
    }

    function renderResults(students) {
        if (!students.length) {
            resultsBox.innerHTML = '<div class="list-group-item text-muted">No students found.</div>';
            resultsBox.classList.remove('d-none');
            return;
        }

        resultsBox.innerHTML = students.map(s => {
            const label = `${s.full_name} (${s.lrn || 'no LRN'})`;
            return `<button type="button" class="list-group-item list-group-item-action student-result" data-id="${s.id}" data-label="${escapeHtml(s.full_name)}">${escapeHtml(label)}</button>`;
        }).join('');
        resultsBox.classList.remove('d-none');

        resultsBox.querySelectorAll('.student-result').forEach(btn => {
            btn.addEventListener('click', function() {
                selectStudent(this.getAttribute('data-id'), this.getAttribute('data-label'));
            });
        });
    }

    input.addEventListener('input', function() {
        hiddenId.value = '';
        const keyword = this.value.trim();

        clearTimeout(debounceTimer);

        if (keyword.length < 2) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch('../../../app/api/shared/search-students.php?q=' + encodeURIComponent(keyword))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderResults(data.students);
                    } else {
                        hideResults();
                    }
                })
                .catch(() => hideResults());
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!resultsBox.contains(e.target) && e.target !== input) {
            hideResults();
        }
    });
});
