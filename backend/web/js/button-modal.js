document.addEventListener('DOMContentLoaded', function() {
  const deleteButtons   = document.querySelectorAll('.delete-user-btn, .delete-company-btn');
  const confirmDeleteEl = document.getElementById('confirm-delete-btn');
  let deleteUrl = null;

  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      deleteUrl = this.getAttribute('data-url');
    });
  });

  confirmDeleteEl.addEventListener('click', function() {
    if (deleteUrl) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = deleteUrl;

      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_csrf';
      csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
      form.appendChild(csrfInput);

      document.body.appendChild(form);
      form.submit();
    }
  });

  const restoreButtons   = document.querySelectorAll('.restore-user-btn');
  const confirmRestoreEl = document.getElementById('confirm-restore-btn');
  let restoreUrl = null;

  restoreButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      restoreUrl = this.getAttribute('data-url');
    });
  });

  confirmRestoreEl.addEventListener('click', function() {
    if (restoreUrl) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = restoreUrl;

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
