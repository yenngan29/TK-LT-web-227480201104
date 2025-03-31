<!DOCTYPE html>
<html>
<head>
    <title>Tính USCLN và BSCNN</title>
    <style>
        form {
            width: 300px;
            padding: 20px;
            border: 2px solid black;
            border-radius: 10px;
        }
        input {
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h2>TÍNH USCLN VÀ BSCNN</h2>
    <form method="post">
        Số thứ 1: <input type="number" name="num1" required>
        Số thứ 2: <input type="number" name="num2" required>
        Kết quả:
        <input type="text" value="<?php echo isset($uscln) ? $uscln : ''; ?>" placeholder="USCLN" readonly>
        <input type="text" value="<?php echo isset($bscnn) ? $bscnn : ''; ?>" placeholder="BSCNN" readonly>
        <button type="submit" name="calculate">Tính</button>
    </form>

    <?php
    function uscln($a, $b) {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    function bscnn($a, $b) {
        return ($a * $b) / uscln($a, $b);
    }

    if (isset($_POST['calculate'])) {
        $num1 = abs((int)$_POST['num1']);
        $num2 = abs((int)$_POST['num2']);
        $uscln = uscln($num1, $num2);
        $bscnn = bscnn($num1, $num2);
        echo "<script>
                document.querySelectorAll('input')[2].value = '$uscln';
                document.querySelectorAll('input')[3].value = '$bscnn';
              </script>";
    }
    ?>
</body>
</html>