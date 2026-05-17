<!-- Success Page - Order Confirmation -->
<section class="success">
    <div class="success-top">
        <p>ĐẶT HÀNG THÀNH CÔNG</p>
    </div>
    
    <div class="success-text">
        <?php if (isset($paymentInfo) && $paymentInfo): ?>
            <p>Chào <span style="font-size: 20px; color: #378000;"><?php echo htmlspecialchars($paymentInfo['customer_name'] ?? 'Khách hàng'); ?></span>, 
               đơn hàng của bạn với mã <span style="font-size: 20px; color: #378000;"><?php echo htmlspecialchars($orderCode); ?></span> đã được đặt thành công.</p>
            
            <div class="order-details" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="margin-bottom: 10px; color: #333;">Chi tiết đơn hàng:</h4>
                <p><strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($orderCode); ?></p>
                <p><strong>Tổng tiền:</strong> <span style="color: #e74c3c; font-weight: bold;"><?php echo number_format($paymentInfo['amount'], 0, ',', '.'); ?> VNĐ</span></p>
                <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($paymentInfo['payment_method'] ?? 'Không xác định'); ?></p>
                <p><strong>Phương thức giao hàng:</strong> <?php echo htmlspecialchars($paymentInfo['delivery_method'] ?? 'Không xác định'); ?></p>
                <p><strong>Thời gian đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($paymentInfo['created_at'] ?? 'now')); ?></p>
            </div>
            
            <div class="important-notes" style="margin: 20px 0;">
                <p>Đơn hàng của bạn đã được xác nhận tự động.</p>
                <p style="font-weight: bold;">Hiện tại do đang trong Chương trình Sale lớn, đơn hàng của quý khách sẽ gửi chậm hơn so với thời gian dự kiến từ 5-10 ngày. Rất mong quý khách thông cảm vì sự bất tiện này!</p>
                <p style="color: red;">(Lưu ý: VOXFOOTBALL sẽ không gọi xác nhận đơn hàng, đơn hàng được xử lý tự động và sẽ giao cho bạn trong thời gian sớm nhất)</p>
                <p>Cảm ơn <span style="font-size: 20px; color: #378000;"><?php echo htmlspecialchars($paymentInfo['customer_name'] ?? 'bạn'); ?></span> đã tin dùng sản phẩm của VOXFOOTBALL.</p>
            </div>
        <?php else: ?>
            <p style="color: #e74c3c;">Không tìm thấy thông tin đơn hàng. Vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>
        <?php endif; ?>
    </div>
    
    <div class="success-button">
        <?php if (isset($paymentInfo) && isset($paymentInfo['order_id'])): ?>
            <a href="index.php?page=order-detail&order_id=<?php echo $paymentInfo['order_id']; ?>"><button>XEM CHI TIẾT ĐƠN HÀNG</button></a>
        <?php else: ?>
            <a href="index.php?page=order-detail"><button>XEM CHI TIẾT ĐƠN HÀNG</button></a>
        <?php endif; ?>
        <a href="index.php"><button>TIẾP TỤC MUA SẮM</button></a>
    </div>
    
    <p>Mọi thắc mắc quý khách vui lòng liên hệ hotline <span style="font-size: 20px; color: red;">0973 999 949</span> hoặc chat với kênh hỗ trợ trên website để được hỗ trợ nhanh nhất.</p>
</section>