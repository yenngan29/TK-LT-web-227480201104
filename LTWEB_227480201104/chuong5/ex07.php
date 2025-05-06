<!DOCTYPE html>
<html>
<head>
    <title>Hiển thị dãy số</title>
</head>
<body>
    <h1>Dãy số từ 1 đến 100</h1>
    <?php
    for ($i = 1; $i <= 100; $i++) {
        if ($i % 2 == 0) {
            echo "<b>" . $i . "</b> ";
        } else {
            echo $i . " ";
        }
    }
    ?>
</body>
</html>