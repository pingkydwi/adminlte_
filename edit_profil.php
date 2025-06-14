<?php
include 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);
// Proses update profil
if (isset($_POST['update'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi = trim($_POST['konfirmasi']);
    $update_query = "UPDATE tb_users SET nama_lengkap='$nama_lengkap', email='$email'";
    if (!empty($password_baru)) {
        if ($password_baru !== $konfirmasi) {
            $error = "Konfirmasi password tidak cocok.";
        } else {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $update_query .= ", password='$password_hash'";
        }
    }
    $update_query .= " WHERE id='$user_id'";
    if (!isset($error)) {
        $simpan = mysqli_query($koneksi, $update_query);
        if ($simpan) {
            $success = "Profil berhasil diperbarui.";
            // Refresh data user
            $query = mysqli_query($koneksi, "SELECT * FROM tb_users WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($query);
        } else {
            $error = "Gagal memperbarui profil.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
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
            padding: 25px;
            text-align: center;
        }
        .login-box h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
            border-right: none;
            font-size: 1.2rem;
            padding: 12px;
        }
        .input-group-append .input-group-text {
            background-color: #fff;
            border-radius: 0 0.25rem 0.25rem 0;
            border-left: none;
            color: #007bff;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            padding: 12px;
            font-size: 1.3rem;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            border-radius: 0.25rem;
            padding: 12px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 1.1rem;
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
            font-size: 14px;
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
            .footer {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1><b>Edit</b> Profil</h1>
        <div class="card-body login-card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password_baru" class="form-control" placeholder="Password Baru (opsional)">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="konfirmasi" class="form-control" placeholder="Konfirmasi Password Baru">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="update" class="btn btn-primary btn-block">Update Profil</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="footer">
            Today: 01:06 PM WIB, Saturday, June 14, 2025
        </div>
    </div>
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>