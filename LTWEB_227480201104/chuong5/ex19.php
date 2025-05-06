<?php
    echo "Chào bạn, <br>";

    // Kiểm tra xem cookie 'thoiGianTruyCap' đã tồn tại chưa
    if (isset($_COOKIE['thoiGianTruyCap'])) {
        echo "Thời gian truy cập gần đây nhất là: " . date('d/m/Y H:i:s', $_COOKIE['thoiGianTruyCap']) . "<br>";
    }

    // Cập nhật lại cookie với thời gian hiện tại, thời gian sống 600 giây
    setcookie('thoiGianTruyCap', time(), time() + 600);
?>