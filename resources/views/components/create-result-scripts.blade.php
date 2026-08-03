<script>
    document.addEventListener('DOMContentLoaded', function() {
        const institutionSelect = document.getElementById('institution_id');
        const classSelect = document.getElementById('class_id');
        const studentSelect = document.getElementById('student_id');
        const examinationSelect = document.getElementById('examination_id');

        // Hides every real option unless it matches the chosen parent
        // (institution for Class; class for Student/Examination), so
        // each field stays empty until its parent is picked. The
        // placeholder text explains why.
        function filterOptions(select, parentValue, datasetKey, lockedText, readyText) {
            const currentValue = select.value;
            const placeholder = select.querySelector('option[value=""]');
            if (placeholder) {
                placeholder.textContent = parentValue ? readyText : lockedText;
            }

            Array.from(select.options).forEach(function(option) {
                if (!option.value || option.value === currentValue) {
                    option.hidden = false;
                    return;
                }

                option.hidden = !parentValue || option.dataset[datasetKey] !== String(parentValue);
            });
        }

        function applyClassFilters() {
            filterOptions(studentSelect, classSelect.value, 'classId', 'Select a class first',
            'Select Student');
            filterOptions(examinationSelect, classSelect.value, 'classId', 'Select a class first',
                'Select Examination');
        }

        function applyInstitutionFilter() {
            filterOptions(classSelect, institutionSelect.value, 'institutionId', 'Select an institution first',
                'Select Class');
        }

        // Institution -> Class -> {Student, Examination}. Changing a
        // parent resets and re-filters everything below it so stale,
        // out-of-scope picks can't linger.
        institutionSelect.addEventListener('change', function() {
            classSelect.value = '';
            studentSelect.value = '';
            examinationSelect.value = '';
            applyInstitutionFilter();
            applyClassFilters();
        });

        classSelect.addEventListener('change', function() {
            studentSelect.value = '';
            examinationSelect.value = '';
            applyClassFilters();
        });

        applyInstitutionFilter();
        applyClassFilters();
    });
</script>