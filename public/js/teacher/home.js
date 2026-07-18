/**
 * Loaded on every teacher page (dashboard included). The dashboard itself
 * is fully server-rendered, so there's nothing dashboard-specific here —
 * this only wires the "Add Student" modal's section → grade level sync
 * (resources/views/teacher/students.php, multiple-advisory-sections case),
 * since that markup relies on this script being present site-wide.
 */
document.addEventListener('DOMContentLoaded', function () {
    const sectionSelect = document.getElementById('section_id');
    const gradeLevelInput = document.getElementById('grade_level_id');
    if (sectionSelect && gradeLevelInput) {
        sectionSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            gradeLevelInput.value = selectedOption ? (selectedOption.getAttribute('data-grade-level-id') || '') : '';
        });
    }
});
