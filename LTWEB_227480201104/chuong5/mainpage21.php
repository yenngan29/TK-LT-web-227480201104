<?php
session_start(); // Tiếp tục session

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_SESSION["username"]) && isset($_SESSION["email"])) {
    $username = $_SESSION["username"];
    $email = $_SESSION["email"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trang chính</title>
</head>
<body>
    <h1>TRANG CHÍNH</h1>
    <p>Người dùng đã đăng nhập với tên: <?php echo $username; ?> và Email là: <?php echo $email; ?></p>
    <p><a href="logout21.php">Đăng xuất</a></p>
</body>
</html>
<?php
} else {
    // Nếu không có session username hoặc email, có nghĩa là chưa đăng nhập
    echo "Bạn chưa đăng nhập.";
    echo '<p><a href="login21.html">Đăng nhập</a></p>';
    // Hoặc chuyển hướng về trang đăng nhập
    // header("Location: login21.html");
    // exit();
}
?>