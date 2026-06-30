function editGuardian(guardian) {
    document.getElementById('editId').value = guardian.id;
    document.getElementById('editLastName').value = guardian.last_name || '';
    document.getElementById('editFirstName').value = guardian.first_name || '';
    document.getElementById('editMiddleName').value = guardian.middle_name || '';
    document.getElementById('editRelationship').value = guardian.relationship || '';
    document.getElementById('editContactNumber').value = guardian.contact_number || '';
    document.getElementById('editOccupation').value = guardian.occupation || '';
}
