<!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Bài 11</title>
     <style>
         .color-box {
             display: inline-block;
             width: 50px;
             height: 50px;
             margin-right: 10px;
             border: 1px solid #ccc;
         }
         .color-item {
             margin-bottom: 10px;
         }
     </style>
 </head>
 <body>
     <form action="" method="post">
         <label for="mau">Nhập mảng màu: <input type="text" id="mau" name="mau"></label>
         <button type="submit">Hiển thị màu</button>
 
         <?php
             if (isset($_POST["mau"])) {
                 $chuoiMau = $_POST["mau"];
                 $mangMau = explode(",", $chuoiMau);
 
                 echo "<h2>Danh sách các màu:</h2>";
                 if (!empty($mangMau)) {
                     echo "<ul>";
                     foreach ($mangMau as $tenMau) {
                         $trimmedTenMau = trim($tenMau);
                         echo "<li class='color-item'>";
                         echo "<span class='color-box' style='background-color: " . htmlspecialchars($trimmedTenMau) . ";'></span> ";
                         echo htmlspecialchars($trimmedTenMau);
                         echo "</li>";
                     }
                     echo "</ul>";
                 } else {
                     echo "<p>Vui lòng nhập danh sách màu.</p>";
                 }
             }
         ?>
     </form>
     
 </body>
 </html>