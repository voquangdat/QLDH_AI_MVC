
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced dropdown menu functionality
        const menuItems = document.querySelectorAll('.top-menu-items ul li');
        
        menuItems.forEach(item => {
            const submenu = item.querySelector('.top-menu-item');
            
            if (submenu) {
                let hoverTimeout;
                
                // Mouse enter
                item.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    submenu.style.opacity = '1';
                    submenu.style.visibility = 'visible';
                    submenu.style.transform = 'translateX(-50%) translateY(0)';
                });
                
                // Mouse leave with delay
                item.addEventListener('mouseleave', function() {
                    hoverTimeout = setTimeout(() => {
                        submenu.style.opacity = '0';
                        submenu.style.visibility = 'hidden';
                        submenu.style.transform = 'translateX(-50%) translateY(-10px)';
                    }, 200);
                });
                
                // Keep submenu open when hovering over it
                submenu.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                });
                
                submenu.addEventListener('mouseleave', function() {
                    submenu.style.opacity = '0';
                    submenu.style.visibility = 'hidden';
                    submenu.style.transform = 'translateX(-50%) translateY(-10px)';
                });
            }
        });

        // Enhanced cart mini functionality
        const cartIcon = document.querySelector('.top-menu-icons ul li:last-child');
        const cartMini = document.querySelector('.cart-content-mini');
        
        if (cartIcon && cartMini) {
            let cartTimeout;
            
            cartIcon.addEventListener('mouseenter', function() {
                clearTimeout(cartTimeout);
                cartMini.style.opacity = '1';
                cartMini.style.visibility = 'visible';
                cartMini.style.transform = 'translateY(0)';
            });
            
            cartIcon.addEventListener('mouseleave', function() {
                cartTimeout = setTimeout(() => {
                    cartMini.style.opacity = '0';
                    cartMini.style.visibility = 'hidden';
                    cartMini.style.transform = 'translateY(-10px)';
                }, 200);
            });
            
            cartMini.addEventListener('mouseenter', function() {
                clearTimeout(cartTimeout);
            });
            
            cartMini.addEventListener('mouseleave', function() {
                cartMini.style.opacity = '0';
                cartMini.style.visibility = 'hidden';
                cartMini.style.transform = 'translateY(-10px)';
            });
        }

        // User account dropdown
        const userToggle = document.querySelector('.user-dropdown-toggle');
        const userMenu   = document.querySelector('.user-dropdown-menu');

        if (userToggle && userMenu) {
            userToggle.addEventListener('click', function (e) {
                e.preventDefault();
                userMenu.classList.toggle('show');
            });

            // Đóng dropdown khi click ra ngoài
            document.addEventListener('click', function (e) {
                if (!userToggle.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.classList.remove('show');
                }
            });
        }

        // Scroll effect for header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.top');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        const searchIcon = document.querySelector('.top-menu-icons ul li:first-child i');
        
        if (searchInput && searchIcon) {
            searchIcon.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    // Redirect to search page or handle search
                    window.location.href = 'search.php?q=' + encodeURIComponent(searchTerm);
                }
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = searchInput.value.trim();
                    if (searchTerm) {
                        window.location.href = 'search.php?q=' + encodeURIComponent(searchTerm);
                    }
                }
            });
        }
    });
