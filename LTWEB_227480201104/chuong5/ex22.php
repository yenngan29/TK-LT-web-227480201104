<?php
// Nếu form được submit, xử lý dữ liệu và tạo cookie
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu người dùng nhập và loại bỏ khoảng trắng thừa
    $tenKH = trim($_POST['tenKH']);
    $soDT = trim($_POST['soDT']);

    // Tạo cookie với thời gian tồn tại là 600 giây (10 phút)
    setcookie('tenKH', $tenKH, time() + 600);
    setcookie('soDT', $soDT, time() + 600);

    // Lưu thời gian hết hạn cookie vào biến để sử dụng cho đếm ngược
    $cookieExpire = time() + 600;
    
    // Cập nhật biến để hiển thị thông báo
    $loginMessage = "Chào bạn, <strong>$tenKH</strong>.<br>Số điện thoại: <strong>$soDT</strong>";
} else {
    $loginMessage = "";
    // Nếu chưa submit mà cookie đã tồn tại, lấy giá trị từ cookie để hiển thị
    if (isset($_COOKIE['tenKH']) && isset($_COOKIE['soDT'])) {
        $loginMessage = "Chào bạn, <strong>" . $_COOKIE['tenKH'] . "</strong>.<br>Số điện thoại: <strong>" . $_COOKIE['soDT'] . "</strong>";
    }
    // Nếu cookie chưa được tạo, giả sử thời gian hết hạn là hiện tại (để đếm ngược hiển thị "Hết hạn")
    $cookieExpire = isset($_COOKIE['tenKH']) ? time() + 600 : time();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang Đăng Nhập Khách Hàng</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      padding: 50px;
      text-align: center;
    }
    form {
      background-color: #fff;
      padding: 20px;
      border: 2px solid #007bff;
      border-radius: 5px;
      display: inline-block;
      margin-bottom: 20px;
    }
    input[type="text"],
    input[type="tel"] {
      padding: 8px;
      margin: 10px 0;
      width: 300px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      padding: 10px 20px;
      background-color: #28a745;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
    .message {
      font-size: 18px;
      color: #007bff;
      margin-top: 20px;
    }
    /* Đồng hồ chạy và đếm ngược */
    #clock, #cookieCountdown {
      font-size: 20px;
      color: #dc3545;
      margin-top: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h2>Đăng Nhập Khách Hàng</h2>
  <!-- Hiển thị đồng hồ hiện tại -->
  <div id="clock"></div>
  <!-- Hiển thị đếm ngược thời gian cookie (10 phút) -->
  <div id="cookieCountdown"></div>
  
  <form method="post" action="">
    <div>
      <label for="tenKH">Tên khách hàng:</label><br>
      <input type="text" name="tenKH" id="tenKH" required>
    </div>
    <div>
      <label for="soDT">Số điện thoại:</label><br>
      <input type="tel" name="soDT" id="soDT" required>
    </div>
    <button type="submit">Đăng nhập</button>
  </form>
  
  <?php if (!empty($loginMessage)): ?>
    <div class="message">
      <?php echo $loginMessage; ?>
    </div>
  <?php endif; ?>

  <script>
    // Hàm cập nhật đồng hồ hiện tại
    function updateClock() {
      var now = new Date();
      document.getElementById("clock").innerHTML = "Giờ hiện tại: " + now.toLocaleTimeString();
    }
    // Gọi hàm cập nhật đồng hồ mỗi giây
    setInterval(updateClock, 1000);
    updateClock();

    // Đếm ngược thời gian cookie
    // Lấy thời gian hết hạn cookie từ PHP (đơn vị mili giây)
    var cookieExpireTime = <?php echo $cookieExpire * 1000; ?>;
    function updateCookieCountdown() {
      var now = new Date().getTime();
      var distance = cookieExpireTime - now;
      
      if (distance < 0) {
        document.getElementById("cookieCountdown").innerHTML = "Cookie đã hết hạn!";
      } else {
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("cookieCountdown").innerHTML = "Cookie hết hạn sau: " + minutes + " phút " + seconds + " giây";
      }
    }
    setInterval(updateCookieCountdown, 1000);
    updateCookieCountdown();
  </script>
</body>
</html>