<?php
$chuoiCha = "Nhập môn Công nghệ Thông tin";
$chuoiConTimKiem = "Thông tin";

$viTri = strpos($chuoiCha, $chuoiConTimKiem);

echo "Chuỗi cha: " . $chuoiCha . "<br>";
echo "Chuỗi con cần tìm: " . $chuoiConTimKiem . "<br>";

if ($viTri !== false) {
    echo "Chuỗi con '" . $chuoiConTimKiem . "' được tìm thấy tại vị trí: " . $viTri;
} else {
    echo "Không tìm thấy chuỗi con '" . $chuoiConTimKiem . "' trong chuỗi cha.";
}
?>