function debounce(fn, delay){
    let timer
    return function(...args){
        clearTimeout(timer)
        const context = this
        timer = setTimeout(function(){
            fn.apply(context, args)
        }, delay)
    }
}

document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('student_table_search')
    if(!searchInput) return

    searchInput.addEventListener('input', debounce(function(){
        const term = searchInput.value.trim()
        const url = new URL(window.location.href)
        if(term === ''){
            url.searchParams.delete('search')
        }else{
            url.searchParams.set('search', term)
        }
        url.searchParams.set('page', '1')
        window.location.href = url.toString()
    }, 400))
})

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

function viewStudent(id, lrn, full_name, section, school_year, age, gender, mother_tongue, ip_ethnic_group, religion){
    document.getElementById('view_student_lrn').textContent = lrn;
    document.getElementById('view_student_full_name').textContent = full_name;
    document.getElementById('view_student_section').textContent = section;
    document.getElementById('view_student_school_year').textContent = school_year;
    document.getElementById('view_student_age').textContent = age;
    document.getElementById('view_student_gender').textContent = gender;
    document.getElementById('view_student_mother_tongue').textContent = mother_tongue;
    document.getElementById('view_student_ip_ethnic_group').textContent = ip_ethnic_group;
    document.getElementById('view_student_religion').textContent = religion;

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

    const pgFields = ['father_name', 'father_occupation', 'father_contact', 'mother_name', 'mother_occupation', 'mother_contact', 'guardian_name', 'guardian_relationship', 'guardian_contact'];
    const pg = studentParentGuardian[id] || null;
    pgFields.forEach(function(field){
        document.getElementById('view_pg_' + field).textContent = pg ? (pg[field] ?? '') : '';
    });
    document.getElementById('view_pg_empty').style.display = pg ? 'none' : 'block';

    document.getElementById('view_link_attendance').href = 'attendance.php?student_id=' + id;
    document.getElementById('view_link_academic').href = 'academic.php?student_id=' + id;
    document.getElementById('view_link_achievements').href = 'achievement-profile.php?student_id=' + id;
    document.getElementById('view_link_health').href = 'student-health.php?student_id=' + id;
    document.getElementById('view_link_reading_level').href = 'reading-level.php?student_id=' + id;
    document.getElementById('view_link_parent_guardian').href = 'parent-guardian.php?student_id=' + id;
}

function editStudent(id, lrn, first_name, middle_name, last_name, suffix, birth_date, gender, age_as_of_june, mother_tongue, ip_ethnic_group, religion, house_number, street, sitio, purok, barangay, city_municipality, province, school_year_id, grade_level_id, section_id, recorded_by){
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_lrn').value = lrn;
    document.getElementById('edit_first_name').value = first_name;
    document.getElementById('edit_middle_name').value = middle_name;
    document.getElementById('edit_last_name').value = last_name;
    document.getElementById('edit_suffix').value = suffix;
    document.getElementById('edit_birth_date').value = birth_date;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_age_as_of_june').value = age_as_of_june;
    document.getElementById('edit_mother_tongue').value = mother_tongue;
    document.getElementById('edit_ip_ethnic_group').value = ip_ethnic_group;
    document.getElementById('edit_religion').value = religion;
    document.getElementById('edit_house_number').value = house_number;
    document.getElementById('edit_street').value = street;
    document.getElementById('edit_sitio').value = sitio;
    document.getElementById('edit_purok').value = purok;
    document.getElementById('edit_barangay').value = barangay;
    document.getElementById('edit_city_municipality').value = city_municipality;
    document.getElementById('edit_province').value = province;
    document.getElementById('edit_school_year_id').value = school_year_id;
    document.getElementById('edit_grade_level_id').value = grade_level_id;
    document.getElementById('edit_section_id').value = section_id;
    document.getElementById('edit_recorded_by').value = recorded_by;
}