const open = document.getElementById('open');
const modal = document.getElementById('modal-container');

open.addEventListener('click', () => {
    modalContainer.classList.add('show');
});

modalContainer.addEventListener('click', () => {
    modalContainer.classList.remove('show');
});