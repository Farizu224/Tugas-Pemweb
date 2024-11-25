function validateLogin() {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let isValid = true;

    // Reset error messages
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';

    // Validasi format email
    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        document.getElementById('emailError').textContent = 'Email tidak valid';
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    } else {
        // Cek apakah email terdaftar di database melalui AJAX
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "cekEmail.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status == 200) {
                if (xhr.responseText == 'not_found') {
                    document.getElementById('emailError').textContent = 'Email tidak ditemukan';
                    document.getElementById('emailError').style.display = 'block';
                    isValid = false;
                }
                // Jika email ditemukan, tidak perlu melakukan apa-apa (valid)
            }
        };
        xhr.send("email=" + email);
    }

    // Validasi password
    if (password.trim() === '') {
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
    }

    return isValid;
}
