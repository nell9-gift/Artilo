/* =========================================================
   auth-register-artisan.js
   Comportements JS de la page d'inscription artisan
   ========================================================= */

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
            toggle.classList.toggle('is-visible', isHidden);
        });
    });

    // --- Zone d'upload du document d'identité ---
    const uploadZone = document.querySelector('.upload-zone');
    const fileInput = document.getElementById('identity_document');
    const fileNameLabel = document.querySelector('.upload-zone .file-name');

    if (uploadZone && fileInput) {

        // Affiche le nom du fichier sélectionné
        const showFileName = (file) => {
            if (file && fileNameLabel) {
                fileNameLabel.textContent = file.name;
            }
        };

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                showFileName(fileInput.files[0]);
            }
        });

        // Effet visuel pendant le glisser-déposer
        ['dragenter', 'dragover'].forEach((evt) => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                uploadZone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach((evt) => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                uploadZone.classList.remove('is-dragover');
            });
        });

        uploadZone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (file) {
                fileInput.files = e.dataTransfer.files;
                showFileName(file);
            }
        });
    }

});