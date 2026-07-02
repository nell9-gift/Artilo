// ----- Onglets "Comment ça marche" -----
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    const targetBtn = Array.from(document.querySelectorAll('.tab-btn'))
        .find(btn => btn.getAttribute('onclick') === `switchTab('${tab}')`);
    if (targetBtn) targetBtn.classList.add('active');

    const targetContent = document.getElementById(`tab-${tab}`);
    if (targetContent) targetContent.classList.add('active');
}

// ----- Année dynamique dans le footer -----
document.addEventListener('DOMContentLoaded', function () {
    const yearEl = document.getElementById('year');
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }
});