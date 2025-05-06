<!DOCTYPE html>
<html>
<head>
    <title>Hiển thị thông tin bộ nhớ</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Thông tin bộ nhớ và giá</h1>
    <table>
        <thead>
            <tr>
                <th>Bộ nhớ (GB)</th>
                <th>Giá (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = array(
                0 => 80000,
                1 => 95000,
                2 => 110000,
                3 => 140000,
                4 => 165000,
                5 => 200000,
                6 => 235000,
                50 => 2500000
            );

            foreach ($data as $boNho => $gia) {
                echo "<tr>";
                echo "<td>" . $boNho . "</td>";
                echo "<td>" . number_format($gia) . "</td>"; // Định dạng số tiền
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>