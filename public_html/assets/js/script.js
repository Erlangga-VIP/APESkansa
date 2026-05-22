'use strict';
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initTabSwitchers();
    initStarRating();
    initCheckoutModal();
    initContactModal();
    initQuantitySelector();
});
function initMobileMenu() {
    const toggle = document.getElementById('mobileMenuToggle');
    const nav = document.getElementById('mainNav');
    if (!toggle || !nav)
        return;
    toggle.addEventListener('click', () => {
        nav.classList.toggle('active');
        toggle.classList.toggle('active');
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
        const allTabs = document.querySelectorAll('.profile-tab-btn, .sidebar-menu-item[data-tab]');
        const allContents = document.querySelectorAll('.profile-tab-content');
        allTabs.forEach(t => t.classList.remove('active'));
        allContents.forEach(c => c.classList.remove('active'));
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
        const targetBtn = document.querySelector(`.profile-tab-btn[data-tab="${tabParam}"]`)
            ?? document.querySelector(`.sidebar-menu-item[data-tab="${tabParam}"]`);
        if (targetBtn)
            targetBtn.click();
    }
}
function initStarRating() {
    const stars = document.querySelectorAll('#star-selector i');
    const ratingInput = document.getElementById('rating-value');
    if (!stars.length || !ratingInput)
        return;
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value') ?? '0');
            ratingInput.value = value.toString();
            stars.forEach((s, idx) => {
                s.className = idx < value ? 'fas fa-star' : 'far fa-star';
            });
        });
    });
}
function initCheckoutModal() {
    const modal = document.getElementById('checkout-modal');
    if (!modal)
        return;
    const openBtn = document.getElementById('open-checkout-btn');
    const closeBtn = document.getElementById('close-checkout-btn');
    const cancelBtn = document.getElementById('cancel-checkout-btn');
    const openModal = () => modal.classList.add('active');
    const closeModal = () => modal.classList.remove('active');
    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal)
            closeModal();
    });
}
function initContactModal() {
    const modal = document.getElementById('contact-modal');
    if (!modal)
        return;
    const openBtn = document.getElementById('open-contact-btn');
    const closeBtn = document.getElementById('close-contact-btn');
    const okBtn = document.getElementById('ok-contact-btn');
    const openModal = () => modal.classList.add('active');
    const closeModal = () => modal.classList.remove('active');
    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    okBtn?.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal)
            closeModal();
    });
}
function initQuantitySelector() {
    const input = document.getElementById('jumlah');
    const minus = document.getElementById('btn-minus');
    const plus = document.getElementById('btn-plus');
    const display = document.getElementById('total-bayar-display');
    if (!input || !minus || !plus || !display)
        return;
    const hargaSatuan = parseInt(display.textContent?.replace(/\D/g, '') ?? '0');
    const updateTotal = (qty) => {
        const total = qty * hargaSatuan;
        display.textContent = 'Rp ' + total.toLocaleString('id-ID');
    };
    minus.addEventListener('click', () => {
        let val = parseInt(input.value);
        if (val > 1) {
            val--;
            input.value = val.toString();
            updateTotal(val);
        }
    });
    plus.addEventListener('click', () => {
        let val = parseInt(input.value);
        val++;
        input.value = val.toString();
        updateTotal(val);
    });
}
