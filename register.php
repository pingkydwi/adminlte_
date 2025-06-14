<?php
include 'conf/config.php';
// Add login logic here if needed
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background-color: #ffffff;
            border: 1px solid #007bff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }
        .login-box h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        .login-box p {
            color: #666;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
            border-right: none;
        }
        .input-group-append .input-group-text {
            background-color: #fff;
            border-radius: 0 0.25rem 0.25rem 0;
            border-left: none;
            color: #007bff;
        }
        .form-check {
            text-align: left;
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-social {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 0.25rem;
        }
        .btn-google {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-google:hover {
            background-color: #c82333;
        }
        .btn-github {
            background-color: #333;
            color: #fff;
        }
        .btn-github:hover {
            background-color: #1a1a1a;
        }
        .btn-microsoft {
            background-color: #007bff;
            color: #fff;
        }
        .btn-microsoft:hover {
            background-color: #0056b3;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>AdminLTE</h1>
        <p>Sign in to start your session</p>
        <form action="" method="post">
            <div class="input-group mb-3">
                <input type="text" name="username" class="form-control" placeholder="admin" required>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                </div>
            </div>
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <label class="form-check-label">Remember Me</label>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Log In</button>
        </form>
        <div class="mt-3">
            <button class="btn btn-social btn-google">G Login dengan Google</button>
            <button class="btn btn-social btn-github">G Login dengan Github</button>
            <button class="btn btn-social btn-microsoft">Login dengan Microsoft</button>
        </div>
        <div class="links mt-3">
            <a href="register.php">Daftar akun baru</a> | 
            <a href="#">Lupa Password?</a> | 
            <a href="#">Edit Profil</a>
        </div>
        <div class="footer">
            Today: 01:00 PM WIB, Saturday, June 14, 2025
        </div>
    </div>
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>