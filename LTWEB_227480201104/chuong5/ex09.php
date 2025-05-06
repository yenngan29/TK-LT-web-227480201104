<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng Cửu Chương</title>
    <style>
        body {
            font-family: monospace;
        }
        .table-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .row {
            display: flex;
            gap: 20px;
        }
        .table {
            width: 180px;
        }
        pre {
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>Bảng Cửu Chương</h2>
<div class="table-container">
    <?php
    for ($row = 0; $row < 2; $row++) {
        echo "<div class='row'>";
        for ($col = 1; $col <= 5; $col++) {
            $num = $row * 5 + $col; // Xác định bảng cần in
            if ($num > 10) break; // Dừng lại nếu vượt quá 10
            echo "<div class='table'><pre>";
            for ($j = 1; $j <= 10; $j++) {
                printf("%2d x %2d = %3d\n", $num, $j, $num * $j);
            }
            echo "</pre></div>";
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>