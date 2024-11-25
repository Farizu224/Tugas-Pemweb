
function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const passwordError = document.getElementById('passwordError');
    const signUpButton = document.getElementById('signUpButton');

    if (password !== confirmPassword) {
        passwordError.style.display = 'block';
        signUpButton.disabled = true; 
    } else {
        passwordError.style.display = 'none';
        signUpButton.disabled = false; 
    }
}
document.getElementById('confirmPassword').addEventListener('input', validatePasswords);


function validateEmail() {
    const email = document.getElementById('email').value;
    const emailError = document.getElementById('emailError');
    const signUpButton = document.getElementById('signUpButton');
    
    if (email) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cekEmail.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText;
                if (response === "Email sudah terdaftar") {
                    emailError.style.display = 'block'; 
                    signUpButton.disabled = true;
                } else {
                    emailError.style.display = 'none';
                    signUpButton.disabled = false;
                }
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    } else {
        emailError.style.display = 'none';
    }

}
document.getElementById('email').addEventListener('input', validateEmail);

