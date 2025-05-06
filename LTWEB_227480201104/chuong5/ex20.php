<!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Đăng nhập</title>
     <style>
         * {
             margin: 0;
             padding: 0;
             box-sizing: border-box;
         }
 
         body {
             font-family: Arial, sans-serif;
             background: linear-gradient(to right, #4facfe, #00f2fe); /* Nền gradient */
             display: flex;
             justify-content: center;
             align-items: center;
             height: 100vh;
             text-align: center;
         }
 
         .login-box {
             background-color: #fff;
             padding: 40px;
             border-radius: 10px;
             box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
             width: 300px;
             text-align: center;
         }
 
         h2 {
             margin-bottom: 20px;
             font-size: 24px;
             color: #333;
         }
 
         input[type="email"],
         input[type="password"],
         input[type="text"] {
             width: 100%;
             padding: 10px;
             margin: 10px 0;
             border: 2px solid #ddd;
             border-radius: 5px;
             font-size: 16px;
             transition: border-color 0.3s ease;
         }
 
         input[type="email"]:focus,
         input[type="password"]:focus,
         input[type="text"]:focus {
             border-color: #4facfe;
         }
 
         button {
             background-color: #4facfe;
             color: white;
             padding: 10px;
             width: 100%;
             border: none;
             border-radius: 5px;
             cursor: pointer;
             font-size: 16px;
             transition: background-color 1s ease;
         }
 
         button:hover {
             background-color: gray;
         }
 
         .error-message {
             color: red;
             font-size: 14px;
             margin-top: 10px;
         }
 
         @media (max-width: 768px) {
             .login-box {
                 width: 80%;
                 padding: 30px;
             }
 
             h2 {
                 font-size: 22px;
             }
         }
 
     </style>
     <title>Document</title>
 </head>
 <body>
     <div class="login-box">
         <h2>Đăng nhập</h2>
         <form action="login.php" method="POST">
             <input type="text" name="username" placeholder="Username" required>
             <input type="email" name="email" placeholder="Email" required>
             <input type="password" name="password" placeholder="Password" required>
             <button type="submit">Đăng nhập</button>
         </form>
     <div class="khung">
         <div class="container">
             <form action="login.php" method="post">
                 <label for="">Email:</label>
                 <input type="email" name="email" id="" placeholder="email">
                 <br>
                 <label for="">password:</label>
                 <input type="password" name="password" id="" placeholder="password">
                 <br>
                 <input type="submit" value="Login" name="dangnhap">
             </form>
         </div>
     </div>
 </body>
 </html>
 </html>