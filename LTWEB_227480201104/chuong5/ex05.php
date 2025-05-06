<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tính USCLN và BSCNN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #80deea);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            color: #00796b;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #00796b;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #004d40;
        }

        .result {
            margin-top: 20px;
            background-color: #e0f2f1;
            padding: 15px;
            border-left: 5px solid #00796b;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tính USCLN và BSCNN</h1>
        <form method="post" action="">
            <label for="num1">Số thứ nhất:</label>
            <input type="number" name="num1" id="num1" required>

            <label for="num2">Số thứ hai:</label>
            <input type="number" name="num2" id="num2" required>

            <input type="submit" name="submit" value="Tính USCLN và BSCNN">
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $num1 = $_POST['num1'];
            $num2 = $_POST['num2'];

            function uscln($a, $b) {
                return ($b == 0) ? $a : uscln($b, $a % $b);
            }

            function bscnn($a, $b) {
                return ($a * $b) / uscln($a, $b);
            }

            $uscln = uscln($num1, $num2);
            $bscnn = bscnn($num1, $num2);

            echo "<div class='result'>";
            echo "<strong>Kết quả:</strong><br>";
            echo "USCLN: " . $uscln . "<br>";
            echo "BSCNN: " . $bscnn;
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
