<?php
include 'conf/config.php';
// Proses form lupa password
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = "Email harus diisi.";
    } else {
        // Cek email di database
        $cek = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE email='$email'");
        if (mysqli_num_rows($cek) == 1) {
            $user = mysqli_fetch_assoc($cek);
            // Generate token reset
            $token = bin2hex(random_bytes(16));
            // Simpan token ke database
            mysqli_query($koneksi, "UPDATE tb_users SET reset_token='$token' WHERE email='$email'");
            // Simulasi kirim link reset (tampilkan di halaman)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
            $success = "Link reset password: <a href='$reset_link'>$reset_link</a>";
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
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
            padding: 25px; /* Increased from 20px */
            text-align: center;
        }
        .login-box h1 {
            font-size: 28px; /* Increased from 24px */
            color: #333;
            margin-bottom: 15px; /* Increased from 10px */
        }
        .input-group {
            margin-bottom: 20px; /* Increased from 15px */
        }
        .input-group .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
            border-right: none;
            font-size: 1.2rem; /* Increased from 1.1rem */
            padding: 12px; /* Increased from 1rem */
        }
        .input-group-append .input-group-text {
            background-color: #fff;
            border-radius: 0 0.25rem 0.25rem 0;
            border-left: none;
            color: #007bff;
            padding: 10px 15px; /* Increased from 0.8rem 1.2rem */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            padding: 12px; /* Increased from 10px */
            font-size: 1.3rem; /* Increased from 16px */
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
            font-size: 1.1rem; /* Increased from implied default */
        }
        .links a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 0.25rem;
            padding: 12px; /* Increased from 10px */
            margin-bottom: 20px; /* Increased from 15px */
            text-align: left;
            font-size: 1.1rem; /* Increased from 1rem */
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px; /* Increased from 12px */
            color: #666;
        }
        @media (max-width: 576px) {
            .login-box {
                padding: 20px;
            }
            .login-box h1 {
                font-size: 24px;
            }
            .input-group .form-control {
                font-size: 1.1rem;
                padding: 10px;
            }
            .input-group-append .input-group-text {
                padding: 8px 12px;
            }
            .btn-primary {
                font-size: 1.2rem;
                padding: 10px;
            }
            .alert {
                font-size: 1rem;
                padding: 10px;
            }
            .links a {
                font-size: 1rem;
            }
            .footer {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1><b>Lupa</b> Password</h1>
        <div class="card-body login-card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="links"><a href="index.php">Login</a></div>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="submit" class="btn btn-primary btn-block">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="footer">
            Today: 01:03 PM WIB, Saturday, June 14, 2025
        </div>
    </div>
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>