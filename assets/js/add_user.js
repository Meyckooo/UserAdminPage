function generateRandomPostCode() {
    const randomCode = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    document.getElementById('post_code').value = randomCode;
}

document.getElementById('addUserForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    const postCode = document.getElementById('post_code').value;

    // 1. Check kon duplicate ba ang Post Code
    fetch('check_postcode.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_code=' + encodeURIComponent(postCode)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'exists') {
            // Kon Duplicate
            Swal.fire({
                title: 'Duplicate Post Code!',
                text: 'The Post Code (' + postCode + ') is already assigned to another user. Generating a new code...',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                generateRandomPostCode();
            });
        } else {
            // 2. Kon Dili Duplicate -> Isubmit ang Form Data via AJAX sa action.php
            const formData = new FormData(form);
            formData.append('add', '1');
            formData.append('is_ajax', '1');

            fetch('action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(resData => {
                if (resData.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'User account has been successfully created.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'user_account.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: resData.message || 'Something went wrong while saving the user.',
                        icon: 'error'
                    });
                }
            })
            .catch(err => {
                console.error('Submit Error:', err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while saving the user.',
                    icon: 'error'
                });
            });
        }
    })
    .catch(error => {
        console.error('Check Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Could not verify the post code. Please try again.',
            icon: 'error'
        });
    });
});