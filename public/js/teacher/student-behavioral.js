function editStudentBehavioral(id, student_id, school_year_id, observation_date, category, observation, intervention, remarks, recorded_by){
    document.getElementById('edit_student_behavioral_id').value = id
    document.getElementById('edit_student_id').value = student_id
    document.getElementById('edit_school_year_id').value = school_year_id
    document.getElementById('edit_observation_date').value = observation_date
    document.getElementById('edit_category').value = category
    document.getElementById('edit_observation').value = observation
    document.getElementById('edit_intervention').value = intervention
    document.getElementById('edit_remarks').value = remarks
    document.getElementById('edit_recorded_by').value = recorded_by
}