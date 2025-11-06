/**
 * TrainingHub - dashboard.js
 * Animações e interatividade do dashboard
 */

// Animação de entrada dos cards de estatísticas
document.addEventListener('DOMContentLoaded', function() {
    animateStatCards();
});

/**
 * Anima a entrada dos cards de estatísticas
 */
function animateStatCards() {
    const cards = document.querySelectorAll('.stat-card');
    
    cards.forEach((card, index) => {
        // Estado inicial
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        // Animar com delay progressivo
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Animar contadores (número crescendo)
 * Pode ser usado futuramente quando os valores forem dinâmicos
 */
function animateCounter(element, target, duration = 1000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Adicionar efeito de hover nos action buttons
const actionButtons = document.querySelectorAll('.action-btn');
actionButtons.forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.3s ease';
    });
});