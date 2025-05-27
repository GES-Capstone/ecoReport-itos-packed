function toggleVisibility(targetId, others) {
  const target = document.getElementById(targetId);
  const isVisible = target.style.display === 'block';

  others.forEach(id => {
    document.getElementById(id).style.display = 'none';
  });

  target.style.display = isVisible ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('upload-btn').addEventListener('click', function () {
    toggleVisibility('upload-wrapper', ['change-password-wrapper', 'change-username-wrapper']);
  });

  document.getElementById('change-password-btn').addEventListener('click', function () {
    toggleVisibility('change-password-wrapper', ['upload-wrapper', 'change-username-wrapper']);
  });

  document.getElementById('change-username-btn').addEventListener('click', function () {
    toggleVisibility('change-username-wrapper', ['upload-wrapper', 'change-password-wrapper']);
  });

  document.getElementById('current-avatar').addEventListener('click', function () {
    const modal = document.getElementById('avatar-modal');
    const modalImg = document.getElementById('avatar-modal-img');
    modalImg.src = this.src;
    modal.style.display = 'flex';
  });

  document.getElementById('close-avatar-modal').addEventListener('click', function () {
    document.getElementById('avatar-modal').style.display = 'none';
  });

  document.getElementById('avatar-modal').addEventListener('click', function (e) {
    if (e.target === this) {
      this.style.display = 'none';
    }
  });

  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      document.getElementById('loading-overlay').style.display = 'flex';
    });
  });

  window.addEventListener('load', () => {
    document.getElementById('loading-overlay').style.display = 'none';
  });
});