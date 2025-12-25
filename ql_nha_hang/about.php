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
    <title>Giới Thiệu - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>🍽️ QL Nhà Hàng</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="menu.php">Thực đơn</a></li>
                <li><a href="about.php" class="active">Giới thiệu</a></li>
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

    <!-- Hero Section -->
    <section class="hero hero-with-image">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Về QL Nhà Hàng</h1>
            <p class="hero-subtitle">Hương vị truyền thống - Phong cách hiện đại</p>
            <p class="hero-description">Khám phá câu chuyện và tầm nhìn của chúng tôi</p>
        </div>
    </section>

    <!-- Giới thiệu chính -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Nhà hàng QL" class="about-img">
                </div>
                <div class="about-text">
                    <h2 class="section-title">Câu Chuyện Của Chúng Tôi</h2>
                    <p class="lead">QL Nhà Hàng được thành lập với tầm nhìn mang đến trải nghiệm ẩm thực đẳng cấp quốc tế ngay tại Việt Nam.</p>
                    <p>Với hơn 10 năm kinh nghiệm trong ngành ẩm thực, QL Nhà Hàng đã trở thành điểm đến yêu thích của thực khách sành ăn. Chúng tôi tự hào kết hợp tinh hoa ẩm thực Việt Nam truyền thống với phong cách chế biến hiện đại, tạo nên những món ăn độc đáo và hấp dẫn.</p>
                    <p>Đội ngũ đầu bếp giàu kinh nghiệm của chúng tôi được đào tạo bài bản, luôn tận tâm trong từng món ăn. Nguyên liệu được tuyển chọn kỹ lưỡng từ các nhà cung cấp uy tín, đảm bảo độ tươi ngon và an toàn vệ sinh thực phẩm.</p>
                    <p>Không gian nhà hàng được thiết kế sang trọng với 3 tầng, mỗi tầng mang một phong cách riêng biệt, phù hợp với mọi dịp từ bữa ăn gia đình đến các sự kiện quan trọng.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Thống kê -->
    <section class="features" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container">
            <h2 class="section-title text-center" style="color: white;">Thành Tựu Của Chúng Tôi</h2>
            <div class="about-stats">
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">10+</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Năm kinh nghiệm</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">50+</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Món ăn đa dạng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">1000+</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Khách hàng hài lòng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">12</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Bàn ăn</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">3</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Tầng phục vụ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: white;">24/7</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Hỗ trợ khách hàng</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tầm nhìn và Sứ mệnh -->
    <section class="space-section">
        <div class="container">
            <div class="space-grid">
                <div class="space-item">
                    <div class="space-content">
                        <div class="feature-icon" style="font-size: 4rem; text-align: center; margin-bottom: 1rem;">🎯</div>
                        <h3>Tầm Nhìn</h3>
                        <p>Trở thành nhà hàng hàng đầu tại Việt Nam, được công nhận về chất lượng dịch vụ và ẩm thực đẳng cấp quốc tế. Chúng tôi mong muốn mang đến trải nghiệm ẩm thực độc đáo, kết hợp giữa truyền thống và hiện đại.</p>
                        <p>Hướng tới việc mở rộng và phát triển hệ thống nhà hàng trên toàn quốc, mang hương vị QL đến với nhiều khách hàng hơn nữa.</p>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-content">
                        <div class="feature-icon" style="font-size: 4rem; text-align: center; margin-bottom: 1rem;">💎</div>
                        <h3>Sứ Mệnh</h3>
                        <p>Cam kết mang đến cho khách hàng những món ăn ngon nhất với nguyên liệu tươi ngon nhất. Chúng tôi đặt chất lượng và sự hài lòng của khách hàng lên hàng đầu.</p>
                        <p>Xây dựng đội ngũ nhân viên chuyên nghiệp, tận tâm và luôn sẵn sàng phục vụ khách hàng với nụ cười thân thiện và thái độ nhiệt tình.</p>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-content">
                        <div class="feature-icon" style="font-size: 4rem; text-align: center; margin-bottom: 1rem;">⭐</div>
                        <h3>Giá Trị Cốt Lõi</h3>
                        <p><strong>Chất lượng:</strong> Luôn đảm bảo chất lượng tốt nhất trong từng món ăn và dịch vụ.</p>
                        <p><strong>Tận tâm:</strong> Phục vụ khách hàng với sự tận tâm và chu đáo nhất.</p>
                        <p><strong>Đổi mới:</strong> Không ngừng cải tiến và đổi mới để mang đến trải nghiệm tốt hơn.</p>
                        <p><strong>Uy tín:</strong> Xây dựng thương hiệu dựa trên sự uy tín và tin cậy.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Đội ngũ -->
    <section class="about-section">
        <div class="container">
            <h2 class="section-title text-center">Đội Ngũ Chuyên Nghiệp</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Đầu Bếp Chính</h3>
                    <p>Đội ngũ đầu bếp với hơn 15 năm kinh nghiệm, được đào tạo tại các trường ẩm thực danh tiếng trong và ngoài nước. Luôn sáng tạo và đổi mới trong từng món ăn.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👨‍💼</div>
                    <h3>Quản Lý Dịch Vụ</h3>
                    <p>Đội ngũ quản lý chuyên nghiệp, giàu kinh nghiệm trong việc tổ chức và điều phối các sự kiện, đảm bảo mọi dịch vụ diễn ra suôn sẻ và hoàn hảo.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Nhân Viên Phục Vụ</h3>
                    <p>Đội ngũ nhân viên được đào tạo bài bản về kỹ năng phục vụ, luôn thân thiện, nhiệt tình và sẵn sàng hỗ trợ khách hàng mọi lúc mọi nơi.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🧹</div>
                    <h3>Vệ Sinh & An Toàn</h3>
                    <p>Tuân thủ nghiêm ngặt các tiêu chuẩn vệ sinh an toàn thực phẩm, đảm bảo không gian nhà hàng luôn sạch sẽ và an toàn cho khách hàng.</p>
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
                        <p>Không gian rộng rãi với các bàn 2-4 người, phù hợp cho các bữa ăn gia đình và bạn bè. Thiết kế hiện đại với ánh sáng tự nhiên từ cửa sổ lớn, tạo cảm giác thoáng mát và ấm cúng.</p>
                        <ul class="space-features">
                            <li>✓ 6 bàn 2-4 người</li>
                            <li>✓ Không gian mở, thoáng mát</li>
                            <li>✓ Gần cửa sổ, view đẹp</li>
                            <li>✓ Phù hợp bữa ăn gia đình</li>
                        </ul>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Không gian tầng 2">
                    </div>
                    <div class="space-content">
                        <h3>Tầng 2 - Phòng VIP</h3>
                        <p>Không gian sang trọng với các phòng riêng biệt, phù hợp cho các buổi tiệc, họp mặt và sự kiện đặc biệt. Thiết kế tinh tế với nội thất cao cấp.</p>
                        <ul class="space-features">
                            <li>✓ 4 bàn 4-8 người</li>
                            <li>✓ Phòng riêng biệt, yên tĩnh</li>
                            <li>✓ Phù hợp tiệc và sự kiện</li>
                            <li>✓ Nội thất cao cấp</li>
                        </ul>
                    </div>
                </div>
                <div class="space-item">
                    <div class="space-image">
                        <img src="https://statics.vincom.com.vn/xu-huong/cac-mon-an-nha-hang-5-sao/nha-hang-5-sao-la-gi.jpg" alt="Không gian tầng 3">
                    </div>
                    <div class="space-content">
                        <h3>Tầng 3 - Phòng Tiệc</h3>
                        <p>Không gian lớn với sức chứa lên đến 12 người, lý tưởng cho các buổi tiệc lớn, họp mặt công ty và các sự kiện quan trọng. Được trang bị đầy đủ hệ thống âm thanh và ánh sáng chuyên nghiệp.</p>
                        <ul class="space-features">
                            <li>✓ 2 phòng tiệc lớn</li>
                            <li>✓ Sức chứa 10-12 người/phòng</li>
                            <li>✓ Hệ thống âm thanh, ánh sáng chuyên nghiệp</li>
                            <li>✓ Phù hợp sự kiện lớn</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cam kết -->
    <section class="cta-section">
        <div class="container">
            <h2>Cam Kết Của Chúng Tôi</h2>
            <p>QL Nhà Hàng cam kết mang đến cho quý khách hàng những trải nghiệm ẩm thực tuyệt vời nhất với:</p>
            <div class="features-grid" style="margin-top: 3rem;">
                <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <div class="feature-icon">🍽️</div>
                    <h3 style="color: white;">Món Ăn Chất Lượng</h3>
                    <p>Nguyên liệu tươi ngon, chế biến công phu</p>
                </div>
                <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <div class="feature-icon">💼</div>
                    <h3 style="color: white;">Dịch Vụ Chuyên Nghiệp</h3>
                    <p>Phục vụ tận tâm, chu đáo và nhiệt tình</p>
                </div>
                <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <div class="feature-icon">🏆</div>
                    <h3 style="color: white;">Giá Trị Tốt Nhất</h3>
                    <p>Chất lượng cao với mức giá hợp lý</p>
                </div>
            </div>
            <div style="margin-top: 3rem;">
                <a href="reservation.php" class="btn btn-secondary btn-lg">Đặt Bàn Ngay</a>
                <a href="menu.php" class="btn btn-secondary btn-lg" style="margin-left: 1rem;">Xem Thực Đơn</a>
            </div>
        </div>
    </section>

    <!-- Thông tin liên hệ -->
    <section class="about-section" style="background: #f8f9fa;">
        <div class="container">
            <h2 class="section-title text-center">Thông Tin Liên Hệ</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <h3>Địa Chỉ</h3>
                    <p>123 Đường ABC, Quận 1, TP.HCM</p>
                    <p>Việt Nam</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📞</div>
                    <h3>Điện Thoại</h3>
                    <p>(028) 1234 5678</p>
                    <p>Hotline: 1900 1234</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✉️</div>
                    <h3>Email</h3>
                    <p>info@qlnhahang.com</p>
                    <p>booking@qlnhahang.com</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🕐</div>
                    <h3>Giờ Mở Cửa</h3>
                    <p><strong>Thứ 2 - Thứ 6:</strong> 10:00 - 22:00</p>
                    <p><strong>Thứ 7 - Chủ nhật:</strong> 09:00 - 23:00</p>
                </div>
            </div>
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

