// Adds a click-to-toggle eye icon to every .password-wrap input
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.password-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            if (!input) return;
            var hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            btn.textContent = hidden ? '🙈' : '👁️';
            btn.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
        });
    });
});
