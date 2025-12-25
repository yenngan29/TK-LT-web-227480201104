<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng Dẫn Khách Hàng - QL Nhà Hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .guide-section {
            background: white;
            padding: 2rem;
            margin: 2rem 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .guide-step {
            display: flex;
            align-items: start;
            gap: 1.5rem;
            padding: 1.5rem;
            margin: 1rem 0;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        .step-number {
            background: #667eea;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        .step-content h3 {
            margin-bottom: 0.5rem;
            color: #667eea;
        }
        .scenario {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        .scenario h2 {
            margin-bottom: 1rem;
        }
    </style>
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
                <li><a href="reservation.php">Đặt bàn</a></li>
                <li><a href="customer_guide.php" class="active">Hướng dẫn</a></li>
                <li><a href="login.php">Đăng nhập</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>📖 Hướng Dẫn Sử Dụng Dành Cho Khách Hàng</h1>
            <p>Làm thế nào để đặt bàn và gọi món tại nhà hàng?</p>
        </div>
    </div>

    <div class="container">
        <div class="scenario">
            <h2>🎯 CÓ 2 CÁCH ĐẾN NHÀ HÀNG:</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 10px;">
                    <h3>📱 CÁCH 1: ĐẶT BÀN TRƯỚC</h3>
                    <p>Đặt online → Xác nhận → Đến nhà hàng</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 10px;">
                    <h3>🚶 CÁCH 2: ĐẾN TRỰC TIẾP</h3>
                    <p>Đi thẳng → Nhân viên chỉ bàn trống</p>
                </div>
            </div>
        </div>

        <div class="guide-section">
            <h2>📱 CÁCH 1: ĐẶT BÀN ONLINE (Khuyến nghị)</h2>
            <p><strong>Dành cho:</strong> Khách muốn chắc chắn có chỗ ngồi, đi nhóm đông, hoặc vào giờ cao điểm</p>

            <div class="guide-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Xem Thực Đơn Trước</h3>
                    <p>Truy cập: <a href="menu.php">Thực Đơn</a></p>
                    <p>→ Xem các món ăn, giá cả, hình ảnh</p>
                    <p>→ Chọn sẵn món muốn ăn trong đầu</p>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Đặt Bàn Online</h3>
                    <p>Truy cập: <a href="reservation.php">Đặt Bàn</a></p>
                    <p><strong>Điền thông tin:</strong></p>
                    <ul>
                        <li>Họ tên, Số điện thoại</li>
                        <li>Ngày & Giờ đến nhà hàng</li>
                        <li><strong>Số lượng khách</strong> (Quan trọng!)</li>
                        <li>Ghi chú nếu có yêu cầu đặc biệt</li>
                    </ul>
                    <p><strong>💡 Hệ thống tự động:</strong></p>
                    <ul>
                        <li>✅ Chọn bàn phù hợp với số người</li>
                        <li>✅ Kiểm tra bàn còn trống không</li>
                        <li>✅ Giữ chỗ cho bạn</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Nhận Xác Nhận</h3>
                    <p>Sau khi đặt:</p>
                    <ul>
                        <li>✅ Thông báo "Đặt bàn thành công" xuất hiện</li>
                        <li>📞 Nhân viên gọi xác nhận (trong 30 phút)</li>
                        <li>📝 Nhớ <strong>ngày giờ</strong> và <strong>số điện thoại</strong> đã đặt</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Đến Nhà Hàng</h3>
                    <p><strong>Đúng giờ đã đặt:</strong></p>
                    <ul>
                        <li>🚗 Đến nhà hàng</li>
                        <li>💬 Nói với nhân viên: "Tôi đã đặt bàn, tên [Tên của bạn]"</li>
                        <li>🪑 Nhân viên dẫn bạn đến <strong>bàn đã được chuẩn bị sẵn</strong></li>
                        <li>✅ Ngồi vào bàn và bắt đầu gọi món</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">5</div>
                <div class="step-content">
                    <h3>Gọi Món Tại Bàn</h3>
                    <p><strong>Cách 1: Gọi món điện tử (Hiện đại)</strong></p>
                    <ul>
                        <li>💻 Sử dụng máy tính bảng/laptop có sẵn trên bàn</li>
                        <li>🖱️ Truy cập trang gọi món (đã mở sẵn)</li>
                        <li>👆 Chọn món từ menu, thêm vào giỏ hàng</li>
                        <li>🔢 Điều chỉnh số lượng</li>
                        <li>✅ Nhấn "Gửi Đơn Hàng"</li>
                        <li>→ Đơn hàng tự động gửi đến bếp!</li>
                    </ul>
                    <p><strong>Cách 2: Gọi nhân viên (Truyền thống)</strong></p>
                    <ul>
                        <li>🙋 Gọi nhân viên</li>
                        <li>📖 Xem menu giấy</li>
                        <li>💬 Nói món muốn ăn</li>
                        <li>→ Nhân viên nhập vào hệ thống</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">6</div>
                <div class="step-content">
                    <h3>Đợi Món & Thưởng Thức</h3>
                    <ul>
                        <li>⏱️ Đợi bếp chế biến (10-20 phút)</li>
                        <li>🍽️ Nhân viên mang món ra</li>
                        <li>😋 Thưởng thức món ăn</li>
                        <li>➕ Muốn thêm món? Lặp lại bước 5</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">7</div>
                <div class="step-content">
                    <h3>Thanh Toán</h3>
                    <ul>
                        <li>💰 Gọi nhân viên để thanh toán</li>
                        <li>🧾 Nhận hóa đơn (có chi tiết món đã gọi)</li>
                        <li>💵 Thanh toán: Tiền mặt / Thẻ / Chuyển khoản</li>
                        <li>👋 Tạm biệt và hẹn gặp lại!</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="guide-section">
            <h2>🚶 CÁCH 2: ĐẾN TRỰC TIẾP (Walk-in)</h2>
            <p><strong>Dành cho:</strong> Khách đi 1-2 người, thời gian linh hoạt, không phải giờ cao điểm</p>

            <div class="guide-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Đến Nhà Hàng</h3>
                    <p>🚶 Đi thẳng đến nhà hàng (không cần đặt trước)</p>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Xin Bàn Trống</h3>
                    <ul>
                        <li>💬 Nói với nhân viên: "Cho tôi 1 bàn cho [số người] người"</li>
                        <li>🪑 Nhân viên kiểm tra bàn trống</li>
                        <li>✅ Nếu có: Nhân viên dẫn bạn vào bàn</li>
                        <li>⏱️ Nếu hết: Đợi hoặc quay lại sau</li>
                    </ul>
                </div>
            </div>

            <div class="guide-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Ngồi & Gọi Món</h3>
                    <p>Giống như <strong>Cách 1 - Bước 5</strong> ở trên</p>
                    <ul>
                        <li>💻 Dùng máy tính bảng trên bàn, hoặc</li>
                        <li>🙋 Gọi nhân viên để order</li>
                    </ul>
                </div>
            </div>

            <p style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-top: 2rem;">
                <strong>⚠️ Lưu ý:</strong> Giờ cao điểm (12h-13h, 18h-20h) có thể hết bàn. 
                Nên <strong>đặt bàn trước</strong> để chắc chắn có chỗ!
            </p>
        </div>

        <div class="guide-section">
            <h2>❓ CÂU HỎI THƯỜNG GẶP</h2>

            <div style="margin: 1rem 0;">
                <h3 style="color: #667eea;">💭 Làm sao biết bàn nào phù hợp?</h3>
                <p><strong>Trả lời:</strong> Bạn KHÔNG cần biết số bàn cụ thể!</p>
                <ul>
                    <li>✅ Khi đặt online: Hệ thống <strong>TỰ ĐỘNG chọn</strong> bàn phù hợp dựa trên số người</li>
                    <li>✅ Khi đến trực tiếp: Nhân viên <strong>sẽ chọn và dẫn</strong> bạn đến bàn phù hợp</li>
                    <li>→ Bạn chỉ cần nói: "Tôi đi [X] người"</li>
                </ul>
            </div>

            <div style="margin: 1rem 0;">
                <h3 style="color: #667eea;">💭 Làm sao biết gọi món gì?</h3>
                <p><strong>Trả lời:</strong></p>
                <ul>
                    <li>📖 Xem trước: <a href="menu.php">Thực Đơn</a> (có ảnh + giá)</li>
                    <li>💻 Tại bàn: Xem trên máy tính bảng (có ảnh đẹp, mô tả chi tiết)</li>
                    <li>🙋 Hỏi nhân viên: "Món nào ngon nhất?"</li>
                    <li>⭐ Xem món phổ biến (có đánh dấu)</li>
                </ul>
            </div>

            <div style="margin: 1rem 0;">
                <h3 style="color: #667eea;">💭 Tôi không biết dùng máy tính?</h3>
                <p><strong>Trả lời:</strong> Không sao cả!</p>
                <ul>
                    <li>🙋 Gọi nhân viên</li>
                    <li>📖 Xem menu giấy</li>
                    <li>💬 Nói món bạn muốn</li>
                    <li>→ Nhân viên sẽ nhập vào hệ thống giúp bạn</li>
                </ul>
            </div>

            <div style="margin: 1rem 0;">
                <h3 style="color: #667eea;">💭 Có thể hủy/đổi giờ đặt bàn không?</h3>
                <p><strong>Trả lời:</strong> Có!</p>
                <ul>
                    <li>📞 Gọi hotline: (028) 1234 5678</li>
                    <li>⏰ Thông báo trước ít nhất 2 giờ</li>
                    <li>→ Nhân viên sẽ đổi giờ hoặc hủy giúp bạn</li>
                </ul>
            </div>
        </div>

        <div class="cta-section">
            <div class="container">
                <h2>Sẵn Sàng Trải Nghiệm?</h2>
                <p>Bắt đầu bằng cách xem thực đơn hoặc đặt bàn ngay!</p>
                <div style="margin-top: 2rem;">
                    <a href="menu.php" class="btn btn-primary btn-lg">📖 Xem Thực Đơn</a>
                    <a href="reservation.php" class="btn btn-secondary btn-lg">📅 Đặt Bàn Ngay</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 QL Nhà Hàng. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

