<?php 
require_once 'config.php';
session_start();
require_once 'auth.php';
$user = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QL Nhà Hàng - Trang Chủ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ QL Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Trang chủ</a></li>
                <li><a href="menu.php">Thực đơn</a></li>
                <li><a href="about.php">Giới thiệu</a></li>
                <li><a href="reservation.php">Đặt bàn</a></li>
                <?php if ($user): ?>
                    <li><a href="customer_dashboard.php">👤 <?php echo htmlspecialchars($user['full_name']); ?></a></li>
                    <li><a href="logout.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section với ảnh nền -->
    <section class="hero hero-with-image">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Chào Mừng Đến Với QL Nhà Hàng</h1>
            <p class="hero-subtitle">Hương vị truyền thống - Phong cách hiện đại</p>
            <p class="hero-description">Trải nghiệm ẩm thực đẳng cấp tại không gian sang trọng</p>
            <div class="hero-buttons">
                <a href="reservation.php" class="btn btn-primary btn-large">Đặt bàn ngay</a>
                <a href="menu.php" class="btn btn-secondary btn-large">Xem thực đơn</a>
            </div>
        </div>
    </section>

    <!-- Giới thiệu về nhà hàng -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Nhà hàng 5 sao" class="about-img">
                </div>
                <div class="about-text">
                    <h2 class="section-title">Về QL Nhà Hàng</h2>
                    <p class="lead">Chúng tôi tự hào là nhà hàng 5 sao hàng đầu tại Việt Nam, mang đến trải nghiệm ẩm thực đẳng cấp quốc tế.</p>
                    <p>Với hơn 10 năm kinh nghiệm trong ngành ẩm thực, QL Nhà Hàng đã trở thành điểm đến yêu thích của thực khách sành ăn. Chúng tôi kết hợp tinh hoa ẩm thực Việt Nam truyền thống với phong cách chế biến hiện đại, tạo nên những món ăn độc đáo và hấp dẫn.</p>
                    <p>Đội ngũ đầu bếp giàu kinh nghiệm của chúng tôi được đào tạo bài bản, luôn tận tâm trong từng món ăn. Nguyên liệu được tuyển chọn kỹ lưỡng từ các nhà cung cấp uy tín, đảm bảo độ tươi ngon và an toàn vệ sinh thực phẩm.</p>
                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">10+</div>
                            <div class="stat-label">Năm kinh nghiệm</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Món ăn đa dạng</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">1000+</div>
                            <div class="stat-label">Khách hàng hài lòng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tính năng nổi bật -->
    <section class="features">
        <div class="container">
            <h2 class="section-title text-center">Tại Sao Chọn Chúng Tôi?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🍜</div>
                    <h3>Món Ăn Đẳng Cấp</h3>
                    <p>Đa dạng món ăn Việt Nam và quốc tế, được chế biến bởi đầu bếp giàu kinh nghiệm với công thức độc quyền</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Phục Vụ Chuyên Nghiệp</h3>
                    <p>Hệ thống gọi món điện tử hiện đại, phục vụ nhanh chóng và theo dõi đơn hàng dễ dàng</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💺</div>
                    <h3>Không Gian Sang Trọng</h3>
                    <p>Không gian rộng rãi 3 tầng với thiết kế hiện đại, sang trọng và ấm cúng, phù hợp mọi dịp</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌟</div>
                    <h3>Nguyên Liệu Tươi Ngon</h3>
                    <p>Nguyên liệu được tuyển chọn kỹ lưỡng từ các nhà cung cấp uy tín, đảm bảo độ tươi ngon tuyệt đối</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Đầu Bếp Chuyên Nghiệp</h3>
                    <p>Đội ngũ đầu bếp được đào tạo bài bản, giàu kinh nghiệm và luôn tận tâm trong từng món ăn</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Đặt Bàn Online</h3>
                    <p>Đặt bàn trước qua website dễ dàng, tiết kiệm thời gian và đảm bảo có chỗ ngồi như mong muốn</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Không gian nhà hàng -->
    <section class="space-section">
        <div class="container">
            <h2 class="section-title text-center">Không Gian Nhà Hàng</h2>
            <div class="space-grid">
                <div class="space-item">
                    <div class="space-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Không gian tầng 1">
                    </div>
                    <div class="space-content">
                        <h3>Tầng 1 - Khu Vực Chính</h3>
                        <p>Không gian rộng rãi với các bàn 2-4 người, phù hợp cho các bữa ăn gia đình và bạn bè. Thiết kế hiện đại với ánh sáng tự nhiên từ cửa sổ lớn.</p>
                        <ul class="space-features">
                            <li>✓ 6 bàn 2-4 người</li>
                            <li>✓ Không gian mở, thoáng mát</li>
                            <li>✓ Gần cửa sổ, view đẹp</li>
                        </ul>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Không gian tầng 2">
                    </div>
                    <div class="space-content">
                        <h3>Tầng 2 - Phòng VIP</h3>
                        <p>Không gian sang trọng với các phòng riêng biệt, phù hợp cho các buổi tiệc, họp mặt và sự kiện đặc biệt.</p>
                        <ul class="space-features">
                            <li>✓ 4 bàn 4-8 người</li>
                            <li>✓ Phòng riêng biệt, yên tĩnh</li>
                            <li>✓ Phù hợp tiệc và sự kiện</li>
                        </ul>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Không gian tầng 3">
                    </div>
                    <div class="space-content">
                        <h3>Tầng 3 - Phòng Tiệc</h3>
                        <p>Không gian lớn với sức chứa lên đến 12 người, lý tưởng cho các buổi tiệc lớn, họp mặt công ty và các sự kiện quan trọng.</p>
                        <ul class="space-features">
                            <li>✓ 2 phòng tiệc lớn</li>
                            <li>✓ Sức chứa 10-12 người/phòng</li>
                            <li>✓ Hệ thống âm thanh, ánh sáng chuyên nghiệp</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu nổi bật -->
    <section class="menu-highlight">
        <div class="container">
            <h2 class="section-title text-center">Món Ăn Nổi Bật</h2>
            <p class="section-subtitle text-center">Những món ăn được yêu thích nhất tại nhà hàng</p>
            <div class="menu-grid">
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Bò bít tết">
                    </div>
                    <div class="menu-content">
                        <h3>Bò Bít Tết Úc</h3>
                        <p>Bít tết bò Úc cao cấp, thịt mềm, được chế biến theo công thức độc quyền</p>
                        <div class="menu-price">250.000 đ</div>
                    </div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Cá hồi nướng">
                    </div>
                    <div class="menu-content">
                        <h3>Cá Hồi Na Uy Nướng</h3>
                        <p>Cá hồi Na Uy tươi sống nướng muối ớt, giữ nguyên vị ngọt tự nhiên</p>
                        <div class="menu-price">280.000 đ</div>
                    </div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Lẩu hải sản">
                    </div>
                    <div class="menu-content">
                        <h3>Lẩu Hải Sản</h3>
                        <p>Lẩu hải sản tươi sống đầy đặn, nước dùng đậm đà (2-3 người)</p>
                        <div class="menu-price">450.000 đ</div>
                    </div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Set menu">
                    </div>
                    <div class="menu-content">
                        <h3>Set Menu Cao Cấp</h3>
                        <p>Set menu đầy đủ 5 món cho 2 người, trải nghiệm ẩm thực đẳng cấp</p>
                        <div class="menu-price">800.000 đ</div>
                    </div>
                </div>
            </div>
            <div class="text-center" style="margin-top: 3rem;">
                <a href="menu.php" class="btn btn-primary btn-large">Xem Toàn Bộ Thực Đơn</a>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Sẵn Sàng Trải Nghiệm?</h2>
            <p>Hãy ghé thăm chúng tôi hoặc đặt bàn trước để có trải nghiệm tốt nhất</p>
            <a href="reservation.php" class="btn btn-primary btn-lg">Đặt Bàn Ngay</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Liên Hệ</h3>
                    <p>📍 123 Đường ABC, Quận 1, TP.HCM</p>
                    <p>📞 (028) 1234 5678</p>
                    <p>✉️ info@qlnhahang.com</p>
                </div>
                <div class="footer-section">
                    <h3>Giờ Mở Cửa</h3>
                    <p>Thứ 2 - Thứ 6: 10:00 - 22:00</p>
                    <p>Thứ 7 - Chủ nhật: 09:00 - 23:00</p>
                </div>
                <div class="footer-section">
                    <h3>Theo Dõi</h3>
                    <p>Facebook | Instagram | Zalo</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
