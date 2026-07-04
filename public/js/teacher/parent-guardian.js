function editParentGuardian(button) {
    document.getElementById('edit_parent_id').value = button.dataset.id;
    document.getElementById('edit_student_id').value = button.dataset.studentId;
    document.getElementById('edit_father_name').value = button.dataset.fatherName;
    document.getElementById('edit_father_occupation').value = button.dataset.fatherOccupation;
    document.getElementById('edit_father_contact').value = button.dataset.fatherContact;
    document.getElementById('edit_mother_name').value = button.dataset.motherName;
    document.getElementById('edit_mother_occupation').value = button.dataset.motherOccupation;
    document.getElementById('edit_mother_contact').value = button.dataset.motherContact;
    document.getElementById('edit_guardian_name').value = button.dataset.guardianName;
    document.getElementById('edit_guardian_relationship').value = button.dataset.guardianRelationship;
    document.getElementById('edit_guardian_contact').value = button.dataset.guardianContact;
}
