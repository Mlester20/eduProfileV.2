function editStudent(student) {
    document.getElementById('editId').value = student.id;
    document.getElementById('editLrn').value = student.lrn || '';
    document.getElementById('editFirstName').value = student.first_name || '';
    document.getElementById('editMiddleName').value = student.middle_name || '';
    document.getElementById('editLastName').value = student.last_name || '';
    document.getElementById('editSuffix').value = student.suffix || '';
    document.getElementById('editGender').value = student.gender || '';
    document.getElementById('editBirthDate').value = student.birth_date || '';
    document.getElementById('editPlaceOfBirth').value = student.place_of_birth || '';
    document.getElementById('editNationality').value = student.nationality || '';
    document.getElementById('editReligion').value = student.religion || '';
    document.getElementById('editContactNumber').value = student.contact_number || '';
    document.getElementById('editEmail').value = student.email || '';
    document.getElementById('editGuardianId').value = student.guardian_id || '';
    document.getElementById('editAddress').value = student.address || '';
}
