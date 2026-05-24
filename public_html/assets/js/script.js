'use strict';
document.addEventListener('DOMContentLoaded', () => {
    initHeaderScroll();
    initMobileMenu();
    initTabSwitchers();
    initStarRating();
    initCheckoutModal();
    initContactModal();
    initQuantitySelector();
});
function initHeaderScroll() {
    const header = document.getElementById('siteHeader');
    if (!header)
        return;
    const onScroll = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}
function initMobileMenu() {
    const toggle = document.getElementById('mobileMenuToggle');
    const panel = document.getElementById('headerCollapse');
    if (!toggle || !panel)
        return;
    const closeMenu = () => {
        panel.classList.remove('active');
        toggle.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
    };
    const openMenu = () => {
        panel.classList.add('active');
        toggle.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('menu-open');
    };
    toggle.addEventListener('click', () => {
        if (panel.classList.contains('active')) {
            closeMenu();
        }
        else {
            openMenu();
        }
    });
    panel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => closeMenu());
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeMenu();
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });
}
function initTabSwitchers() {
    document.addEventListener('click', (e) => {
        const target = e.target;
        const tab = target.closest('.profile-tab-btn, .sidebar-menu-item[data-tab]');
        if (!tab)
            return;
        e.preventDefault();
        const targetTab = tab.getAttribute('data-tab');
        if (!targetTab)
            return;
        const scope = tab.closest('.dashboard, main') ?? document;
        const allTabs = scope.querySelectorAll('.profile-tab-btn, .sidebar-menu-item[data-tab]');
        const allContents = scope.querySelectorAll('.profile-tab-content');
        allTabs.forEach((t) => t.classList.remove('active'));
        allContents.forEach((c) => c.classList.remove('active'));
        tab.classList.add('active');
        const activeContent = document.getElementById('tab-' + targetTab);
        if (activeContent) {
            activeContent.classList.add('active');
            if (tab.classList.contains('sidebar-menu-item')) {
                activeContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const targetBtn = document.querySelector(`.profile-tab-btn[data-tab="${tabParam}"]`) ??
            document.querySelector(`.sidebar-menu-item[data-tab="${tabParam}"]`);
        if (targetBtn) {
            targetBtn.click();
        }
    }
}
function initStarRating() {
    const stars = document.querySelectorAll('#star-selector i');
    const ratingInput = document.getElementById('rating-value');
    if (!stars.length || !ratingInput)
        return;
    stars.forEach((star) => {
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value') ?? '0', 10);
            ratingInput.value = value.toString();
            stars.forEach((s, idx) => {
                s.className = idx < value ? 'fas fa-star' : 'far fa-star';
            });
        });
    });
}
function initCheckoutModal() {
    initModal('checkout-modal', 'open-checkout-btn', 'close-checkout-btn', 'cancel-checkout-btn');
}
function initContactModal() {
    initModal('contact-modal', 'open-contact-btn', 'close-contact-btn', 'ok-contact-btn');
}
function initModal(modalId, openId, closeId, cancelId) {
    const modal = document.getElementById(modalId);
    if (!modal)
        return;
    const openBtn = document.getElementById(openId);
    const closeBtn = document.getElementById(closeId);
    const cancelBtn = cancelId ? document.getElementById(cancelId) : null;
    const openModal = () => {
        modal.classList.add('active');
        document.body.classList.add('menu-open');
    };
    const closeModal = () => {
        modal.classList.remove('active');
        document.body.classList.remove('menu-open');
    };
    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal)
            closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}
function initQuantitySelector() {
    const input = document.getElementById('jumlah');
    const minus = document.getElementById('btn-minus');
    const plus = document.getElementById('btn-plus');
    const display = document.getElementById('total-bayar-display');
    if (!input || !minus || !plus || !display)
        return;
    const hargaSatuan = parseInt(display.textContent?.replace(/\D/g, '') ?? '0', 10);
    const updateTotal = (qty) => {
        const total = qty * hargaSatuan;
        display.textContent = 'Rp ' + total.toLocaleString('id-ID');
    };
    minus.addEventListener('click', () => {
        let val = parseInt(input.value, 10) || 1;
        if (val > 1) {
            val--;
            input.value = val.toString();
            updateTotal(val);
        }
    });
    plus.addEventListener('click', () => {
        let val = parseInt(input.value, 10) || 1;
        val++;
        input.value = val.toString();
        updateTotal(val);
    });
}
