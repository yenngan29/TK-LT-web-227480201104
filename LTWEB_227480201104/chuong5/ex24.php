<!DOCTYPE html>
 <html>
 <head>
     <title>Upload nhiều file</title>
 </head>
 <body>
 <h3>Upload nhiều file</h3>
 
<form action="upload_multi.php" method="POST" enctype="multipart/form-data">
  Chọn nhiều file ảnh để upload:
  <input type="file" name="files[]" multiple><br>
  <input type="submit" name="submit" value="Upload">
</form>

<?php
if (isset($_POST['submit'])) {
    $target_dir = "BoSuuTap/";

    foreach ($_FILES['files']['name'] as $key => $name) {
        $tmp_name = $_FILES['files']['tmp_name'][$key];
        $target_file = $target_dir . basename($name);

        if (move_uploaded_file($tmp_name, $target_file)) {
            echo "File " . htmlspecialchars($name) . " đã được upload thành công.<br>";
        } else {
            echo "Lỗi khi upload file: " . htmlspecialchars($name) . "<br>";
        }
    }
}
?>
 </body>
 </html>