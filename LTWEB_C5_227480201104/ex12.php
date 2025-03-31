<!DOCTYPE html>
<html>
<head>
    <title>Bàn Cờ Vua</title>
    <style>
        .chessboard {
            width: 320px;
            height: 320px;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(8, 1fr);
            border: 2px solid black;
        }
        .black {
            background-color: black;
        }
        .white {
            background-color: white;
        }
        .square {
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body>
    <h2>Bàn Cờ Vua</h2>
    <div class="chessboard">
        <?php
        for ($row = 0; $row < 8; $row++) {
            for ($col = 0; $col < 8; $col++) {
                $color = ($row + $col) % 2 == 0 ? 'white' : 'black';
                echo "<div class='square $color'></div>";
            }
        }
        ?>
    </div>
</body>
</html>