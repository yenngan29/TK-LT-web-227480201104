<?php
// Danh sách các số
$danhSachSo = [15, 7, 22, 9, 31, 12, 5, 18];

function timSoLonNhat($arr) {
    return max($arr);
}

function timSoNhoNhat($arr) {
    return min($arr);
}

function tinhTong($arr) {
    return array_sum($arr);
}

function tinhTrungBinhCong($arr) {
    $tong = array_sum($arr);
    $soLuong = count($arr);
    if ($soLuong > 0) {
        return $tong / $soLuong;
    } else {
        return 0;
    }
}

function inSoChan($arr) {
    $soChan = [];
    foreach ($arr as $so) {
        if ($so % 2 == 0) {
            $soChan[] = $so;
        }
    }
    return $soChan;
}

function sapXepTangDan($arr) {
    sort($arr);
    return $arr;
}

echo "Danh sách số: " . implode(", ", $danhSachSo) . "<br><br>";

// a) Tìm số lớn nhất
$soLonNhat = timSoLonNhat($danhSachSo);
echo "Số lớn nhất trong danh sách là: " . $soLonNhat . "<br>";

// b) Tìm số nhỏ nhất
$soNhoNhat = timSoNhoNhat($danhSachSo);
echo "Số nhỏ nhất trong danh sách là: " . $soNhoNhat . "<br>";

// c) Tính tổng
$tong = tinhTong($danhSachSo);
echo "Tổng các số trong danh sách là: " . $tong . "<br>";

// d) Tính trung bình cộng
$trungBinhCong = tinhTrungBinhCong($danhSachSo);
echo "Trung bình cộng các số trong danh sách là: " . $trungBinhCong . "<br>";

// e) In ra màn hình các số chẵn
$cacSoChan = inSoChan($danhSachSo);
echo "Các số chẵn trong danh sách là: " . implode(", ", $cacSoChan) . "<br>";

// f) Sắp xếp tăng dần
$danhSachDaSapXep = sapXepTangDan($danhSachSo);
echo "Danh sách sau khi sắp xếp tăng dần là: " . implode(", ", $danhSachDaSapXep) . "<br>";
?>