<?php
session_start();
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy bỏ session
header("Location: login21.html"); // Chuyển hướng về trang đăng nhập
exit();
?>