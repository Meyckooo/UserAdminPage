function showForm(formId) {
    const targetForm = document.getElementById(formId);
    if (targetForm) {
        document.querySelectorAll(".form_box").forEach(form => form.classList.remove("active"));
        targetForm.classList.add("active");
    }
}

document.addEventListener('DOMContentLoaded', function() {

    // 1. Show / Hide Password Feature
    const eyeicon = document.getElementById("eyeicon");
    const password = document.getElementById("password");

    if (eyeicon && password) {
        eyeicon.onclick = function() {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.src = "assets/images/eyeopen.png";
            } else {
                password.type = "password";
                eyeicon.src = "assets/images/eyeclose.png";
            }
        };
    }

    // 2. Disable Submit Button hangtod nga naay bag-ong pagbag-o (Form Change Detection)
    const editForm = document.getElementById('editUserForm');

    if (editForm) {
        const submitBtn = editForm.querySelector('button[type="submit"]');
        const inputs = editForm.querySelectorAll('input:not([type="hidden"])');
        
        // I-save ang orihinal nga mga value sa mga fields
        const initialValues = {};
        inputs.forEach(input => {
            initialValues[input.name] = input.value;
        });

        // Sa sugod, i-disable ang submit button
        if (submitBtn) submitBtn.disabled = true;

        // Function para i-check kung naa ba'y giusab sa mga fields
        function checkFormChanges() {
            let hasChanged = false;

            inputs.forEach(input => {
                if (input.value !== initialValues[input.name]) {
                    hasChanged = true;
                }
            });

            // Kung naa'y nausab, i-enable ang button; kung wala, naka-disable ra
            if (submitBtn) {
                submitBtn.disabled = !hasChanged;
            }
        }

        // Maminaw sa matag type o usab sa inputs
        inputs.forEach(input => {
            input.addEventListener('input', checkFormChanges);
            input.addEventListener('change', checkFormChanges);
        });

        // 3. SweetAlert Edit User Confirmation
        editForm.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Do you want to save these changes?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    editForm.submit();
                }
            });
        });
    }

    const tableContainer = document.querySelector('.item_table_details');
    const formContainer = document.querySelector('.item_form_container');

    if (!tableContainer || !formContainer) return;

    // Observe table height dynamically
    const observer = new ResizeObserver(() => {
        const tableHeight = tableContainer.offsetHeight;

        if (tableHeight >= 500) {
            // Apply sticky position and height cap
            formContainer.style.position = 'sticky';
            formContainer.style.top = '20px';
            formContainer.style.maxHeight = 'calc(100vh - 40px)';
            formContainer.style.overflowY = 'auto';
        } else {
            // Revert back to normal
            formContainer.style.position = 'static';
            formContainer.style.maxHeight = 'none';
            formContainer.style.overflowY = 'visible';
        }
    });

    observer.observe(tableContainer);

});