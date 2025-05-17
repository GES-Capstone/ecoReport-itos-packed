
document.addEventListener('DOMContentLoaded', function() {
    // Delete modal handling
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const confirmDelete = document.getElementById('confirmDelete');
    
    let deleteUserId = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            deleteUserId = this.getAttribute('data-id');
        });
    });
    
    confirmDelete.addEventListener('click', function() {
        if (deleteUserId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${window.location.origin}/home/delete?id=${deleteUserId}`;
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_csrf';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});

const restoreModal = document.getElementById('restoreModal');
const restoreButtons = document.querySelectorAll('.restore-btn');
const confirmRestore = document.getElementById('confirmRestore');

let restoreUserId = null;

restoreButtons.forEach(button => {
    button.addEventListener('click', function() {
        restoreUserId = this.getAttribute('data-id');
    });
});

confirmRestore.addEventListener('click', function() {
    if (restoreUserId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${window.location.origin}/home/restore?id=${restoreUserId}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
});