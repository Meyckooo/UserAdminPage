function generateRandomPostCode() {
        const randomCode = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        const postInput = document.getElementById('post_code');
        postInput.value = randomCode;
        
        // Trigger event para ma-detect sa script nga naay pagbag-o ug ma-enable ang Update Button
        postInput.dispatchEvent(new Event('input'));
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('editUserForm');
        const btnUpdate = document.getElementById('btnUpdate');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');

        const initialValues = {};
        inputs.forEach((input) => {
            initialValues[input.name] = input.value;
        });

        function checkChanges() {
            let hasChanged = false;

            inputs.forEach((input) => {
                if (input.value !== initialValues[input.name]) {
                    hasChanged = true;
                }
            });

            if (hasChanged) {
                btnUpdate.disabled = false;
                btnUpdate.style.opacity = '1';
                btnUpdate.style.cursor = 'pointer';
            } else {
                btnUpdate.disabled = true;
                btnUpdate.style.opacity = '0.5';
                btnUpdate.style.cursor = 'not-allowed';
            }
        }

        inputs.forEach((input) => {
            input.addEventListener('input', checkChanges);
            input.addEventListener('change', checkChanges);
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (btnUpdate.disabled) return;

            Swal.fire({
                title: 'Update User Details?',
                text: "Are you sure you want to save these changes?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });