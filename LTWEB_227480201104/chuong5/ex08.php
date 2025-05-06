<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách năm</title>
</head>
<body>

<form>
    <label for="year">Chọn năm:</label>
    <select id="year" name="year">
        <?php
        $currentYear = date("Y"); // Lấy năm hiện tại
        for ($year = 1900; $year <= $currentYear; $year++) {
            echo "<option value='$year'>$year</option>";
        }
        ?>
    </select>
</form>

</body>
</html>