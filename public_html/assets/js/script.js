// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }

    // File Upload Preview
    const productImage = document.getElementById('product-image');
    const fileUploadPreview = document.querySelector('.file-upload-preview');
    
    if (productImage && fileUploadPreview) {
        productImage.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    fileUploadPreview.innerHTML = `
                        <div class="preview-image">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-preview"><i class="fas fa-times"></i></button>
                        </div>
                    `;
                    
                    const removePreview = document.querySelector('.remove-preview');
                    if (removePreview) {
                        removePreview.addEventListener('click', function() {
                            fileUploadPreview.innerHTML = '';
                            productImage.value = '';
                        });
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    }

    // Product Filter
    const filterCategory = document.getElementById('filter-category');
    const filterPrice = document.getElementById('filter-price');
    const filterDate = document.getElementById('filter-date');
    const productItems = document.querySelectorAll('.product-card');
    
    if (filterCategory && productItems.length > 0) {
        filterCategory.addEventListener('change', filterProducts);
        if (filterPrice) filterPrice.addEventListener('change', filterProducts);
        if (filterDate) filterDate.addEventListener('change', filterProducts);
        
        function filterProducts() {
            const categoryValue = filterCategory.value;
            const priceValue = filterPrice ? filterPrice.value : 'all';
            const dateValue = filterDate ? filterDate.value : 'all';
            
            productItems.forEach(item => {
                const category = item.getAttribute('data-category');
                const price = parseInt(item.getAttribute('data-price'));
                const date = item.getAttribute('data-date');
                
                let showItem = true;
                
                if (categoryValue !== 'all' && category !== categoryValue) {
                    showItem = false;
                }
                
                if (priceValue !== 'all') {
                    if (priceValue === 'under10k' && price >= 10000) showItem = false;
                    if (priceValue === '10k-50k' && (price < 10000 || price > 50000)) showItem = false;
                    if (priceValue === 'over50k' && price <= 50000) showItem = false;
                }
                
                if (dateValue !== 'all' && date !== dateValue) {
                    showItem = false;
                }
                
                item.style.display = showItem ? 'block' : 'none';
            });
        }
    }

    // Simple Testimonial Slider
    const testimonialContainer = document.querySelector('.testimonials-container');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    if (testimonialContainer && testimonialCards.length > 2) {
        let currentIndex = 0;
        
        function showTestimonial(index) {
            testimonialCards.forEach((card, i) => {
                if (i === index || i === (index + 1) % testimonialCards.length || i === (index + 2) % testimonialCards.length) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Initial display
        showTestimonial(currentIndex);
        
        // Auto scroll every 5 seconds
        setInterval(() => {
            currentIndex = (currentIndex + 1) % testimonialCards.length;
            showTestimonial(currentIndex);
        }, 5000);
    }
});