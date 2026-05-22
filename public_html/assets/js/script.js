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
    const pembeliTabs = document.querySelectorAll('.profile-tab-btn');
    const pembeliContents = document.querySelectorAll('.profile-tab-content');
    pembeliTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;
            if (!targetTab)
                return;
            pembeliTabs.forEach(t => t.classList.remove('active'));
            pembeliContents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + targetTab)?.classList.add('active');
        });
    });
    const penjualTabs = document.querySelectorAll('.sidebar-menu-item[data-tab]');
    penjualTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            const targetTab = tab.dataset.tab;
            if (!targetTab)
                return;
            penjualTabs.forEach(t => t.classList.remove('active'));
            pembeliContents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            const activeContent = document.getElementById('tab-' + targetTab);
            if (activeContent) {
                activeContent.classList.add('active');
                activeContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const targetBtn = document.querySelector(`.profile-tab-btn[data-tab="${tabParam}"]`)
            ?? document.querySelector(`.sidebar-menu-item[data-tab="${tabParam}"]`);
        targetBtn?.click();
    }
}
function initStarRating() {
    const stars = document.querySelectorAll('#star-selector i');
    const ratingInput = document.getElementById('rating-value');
    if (!stars.length || !ratingInput)
        return;
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.dataset.value ?? '0');
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
