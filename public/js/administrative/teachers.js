function editTeacher(data){
    document.getElementById('editTeacherId').value = data.id;
    document.getElementById('editFullName').value = data.fullName;
    document.getElementById('editEmail').value = data.email;
    document.getElementById('editContactNumber').value = data.contactNumber || '';
    document.getElementById('editAddress').value = data.address || '';
    document.getElementById('editBirthDate').value = data.birthDate || '';
    document.getElementById('editSex').value = data.sex || '';
}

function resetTeacherPassword(id, fullName){
    document.getElementById('resetTeacherPasswordForm').reset();
    document.getElementById('resetTeacherPasswordId').value = id;
    document.getElementById('resetTeacherPasswordName').textContent = fullName;
}
