// Newsletter form
document.querySelector('.newsletter-form button').addEventListener('click', function(e) {
    e.preventDefault();
    const email = document.querySelector('.newsletter-form input').value;
    if (email) {
        this.innerHTML = '<i class="fas fa-check"></i>';
        this.style.background = '#28a745';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-paper-plane"></i>';
            this.style.background = '';
            document.querySelector('.newsletter-form input').value = '';
        }, 2000);
    }
});

// Back to top button
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

backToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Social media click effects
document.querySelectorAll('.social-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        this.style.transform = 'scale(0.9)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 150);
    });
});