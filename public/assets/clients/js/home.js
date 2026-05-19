
// // Product interaction functions
// function addToCart(productId) {
//     // Add animation
//     const btn = event.target.closest('.btn-cart');
//     btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    
//     // Simulate API call
//     setTimeout(() => {
//         btn.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
//         btn.style.background = '#28a745';
        
//         // Update cart count
//         const cartCount = document.querySelector('.top-menu-icons span');
//         cartCount.textContent = parseInt(cartCount.textContent) + 1;
        
//         // Reset button after 2s
//         setTimeout(() => {
//             btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
//             btn.style.background = '';
//         }, 2000);
//     }, 1000);
// }

// function buyNow(productId) {
//     window.location.href = `product.php?id=${productId}&action=buy`;
// }

// // Size selection
// document.querySelectorAll('.size-btn').forEach(btn => {
//     btn.addEventListener('click', function() {
//         // Remove active class from siblings
//         this.parentNode.querySelectorAll('.size-btn').forEach(sibling => {
//             sibling.classList.remove('active');
//         });
//         // Add active class to clicked button
//         this.classList.add('active');
//     });
// });

// // Wishlist functionality
// document.querySelectorAll('.wishlist-btn').forEach(btn => {
//     btn.addEventListener('click', function() {
//         const icon = this.querySelector('i');
//         if (icon.classList.contains('far')) {
//             icon.classList.replace('far', 'fas');
//             this.style.color = '#e74c3c';
//         } else {
//             icon.classList.replace('fas', 'far');
//             this.style.color = '';
//         }
//     });
// });

// thử

// // Product interaction functions
// document.addEventListener('DOMContentLoaded', function() {
    
//     // Add to cart functionality
//     document.querySelectorAll('.btn-cart').forEach(btn => {
//         btn.addEventListener('click', function() {
//             const productId = this.dataset.productId;
//             const productCard = this.closest('.product-card');
//             const selectedSize = productCard.querySelector('.size-btn.active')?.dataset.size || 'M';
            
//             addToCart(productId, selectedSize, this);
//         });
//     });
    
//     // Buy now functionality
//     document.querySelectorAll('.btn-buy').forEach(btn => {
//         btn.addEventListener('click', function() {
//             const productId = this.dataset.productId;
//             buyNow(productId);
//         });
//     });
    
//     // Size selection
//     document.querySelectorAll('.size-btn').forEach(btn => {
//         btn.addEventListener('click', function() {
//             // Remove active class from siblings
//             this.parentNode.querySelectorAll('.size-btn').forEach(sibling => {
//                 sibling.classList.remove('active');
//             });
//             // Add active class to clicked button
//             this.classList.add('active');
//         });
//     });
    
//     // Wishlist functionality
//     document.querySelectorAll('.wishlist-btn').forEach(btn => {
//         btn.addEventListener('click', function() {
//             const icon = this.querySelector('i');
//             if (icon.classList.contains('far')) {
//                 icon.classList.replace('far', 'fas');
//                 this.style.color = '#e74c3c';
//             } else {
//                 icon.classList.replace('fas', 'far');
//                 this.style.color = '';
//             }
//         });
//     });
// });

// async function addToCart(productId, size, button) {
//     // Add animation
//     const originalHTML = button.innerHTML;
//     button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
//     button.disabled = true;
    
//     try {
//         const response = await fetch('/public/ajax/cart_ajax.php', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//             },
//             body: JSON.stringify({
//                 action: 'add',
//                 product_id: productId,
//                 size: size,
//                 quantity: 1
//             })
//         });
        
//         const result = await response.json();
        
//         if (result.success) {
//             button.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
//             button.style.background = '#28a745';
            
//             // Update cart count
//             const cartCount = document.querySelector('.top-menu-icons span');
//             if (cartCount) {
//                 cartCount.textContent = result.cart_count;
//             }
            
//             // Reset button after 2s
//             setTimeout(() => {
//                 button.innerHTML = originalHTML;
//                 button.style.background = '';
//                 button.disabled = false;
//             }, 2000);
//         } else {
//             throw new Error(result.message || 'Có lỗi xảy ra');
//         }
        
//     } catch (error) {
//         console.error('Error:', error);
//         button.innerHTML = '<i class="fas fa-times"></i> Lỗi';
//         button.style.background = '#dc3545';
        
