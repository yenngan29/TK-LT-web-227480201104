<!DOCTYPE html>
 <html>
 <head>
     <title>Upload 1 file</title>
 </head>
 <body>
 <h3>Upload một file</h3>
 <<form action="upload_file.php" method="POST" enctype="multipart/form-data">
  Chọn file để upload:
  <input type="file" name="myfile"><br>
  <input type="submit" name="submit" value="Upload">
</form>
<?php
if (isset($_POST['submit'])) {
    $target_dir = "tailieu/";
    $target_file = $target_dir . basename($_FILES["myfile"]["name"]);

    // Kiểm tra và upload
    if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $target_file)) {
        echo "File " . htmlspecialchars(basename($_FILES["myfile"]["name"])) . " đã được upload thành công.";
    } else {
        echo "Đã xảy ra lỗi khi upload file.";
    }
}
?>
 </body>
 </html>