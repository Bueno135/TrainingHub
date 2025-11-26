/**
 * TrainingHub - register.js
 * Validações e interatividade da página de cadastro
 */

// Validação de senha em tempo real
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const strengthBar = document.getElementById('strengthBar');
const registerForm = document.getElementById('registerForm');

// Atualizar indicador de força da senha
passwordInput.addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    
    strengthBar.className = 'password-strength-bar';
    
    if (strength >= 3) {
        strengthBar.classList.add('password-strength-strong');
    } else if (strength >= 2) {
        strengthBar.classList.add('password-strength-medium');
    } else if (strength >= 1) {
        strengthBar.classList.add('password-strength-weak');
    }
});

/**
 * Calcula a força da senha
 * @param {string} password - Senha a ser avaliada
 * @returns {number} - Força de 0 a 3
 */
function calculatePasswordStrength(password) {
    let strength = 0;
    
    // Comprimento mínimo
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    
    // Letras maiúsculas e minúsculas
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    
    // Contém números
    if (/\d/.test(password)) strength++;
    
    // Caracteres especiais
    if (/[^a-zA-Z\d]/.test(password)) strength++;
    
    return Math.min(strength, 3);
}

// Validar confirmação de senha ao submeter
registerForm.addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        confirmInput.focus();
        confirmInput.style.borderColor = '#f44';
        return false;
    }
    
    // Validar força mínima da senha
    const strength = calculatePasswordStrength(password);
    if (strength < 1) {
        e.preventDefault();
        alert('A senha é muito fraca. Use pelo menos 6 caracteres.');
        passwordInput.focus();
        return false;
    }
});

// Remover erro visual ao corrigir
confirmInput.addEventListener('input', function() {
    if (this.value === passwordInput.value) {
        this.style.borderColor = '#4f4';
    } else {
        this.style.borderColor = '#f44';
    }
});