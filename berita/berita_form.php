<?php
include_once 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'wartawan') {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch categories
$kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori ORDER BY nama_kategori");

// Edit news if ID is provided
$judul = $isi = $id_kategori = $gambar = '';
$edit = false;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $q = mysqli_query($koneksi, "SELECT * FROM berita WHERE id='$id' AND id_pengirim='$user_id'");
    if ($data = mysqli_fetch_assoc($q)) {
        $judul = $data['judul'];
        $isi = $data['isi'];
        $id_kategori = $data['id_kategori'];
        $gambar = $data['gambar'];
        $edit = true;
    }
}

// Process form submission
if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $id_kategori = intval($_POST['id_kategori']);
    $gambar_name = $gambar;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'upload/' . $gambar_name);
    }
    if ($edit) {
        $sql = "UPDATE berita SET judul='$judul', isi='$isi', id_kategori='$id_kategori', gambar='$gambar_name' WHERE id='$id' AND id_pengirim='$user_id'";
    } else {
        $sql = "INSERT INTO berita (judul, isi, id_kategori, gambar, id_pengirim, status) VALUES ('$judul', '$isi', '$id_kategori', '$gambar_name', '$user_id', 'draft')";
    }
    if (mysqli_query($koneksi, $sql)) {
        header('Location: berita_list.php');
        exit;
    } else {
        $error = 'Gagal menyimpan berita.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit ? 'Edit' : 'Tambah' ?> Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #FFA500; /* Orange */
            --secondary: #FF8C00; /* Darker Orange */
            --accent: #00C4CC; /* Cyan Blue */
            --bg-gradient: linear-gradient(135deg, #1A2E44 0%, #1E4060 100%); /* Blue-based gradient */
            --card-bg: rgba(26, 46, 60, 0.95); /* Darker blue tint */
            --shadow: 0 10px 30px rgba(0, 196, 204, 0.15); /* Blue shadow */
            --text-light: #E6F0FA; /* Light blue-white */
            --text-muted: #A0B8C8; /* Muted blue */
            --glow: 0 0 15px rgba(0, 196, 204, 0.5); /* Cyan glow */
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--text-light);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                             linear-gradient(45deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                             linear-gradient(135deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                             linear-gradient(45deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: gradientShift 10s infinite alternate;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 0%, 0% 0%, 0% 0%, 0% 0%; }
            100% { background-position: 100% 100%, 100% 100%, 100% 100%, 100% 100%; }
        }

        .container-wrapper {
            height: 100vh;
            padding: 1rem; /* Reduced padding */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .news-card {
            background: var(--card-bg);
            border-radius: 2rem;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 900px;
            height: auto; /* Changed from 100% to auto */
            max-height: 100vh; /* Ensure it fits within viewport */
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 165, 0, 0.2);
            padding: 1.5rem; /* Reduced from 2rem */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-image: linear-gradient(to right, var(--primary), var(--accent)) 1;
            animation: cardGlow 4s infinite alternate;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-y: auto; /* Scroll only if content exceeds */
        }

        @keyframes cardGlow {
            0% { box-shadow: var(--shadow), var(--glow); }
            100% { box-shadow: var(--shadow), 0 0 25px rgba(0, 196, 204, 0.7); }
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 50px rgba(0, 196, 204, 0.25);
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-top-left-radius: 1.8rem;
            border-top-right-radius: 1.8rem;
            padding: 1rem; /* Reduced from 1.5rem */
            margin: -1.5rem -1.5rem 1.5rem -1.5rem; /* Adjusted margins */
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .card-header h3 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 2rem; /* Reduced from 2.5rem */
            font-weight: 700;
            color: var(--text-light);
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .badge-status {
            background: rgba(0, 196, 204, 0.2);
            color: var(--accent);
            font-weight: 600;
            padding: 0.5rem 1rem; /* Reduced from 0.7rem 1.5rem */
            border-radius: 1.2rem;
            font-size: 1rem; /* Reduced from 1.1rem */
            box-shadow: 0 2px 8px rgba(0, 196, 204, 0.3);
            transition: transform 0.2s ease;
        }

        .badge-status:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 0;
            max-width: 600px; /* Reduced from 700px */
            width: 100%;
        }

        .form-group {
            margin-bottom: 1rem; /* Reduced from 1.5rem */
        }

        .form-group label {
            font-weight: 600;
            font-size: 1.1rem; /* Reduced from 1.3rem */
            color: var(--primary);
            margin-bottom: 0.3rem; /* Reduced from 0.5rem */
            display: flex;
            align-items: center;
            gap: 0.3rem; /* Reduced from 0.5rem */
        }

        .form-control, .custom-select {
            border: 2px solid var(--accent);
            border-radius: 1rem; /* Reduced from 1.1rem */
            padding: 0.8rem 1.2rem; /* Reduced from 1rem 1.5rem */
            font-size: 1rem; /* Reduced from 1.25rem */
            background: rgba(44, 70, 80, 0.95);
            color: var(--text-light);
            font-weight: 500;
            box-shadow: 0 2px 12px rgba(0, 196, 204, 0.08);
            transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
        }

        .form-control:focus, .custom-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.25), 0 0 10px var(--accent);
            outline: none;
            background: rgba(44, 70, 80, 1);
        }

        select.form-control, select.custom-select, select.form-control option, select.custom-select option {
            color: var(--text-light) !important;
            background: rgba(30, 64, 96, 0.95) !important;
        }

        select.form-control:focus, select.custom-select:focus, select.form-control option:checked, select.custom-select option:checked {
            background: var(--secondary) !important;
            color: var(--text-light) !important;
            font-weight: 700;
        }

        textarea.form-control {
            min-height: 150px; /* Reduced from 180px */
            resize: vertical;
            line-height: 1.4; /* Reduced from 1.6 */
        }

        .custom-file-input {
            border-radius: 1rem;
            cursor: pointer;
        }

        .custom-file-label {
            border-radius: 1rem;
            background: rgba(255, 165, 0, 0.1);
            border: 2px solid var(--accent);
            padding: 0.8rem 1.2rem; /* Reduced from 1rem 1.5rem */
            font-size: 1rem; /* Reduced from 1.2rem */
            color: var(--text-muted);
        }

        .custom-file-label::after {
            background: var(--primary);
            color: var(--text-light);
            border-radius: 0 1rem 1rem 0;
            padding: 0.8rem 1.2rem; /* Reduced from 1rem 1.5rem */
        }

        .img-preview {
            max-width: 180px; /* Reduced from 200px */
            border-radius: 1rem; /* Reduced from 1.2rem */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Reduced shadow */
            margin-bottom: 0.8rem; /* Reduced from 1rem */
            transition: transform 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.07);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 1rem;
            padding: 0.8rem 2rem; /* Reduced from 1rem 2.5rem */
            font-weight: 600;
            color: var(--text-light);
            font-size: 1.1rem; /* Reduced from 1.3rem */
            transition: background 0.3s ease, transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::before {
            width: 300%;
            height: 300%;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: linear-gradient(90deg, #FF9A8B, var(--secondary));
            border: none;
            border-radius: 1rem;
            padding: 0.8rem 2rem; /* Reduced from 1rem 2.5rem */
            font-weight: 600;
            color: var(--text-light);
            font-size: 1.1rem; /* Reduced from 1.3rem */
            position: relative;
            overflow: hidden;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-secondary:hover::before {
            width: 300%;
            height: 300%;
        }

        .btn-secondary:hover {
            background: linear-gradient(90deg, var(--secondary), #FF9A8B);
            transform: translateY(-3px);
        }

        .alert {
            border-radius: 1rem;
            padding: 1rem; /* Reduced from 1.2rem */
            font-size: 1rem; /* Reduced from 1.1rem */
            display: flex;
            align-items: center;
            gap: 0.5rem; /* Reduced from 0.7rem */
            margin-bottom: 1rem; /* Reduced from 1.5rem */
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.2);
        }

        .form-text {
            font-size: 0.9rem; /* Reduced from 1rem */
            color: var(--text-muted);
            margin-top: 0.3rem; /* Reduced from 0.5rem */
        }

        hr {
            border-top: 2px dashed var(--secondary); /* Reduced from 3px */
            margin: 1.5rem 0; /* Reduced from 2rem */
        }

        .is-invalid {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(0, 196, 204, 0.2) !important; /* Reduced from 4px */
        }

        @media (max-width: 768px) {
            .news-card {
                padding: 1rem;
            }

            .card-header h3 {
                font-size: 1.5rem; /* Reduced from 2rem */
            }

            .badge-status {
                font-size: 0.9rem; /* Reduced from 1rem */
                padding: 0.4rem 0.8rem; /* Reduced from 0.6rem 1.2rem */
            }

            .form-group label {
                font-size: 1rem; /* Reduced from 1.1rem */
            }

            .form-control, .custom-select, .custom-file-label {
                font-size: 0.9rem; /* Reduced from 1rem */
                padding: 0.6rem 1rem; /* Reduced from 0.8rem 1.2rem */
            }

            .btn-primary, .btn-secondary {
                font-size: 1rem; /* Reduced from 1.1rem */
                padding: 0.6rem 1.5rem; /* Reduced from 0.8rem 2rem */
            }

            .img-preview {
                max-width: 120px; /* Reduced from 150px */
            }

            .card-body {
                max-width: 500px; /* Reduced from 600px */
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <div class="news-card">
        <div class="card-header">
            <h3><i class="fas fa-edit mr-2"></i><?= $edit ? 'Edit' : 'Tambah' ?> Berita</h3>
            <span class="badge-status"><?= $edit ? 'Edit Mode' : 'Tambah Baru' ?></span>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Judul Berita</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($judul) ?>" required placeholder="Masukkan judul berita">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Kategori</label>
                    <select name="id_kategori" id="id_kategori" class="form-control custom-select" required>
                        <option value="" disabled hidden <?= $id_kategori == '' ? 'selected' : '' ?>>Pilih Kategori</option>
                        <?php
                        if ($kategori instanceof mysqli_result && $kategori->num_rows > 0) mysqli_data_seek($kategori, 0);
                        while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_kategori == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nama_kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div id="kategoriError" class="form-text text-danger" style="display:none;"><i class="fas fa-exclamation-triangle"></i> Silakan pilih kategori!</div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Isi Berita</label>
                    <textarea name="isi" class="form-control" rows="6" required placeholder="Tulis isi berita di sini..."><?= htmlspecialchars($isi) ?></textarea>
                    <small class="form-text"><i class="fas fa-info-circle mr-1"></i>Gunakan bahasa yang jelas dan informatif.</small>
                </div>
                <hr>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <?php if ($gambar): ?>
                        <div>
                            <img src="upload/<?= htmlspecialchars($gambar) ?>" class="img-preview">
                        </div>
                    <?php endif; ?>
                    <div class="custom-file">
                        <input type="file" name="gambar" class="custom-file-input" id="gambarInput">
                        <label class="custom-file-label" for="gambarInput">Pilih file gambar...</label>
                    </div>
                    <small class="form-text"><i class="fas fa-info-circle mr-1"></i>Format: jpg, png, max 2MB.</small>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-2"> <!-- Reduced gap and margin-top -->
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
                    <a href="berita_list.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
$(function () {
    bsCustomFileInput.init();

    // Validasi kategori sebelum submit
    $('form').on('submit', function(e) {
        var kategori = $('#id_kategori').val();
        if (!kategori) {
            $('#kategoriError').show();
            $('#id_kategori').addClass('is-invalid');
            $('#id_kategori').focus();
            e.preventDefault();
            return false;
        } else {
            $('#kategoriError').hide();
            $('#id_kategori').removeClass('is-invalid');
        }
    });
    $('#id_kategori').on('change', function() {
        if ($(this).val()) {
            $('#kategoriError').hide();
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
</body>
</html>