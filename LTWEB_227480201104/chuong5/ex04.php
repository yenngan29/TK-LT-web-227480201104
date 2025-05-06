<?php
if (isset($_POST['so1']) && isset($_POST['so2'])) {
    $so1 = $_POST['so1'];
    $so2 = $_POST['so2'];

    if (is_numeric($so1) && is_numeric($so2)) {
        if (isset($_POST['cong'])) {
            $ketqua = $so1 + $so2;
        } elseif (isset($_POST['tru'])) {
            $ketqua = $so1 - $so2;
        } elseif (isset($_POST['nhan'])) {
            $ketqua = $so1 * $so2;
        } elseif (isset($_POST['chia'])) {
            if ($so2 != 0) {
                $ketqua = $so1 / $so2;
            } else {
                $ketqua = "Không thể chia cho 0";
            }
        } elseif (isset($_POST['mod'])) {
            if ($so2 != 0) {
                $ketqua = $so1 % $so2;
            } else {
                $ketqua = "Không thể chia cho 0";
            }
        }

        // Truyền kết quả trở lại form
        header("Location: tinhtoan.html?ketqua=" . urlencode($ketqua) . "&so1=" . urlencode($so1) . "&so2=" . urlencode($so2));
        exit();

    } else {
        $ketqua = "Vui lòng nhập số!";
        header("Location: tinhtoan.html?ketqua=" . urlencode($ketqua));
        exit();
    }
}
?>