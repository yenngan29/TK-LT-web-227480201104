<?php
session_start(); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // **LƯU Ý QUAN TRỌNG:**
    // Trong thực tế, bạn cần phải kiểm tra thông tin đăng nhập này
    // với dữ liệu được lưu trữ trong cơ sở dữ liệu một cách an toàn.
    // Ở đây, mình chỉ đưa ra một ví dụ đơn giản để minh họa.

    // Ví dụ kiểm tra (RẤT KHÔNG AN TOÀN CHO MÔI TRƯỜNG THỰC TẾ)
    if ($username == "yenngan" && $email == "yennganbui3@gmail.com" && $password == "123456") {
        // Đăng nhập thành công
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;
        header("Location: mainpage21.php"); // Chuyển hướng đến trang mainpage
        exit();
    } else {
        // Đăng nhập thất bại
        echo "Đăng nhập không thành công. Vui lòng kiểm tra lại thông tin.";
        // Hoặc bạn có thể chuyển hướng trở lại trang login21.html với thông báo lỗi
        // header("Location: login21.html?error=1");
        // exit();
    }
} else {
    // Nếu không phải là phương thức POST (ví dụ truy cập trực tiếp vào trang)
    header("Location: login21.html"); // Chuyển hướng về trang login
    exit();
}
?>