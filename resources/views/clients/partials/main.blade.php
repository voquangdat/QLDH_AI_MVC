<?php
// Lấy dữ liệu sản phẩm từ controller hoặc model
// Tạm thời dùng dữ liệu mẫu
// $featuredProducts = [
//     [
//         'id' => 1,
//         'name' => 'Bộ quần áo bóng đá CLB Arsenal Home 2024-2025',
//         'price' => 120000,
//         'old_price' => 180000,
//         'image_main' => '/public/uploads/sp1.jpg',
//         'image_hover' => '/public/uploads/sp1.2.jpg',
//         'discount' => 33
//     ],
//     [
//         'id' => 2,
//         'name' => 'Bộ quần áo bóng đá CLB Manchester United Away 2024-2025',
//         'price' => 125000,
//         'old_price' => 190000,
//         'image_main' => '/public/uploads/sp2.jpg',
//         'image_hover' => '/public/uploads/sp2.2.jpg',
//         'discount' => 34
//     ],
//     [
//         'id' => 3,
//         'name' => 'Bộ quần áo bóng đá CLB Liverpool Third 2024-2025',
//         'price' => 130000,
//         'old_price' => 185000,
//         'image_main' => '/public/uploads/sp3.jpg',
//         'image_hover' => '/public/uploads/sp3.2.jpg',
//         'discount' => 30
//     ],
//     [
//         'id' => 4,
//         'name' => 'Bộ quần áo bóng đá CLB Chelsea Home 2024-2025',
//         'price' => 118000,
//         'old_price' => 175000,
//         'image_main' => '/public/uploads/sp4.jpg',
//         'image_hover' => '/public/uploads/sp4.2.jpg',
//         'discount' => 33
//     ]
// ];
?>

<link rel="stylesheet" href="/public/css/index-effects.css">

<!-- -------------Trình bày sản phẩm nổi bật ----------------- -->
<section class="product-display" id="product">
    <div class="container">
        <div class="section-header">
            <h1><i class="fas fa-fire"></i> Sản Phẩm Bán Chạy</h1>
            <p>Những sản phẩm được yêu thích nhất tại VoxFootball</p>
        </div>
        
        <div class="product-grid">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image-container">
                            <div class="product-badge">
                                <span class="discount-badge">-<?php echo $product['discount']; ?>%</span>
                                <div class="product-actions">
                                    <button class="action-btn wishlist-btn" title="Yêu thích">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="action-btn quick-view-btn" title="Xem nhanh">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn compare-btn" title="So sánh">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-images">
                                <img src="/public/uploads/<?php echo htmlspecialchars($product['image_main']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="main-image">
                                <img src="/public/uploads/<?php echo htmlspecialchars($product['image_hover']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="hover-image">
                            </div>
                            
                            <div class="size-options">
                                <span class="size-label">Size:</span>
                                <div class="size-list">
                                    <button class="size-btn" data-size="S">S</button>
                                    <button class="size-btn active" data-size="M">M</button>
                                    <button class="size-btn" data-size="L">L</button>
                                    <button class="size-btn" data-size="XL">XL</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="rating-count">(4.5)</span>
                            </div>
                            
                            <h3 class="product-name">
                                <a href="?page=product&id=<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            
                            <div class="product-price">
                                <span class="current-price"><?php echo number_format($product['price']); ?>đ</span>
                                <span class="old-price"><?php echo number_format($product['old_price']); ?>đ</span>
                            </div>
                            
                            <div class="product-buttons">
                                <button class="btn btn-cart" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i>
                                    Thêm vào giỏ
                                </button>
                                <button class="btn btn-buy" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-bolt"></i>
                                    Mua ngay
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">Không có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="?page=category" class="view-all-btn">
                <i class="fas fa-th-large"></i>
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2><i class="fas fa-envelope"></i> Đăng ký nhận tin</h2>
                <p>Nhận thông báo về sản phẩm mới và ưu đãi đặc biệt</p>
            </div>
            <div class="newsletter-form">
                <form class="newsletter-form-inner" id="newsletterForm">
                    <input type="email" placeholder="Nhập email của bạn..." required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


<script src="/public/js/home.js"></script>
<script>
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
</script>