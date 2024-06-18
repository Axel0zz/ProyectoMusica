document.getElementById('registerForm').addEventListener('submit', function (e) {
    let password = document.getElementById('Con').value;
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres.');
    }
});
