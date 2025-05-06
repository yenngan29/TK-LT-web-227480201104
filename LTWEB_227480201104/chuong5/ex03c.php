<?php
$i = 5;

echo "Giá trị ban đầu của i: " . $i . "<br>";

echo "i++ (post-increment): " . $i++ . "<br>"; // In ra 5, sau đó $i tăng lên 6
echo "Giá trị của i sau i++: " . $i . "<br>";

$j = 5;
echo "Giá trị ban đầu của j: " . $j . "<br>";

echo "++j (pre-increment): " . ++$j . "<br>"; // $j tăng lên 6, sau đó in ra 6
echo "Giá trị của j sau ++j: " . $j . "<br>";

$k = 5;
echo "Giá trị ban đầu của k: " . $k . "<br>";

echo "k-- (post-decrement): " . $k-- . "<br>"; // In ra 5, sau đó $k giảm xuống 4
echo "Giá trị của k sau k--: " . $k . "<br>";

$l = 5;
echo "Giá trị ban đầu của l: " . $l . "<br>";

echo "--l (pre-decrement): " . --$l . "<br>"; // $l giảm xuống 4, sau đó in ra 4
echo "Giá trị của l sau --l: " . $l . "<br>";
?>