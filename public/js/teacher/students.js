function renderRecordsTable(tbodyId, records, columns, emptyMessage){
    const tbody = document.getElementById(tbodyId);
    tbody.innerHTML = '';

    if(!records || records.length === 0){
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = columns.length;
        cell.className = 'text-center text-muted';
        cell.textContent = emptyMessage;
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }

    records.forEach(function(record){
        const row = document.createElement('tr');
        columns.forEach(function(column){
            const cell = document.createElement('td');
            cell.textContent = record[column] ?? '';
            row.appendChild(cell);
        });
        tbody.appendChild(row);
    });
}

function viewStudent(id, lrn, full_name, section, school_year, age, gender, address){
    document.getElementById('view_student_lrn').textContent = lrn;
    document.getElementById('view_student_full_name').textContent = full_name;
    document.getElementById('view_student_section').textContent = section;
    document.getElementById('view_student_school_year').textContent = school_year;
    document.getElementById('view_student_age').textContent = age;
    document.getElementById('view_student_gender').textContent = gender;
    document.getElementById('view_student_address').textContent = address;

    renderRecordsTable(
        'view_behavior_records',
        studentBehaviorRecords[id],
        ['observation_date', 'category', 'observation', 'intervention', 'remarks'],
        'No behavior records found.'
    );

    renderRecordsTable(
        'view_developmental_records',
        studentDevelopmentalRecords[id],
        ['school_year', 'domain', 'observation', 'recommendation'],
        'No developmental records found.'
    );
}

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