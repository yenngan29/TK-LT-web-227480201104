<!DOCTYPE html>
<html>
<head>
    <title> Mảng</title>
</head>
<body>
    <form method="post">
        Nhập mảng các số nguyên (cách nhau bằng dấu phẩy): <br>
        <input type="text" name="numbers" size="50" value="<?php echo isset($_POST['numbers']) ? $_POST['numbers'] : '' ?>">
        <br><br>
        <input type="submit" value="Xử lý">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $input = $_POST["numbers"];
        $arr = explode(",", $input);
        $arr = array_map("trim", $arr); 
        $arr = array_map("intval", $arr); 
        echo "<h3>Kết quả xử lý:</h3>";

        
        echo "a. Mảng đã nhập: " . implode(", ", $arr) . "<br>";

       
        $oddCount = 0;
        foreach ($arr as $num) {
            if ($num % 2 != 0) $oddCount++;
        }
        echo "b. Số lượng số lẻ: $oddCount<br>";

       
        $evenCount = 0;
        foreach ($arr as $num) {
            if ($num % 2 == 0) $evenCount++;
        }
        echo "c. Số lượng số chẵn: $evenCount<br>";

       
        $oddSum = 0;
        foreach ($arr as $num) {
            if ($num % 2 != 0) $oddSum += $num;
        }
        echo "d. Tổng các số lẻ: $oddSum<br>";

       
        $max = max($arr);
        $min = min($arr);
        echo "e. Số lớn nhất: $max<br>";
        echo "   Số nhỏ nhất: $min<br>";

        $reversed = array_reverse($arr);
        echo "f. Mảng sau khi đảo ngược: " . implode(", ", $reversed) . "<br>";
    }
    ?>
</body>
</html>
