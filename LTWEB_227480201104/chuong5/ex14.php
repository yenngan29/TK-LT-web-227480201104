<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Ma trận</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; text-align: center; padding: 20px; }
        .container { width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; }
        textarea, input[type="hidden"] { width: 90%; margin: 10px 0; padding: 10px; }
        button { padding: 10px 20px; margin: 5px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .result { margin-top: 20px; padding: 10px; border-radius: 5px; background-color: #e7f4e4; border: 1px solid #c6e0c0; color: #3c763d; }
        table { margin: 10px auto; border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: #fff; }
    </style>
</head>
<body>

<div class="container">
    <h2>Quản lý Ma trận số thực</h2>
    <form method="post">
        <label for="matrix">Nhập ma trận (các số cách nhau bằng dấu cách, mỗi hàng cách nhau bằng Enter):</label><br>
        <textarea id="matrix" name="matrix"><?php echo isset($_POST['matrix']) ? htmlspecialchars($_POST['matrix']) : ''; ?></textarea><br>
        <input type="hidden" name="saved_matrix" value="<?php echo isset($_POST['matrix']) ? htmlspecialchars($_POST['matrix']) : ''; ?>">
        <button type="submit" name="action" value="max">Tìm số lớn nhất</button>
        <button type="submit" name="action" value="min">Tìm số nhỏ nhất</button>
        <button type="submit" name="action" value="sum">Tính tổng</button>
        <button type="submit" name="action" value="display">In ma trận</button>
    </form>

    <?php
    // Hàm xử lý ma trận
    function parseMatrix($input) {
        $rows = explode("\n", trim($input));
        $matrix = [];
        foreach ($rows as $row) {
            $matrix[] = array_map('floatval', explode(' ', trim($row)));
        }
        return $matrix;
    }

    function findMax($matrix) {
        $max = PHP_FLOAT_MIN;
        foreach ($matrix as $row) {
            $max = max($max, max($row));
        }
        return $max;
    }

    function findMin($matrix) {
        $min = PHP_FLOAT_MAX;
        foreach ($matrix as $row) {
            $min = min($min, min($row));
        }
        return $min;
    }

    function calculateSum($matrix) {
        $sum = 0;
        foreach ($matrix as $row) {
            $sum += array_sum($row);
        }
        return $sum;
    }

    function displayMatrix($matrix) {
        echo "<div class='result'><strong>Ma trận:</strong><br>";
        echo "<table>";
        foreach ($matrix as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    // Xử lý yêu cầu từ người dùng
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $matrixInput = isset($_POST['saved_matrix']) ? $_POST['saved_matrix'] : '';
        $matrix = parseMatrix($matrixInput);

        switch ($_POST["action"]) {
            case "max":
                echo "<div class='result'>Số lớn nhất trong ma trận là: " . findMax($matrix) . "</div>";
                break;
            case "min":
                echo "<div class='result'>Số nhỏ nhất trong ma trận là: " . findMin($matrix) . "</div>";
                break;
            case "sum":
                echo "<div class='result'>Tổng các phần tử trong ma trận là: " . calculateSum($matrix) . "</div>";
                break;
            case "display":
                displayMatrix($matrix);
                break;
            default:
                echo "<div class='result'>Lựa chọn không hợp lệ!</div>";
        }
    }
    ?>
</div>

</body>
</html>