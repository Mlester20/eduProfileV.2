function editUser(id, fullName, email, role){
    document.getElementById('editUserId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
}

function resetPassword(id, fullName){
    document.getElementById('resetPasswordUserId').value = id;
    document.getElementById('resetPasswordUserName').textContent = fullName;
    document.getElementById('resetPasswordForm').reset();
    document.getElementById('resetPasswordUserId').value = id;
}