/**
 * Loaded on every teacher page (dashboard included). The dashboard itself
 * is fully server-rendered, so there's nothing dashboard-specific here —
 * this only wires up section → grade level sync for the "Add Student" and
 * "Import from Excel" modals (resources/views/teacher/students.php,
 * multiple-advisory-sections case), since that markup relies on this
 * script being present site-wide.
 */
function wireSectionToGradeLevel(sectionSelectId, gradeLevelInputId){
    const sectionSelect = document.getElementById(sectionSelectId);
    const gradeLevelInput = document.getElementById(gradeLevelInputId);
    if (sectionSelect && gradeLevelInput) {
        sectionSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            gradeLevelInput.value = selectedOption ? (selectedOption.getAttribute('data-grade-level-id') || '') : '';
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    wireSectionToGradeLevel('section_id', 'grade_level_id');
    wireSectionToGradeLevel('import_section_id', 'import_grade_level_id');
});
