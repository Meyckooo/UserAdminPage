document.addEventListener('DOMContentLoaded', () => {
    
    // Select triggers, modals, and close buttons
    const triggerBtns = document.querySelectorAll('.item_right_btns [data-modal]');
    const closeBtns = document.querySelectorAll('.modal_close');
    const modals = document.querySelectorAll('.modal_overlay');

    // Function to open a target modal
    function openModal(modalId) {
        const targetModal = document.getElementById(modalId);
        if (targetModal) {
            targetModal.classList.add('active');
        }
    }

    // Function to close all modals
    function closeModal() {
        modals.forEach(modal => modal.classList.remove('active'));
    }

    // Event listener for opening modals via buttons
    triggerBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = btn.getAttribute('data-modal');
            openModal(modalId);
        });
    });

    // Event listener for X close buttons
    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    // Close modal when clicking outside the modal window
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal_overlay')) {
            closeModal();
        }
    });

    // Close modal when pressing the Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });


     // STICKY LOCATOR

    const tableContainer = document.querySelector('.item_table_details');
    const formContainer = document.querySelector('.item_form_container');

    if (!tableContainer || !formContainer) return;

    // Observe table height dynamically
    const observer = new ResizeObserver(() => {
        const tableHeight = tableContainer.offsetHeight;

        if (tableHeight >= 500) {
            // Apply sticky position and height cap
            formContainer.style.position = 'sticky';
            formContainer.style.top = '20px';
            formContainer.style.maxHeight = 'calc(100vh - 40px)';
            formContainer.style.overflowY = 'auto';
        } else {
            // Revert back to normal
            formContainer.style.position = 'static';
            formContainer.style.maxHeight = 'none';
            formContainer.style.overflowY = 'visible';
        }
    });

    observer.observe(tableContainer);
});