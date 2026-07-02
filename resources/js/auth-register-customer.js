
document.addEventListener('DOMContentLoaded', () => {

    // --- Bascule afficher / masquer le mot de passe ---
    const toggles = document.querySelectorAll('.toggle-password');

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const targetId = toggle.getAttribute('data-target');
            const input = document.getElementById(targetId);

            if (!input) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            // Change l'icône (œil ouvert / œil barré)
            toggle.classList.toggle('is-visible', isHidden);
        });
    });

});