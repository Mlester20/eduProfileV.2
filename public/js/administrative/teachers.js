function editTeacher(id, fullName, email){
    document.getElementById('editTeacherId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = email;
}

function resetTeacherPassword(id, fullName){
    document.getElementById('resetTeacherPasswordForm').reset();
    document.getElementById('resetTeacherPasswordId').value = id;
    document.getElementById('resetTeacherPasswordName').textContent = fullName;
}
