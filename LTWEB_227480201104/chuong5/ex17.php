<?php
// Ví dụ 1
$chuoi1 = "Welcom to DCB.";
$kyTuTach1 = "o";
$mangKetQua1 = explode($kyTuTach1, $chuoi1);

echo "Chuỗi 1: " . $chuoi1 . "<br>";
echo "Ký tự tách: '" . $kyTuTach1 . "'<br>";
echo "Kết quả tách 1: " . implode(", ", $mangKetQua1) . "<br><br>";

// Ví dụ 2
$chuoi2 = "Nguyen Van An";
$kyTuTach2 = " ";
$mangKetQua2 = explode($kyTuTach2, $chuoi2);

echo "Chuỗi 2: " . $chuoi2 . "<br>";
echo "Ký tự tách: '" . $kyTuTach2 . "'<br>";
echo "Kết quả tách 2: " . implode(", ", $mangKetQua2);
?>