//         setTimeout(() => {
//             button.innerHTML = originalHTML;
//             button.style.background = '';
//             button.disabled = false;
//         }, 2000);
//     }
// }

// function buyNow(productId) {
//     window.location.href = `?page=product&id=${productId}&action=buy`;
// }


document.addEventListener('DOMContentLoaded', function () {
    // Size selection — update cart button's variant when size changes
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const card = this.closest('.product-card');
            card.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const cartBtn = card.querySelector('.btn-cart');
            if (cartBtn) cartBtn.dataset.variantId = this.dataset.variantId;
        });
    });

    document.querySelectorAll('.btn-cart').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const variantId = this.dataset.variantId;
            if (!variantId) {
                window.location.href = this.dataset.productUrl;
                return;
            }
            await addToCartAjax(variantId, this);
        });
    });
});

async function addToCartAjax(variantId, button) {
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    try {
        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('variant_id', variantId);
        fd.append('quantity', 1);

        const res  = await fetch(CART_STORE_URL, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
            button.style.background = '#22c55e';
            await refreshMiniCart(data.cart_count);
        } else {
            button.innerHTML = '<i class="fas fa-times"></i> ' + (data.message ?? 'Lỗi');
            button.style.background = '#ef4444';
        }
    } catch {
        button.innerHTML = '<i class="fas fa-times"></i> Lỗi';
        button.style.background = '#ef4444';
    } finally {
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.background = '';
            button.disabled = false;
        }, 2000);
    }
}

async function refreshMiniCart(count) {
    try {
        const res  = await fetch(CART_MINI_URL);
        const data = await res.json();

        const tmp = document.createElement('div');
        tmp.innerHTML = data.html;

        // Replace toàn bộ dropdown (items + footer với đúng display state)
        const newDropdown = tmp.querySelector('#mini-cart-dropdown');
        if (newDropdown) {
            document.getElementById('mini-cart-dropdown').innerHTML = newDropdown.innerHTML;
        }

        // cart-count nằm ngoài dropdown — cập nhật riêng
        document.querySelectorAll('#cart-count').forEach(el => el.textContent = data.count);
    } catch { /* silent */ }
}
// Debug: Kiểm tra file JS có load không
// console.log('Checking if home.js loaded...');
// console.log('jQuery loaded:', typeof $ !== 'undefined');
// console.log('All cart buttons:', document.querySelectorAll('.btn-cart').length);

// Enhance hover effect cho product images
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const mainImage = card.querySelector('.main-image');
        const hoverImage = card.querySelector('.hover-image');
        const sizeOptions = card.querySelector('.size-options');
        
        // Nếu hover image bị lỗi hoặc không tồn tại, ẩn nó đi
        if (hoverImage) {
            hoverImage.addEventListener('error', function() {
                this.style.display = 'none';
                console.log('Hover image failed to load:', this.src);
            });
            
            // Preload hover image để hiệu ứng mượt hơn
            const img = new Image();
            img.src = hoverImage.src;
        }
        
        // Thêm loading state cho main image
        if (mainImage) {
            mainImage.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            mainImage.addEventListener('error', function() {
                this.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';
                this.style.border = '2px dashed #dee2e6';
                this.alt = 'Không có hình ảnh';
                console.log('Main image failed to load:', this.src);
            });
        }
        
        // Đảm bảo size-options luôn hiển thị khi hover vào card
        card.addEventListener('mouseenter', function() {
            if (sizeOptions) {
                sizeOptions.style.opacity = '1';
                sizeOptions.style.transform = 'translateY(0)';
                sizeOptions.style.visibility = 'visible';
            }
            
            if (hoverImage && hoverImage.complete && hoverImage.naturalHeight !== 0) {
                this.classList.add('has-hover-image');
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('has-hover-image');
            // Giữ size-options hiển thị một chút trước khi ẩn
            setTimeout(() => {
                if (!this.matches(':hover') && sizeOptions) {
                    sizeOptions.style.opacity = '0';
                    sizeOptions.style.transform = 'translateY(20px)';
                }
            }, 100);
        });
        
        // Xử lý size button clicks
        const sizeButtons = card.querySelectorAll('.size-btn');
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Remove active class from siblings
                sizeButtons.forEach(sibling => sibling.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
            });
        });
    });
});