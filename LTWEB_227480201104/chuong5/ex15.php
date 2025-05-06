<?php
$chuoi = "Hello World";
$kyTuDem = array();

$chuoi = strtolower($chuoi);
for ($i = 0; $i < strlen($chuoi); $i++) {
    $kyTu = $chuoi[$i];
    if (isset($kyTuDem[$kyTu])) {
        $kyTuDem[$kyTu]++;
    } else {
        $kyTuDem[$kyTu] = 1;
    }
}
echo "Chuỗi đã nhập: " . $chuoi . "<br>";
echo "Số lần xuất hiện của từng ký tự:<br>";
foreach ($kyTuDem as $kyTu => $soLan) {
    echo "'$kyTu': " . $soLan . " lần<br>";
}
?>