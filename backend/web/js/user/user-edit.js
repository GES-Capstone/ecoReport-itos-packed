function toggleVisibility(targetId, others) {
    const target = document.getElementById(targetId);
    const isVisible = target && target.style.display === 'block';

    others.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    if (target) {
        target.style.display = isVisible ? 'none' : 'block';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const mapping = {
        'upload-btn': 'upload-wrapper',
        'change-password-btn': 'change-password-wrapper',
        'edit-data-btn': 'user-data-wrapper',
        'edit-roles-btn': 'user-roles-wrapper',
        'edit-permissions-btn': 'user-permissions-wrapper',
    };

    Object.keys(mapping).forEach(buttonId => {
        const button = document.getElementById(buttonId);
        const targetWrapper = mapping[buttonId];

        if (button && targetWrapper) {
            button.addEventListener('click', function () {
                const otherWrappers = Object.values(mapping).filter(id => id !== targetWrapper);
                toggleVisibility(targetWrapper, otherWrappers);
            });
        }
    });

    const avatar = document.getElementById('current-avatar');
    const modal = document.getElementById('avatar-modal');
    const modalImg = document.getElementById('avatar-modal-img');
    const closeBtn = document.getElementById('close-avatar-modal');

    if (avatar && modal && modalImg && closeBtn) {
        avatar.addEventListener('click', () => {
            modalImg.src = avatar.src;
            modal.style.display = 'flex';
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) overlay.style.display = 'flex';
        });
    });

    window.addEventListener('load', () => {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) overlay.style.display = 'none';
    });
});
