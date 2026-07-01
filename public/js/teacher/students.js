function editStudent(id, lrn, first_name, middle_name, last_name, suffix, birth_date, gender, address, school_year_id, grade_level_id, section_id, recorded_by){
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_lrn').value = lrn;
    document.getElementById('edit_first_name').value = first_name;
    document.getElementById('edit_middle_name').value = middle_name;
    document.getElementById('edit_last_name').value = last_name;
    document.getElementById('edit_suffix').value = suffix;
    document.getElementById('edit_birth_date').value = birth_date;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_school_year_id').value = school_year_id;
    document.getElementById('edit_grade_level_id').value = grade_level_id;
    document.getElementById('edit_section_id').value = section_id;
    document.getElementById('edit_recorded_by').value = recorded_by;
}