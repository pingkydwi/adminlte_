<?php
include 'conf/config.php';
session_start();

// Fetch categories for filter
$kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori ORDER BY nama_kategori");

// Handle search
$search_results = [];
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$search_performed = ($search_keyword !== '' || $search_kategori > 0);
if ($search_performed) {
    $where = [];
    if ($search_keyword !== '') {
        $escaped = mysqli_real_escape_string($koneksi, $search_keyword);
        $where[] = "(b.judul LIKE '%$escaped%' OR b.isi LIKE '%$escaped%')";
    }
    if ($search_kategori > 0) {
        $where[] = "b.id_kategori = $search_kategori";
    }
    $where[] = "b.status = 'publish'";
    $where_sql = implode(' AND ', $where);
    $q_search = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id WHERE $where_sql ORDER BY b.created_at DESC LIMIT 12");
    while ($row = mysqli_fetch_assoc($q_search)) {
        $search_results[] = $row;
    }
}

if (!isset($_GET['id'])) {
    header('Location: berita_list.php');
    exit;
}
$id = intval($_GET['id']);
$sql_detail = "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id WHERE b.id='$id'";
$q = mysqli_query($koneksi, $sql_detail);
if (!$q) {
    echo '<div class="alert alert-danger">Query error: ' . mysqli_error($koneksi) . '</div>';
    echo '<pre>' . htmlspecialchars($sql_detail) . '</pre>';
    exit;
}
if (!$data = mysqli_fetch_assoc($q)) {
    // Debug: cek apakah data berita dengan id ini ada
    $cek = mysqli_query($koneksi, "SELECT * FROM berita WHERE id='$id'");
    if (mysqli_num_rows($cek) == 0) {
        echo '<div class="alert alert-danger">Data berita dengan id = ' . $id . ' tidak ada di tabel berita.</div>';
    } else {
        echo '<div class="alert alert-danger">Data berita ditemukan, tapi join kategori/user gagal. Cek data kategori dan user terkait.</div>';
    }
    echo '<pre>' . htmlspecialchars($sql_detail) . '</pre>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #2E3192;
            --secondary: #00C4CC;
            --accent: #FF6B6B;
            --bg-gradient: linear-gradient(135deg, #0F1C3E 0%, #1A2A6C 100%);
            --card-bg: rgba(15, 28, 62, 0.85);
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            --text-light: #F5F7FA;
            --text-muted: #B0C4DE;
            --border-glow: 0 0 8px rgba(0, 196, 204, 0.3);
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--text-light);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container-wrapper {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: flex;
            gap: 2rem;
        }

        .sidebar {
            flex: 0 0 300px;
            position: sticky;
            top: 2rem;
            align-self: flex-start;
        }

        .main-content {
            flex: 1;
        }

        .search-form {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .search-form .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.95rem;
        }
        .search-form select.form-control, .search-form select.form-control option {
            color: #fff !important;
            background: rgba(15, 28, 62, 0.95) !important;
        }
        .search-form select.form-control:focus, .search-form select.form-control option:checked {
            background: var(--secondary) !important;
            color: #fff !important;
        }

        .search-form .form-control:focus {
            border-color: var(--secondary);
            box-shadow: var(--border-glow);
        }

        .search-form .btn-primary {
            background: var(--secondary);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-form .btn-primary:hover {
            background: var(--accent);
        }

        .glass-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .card-header h2 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .meta-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            font-size: 0.9rem;
            color: var(--text-muted);
            flex: 1 1 150px;
        }

        .meta-item b {
            color: var(--secondary);
            font-weight: 500;
        }

        .img-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            margin: 1.5rem auto;
            display: block;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .content {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--text-light);
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .btn-back {
            background: var(--accent);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: var(--text-light);
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--secondary);
        }

        .search-results h4 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.4rem;
            color: var(--secondary);
            margin: 2rem 0 1.5rem;
        }

        .search-results .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .search-results .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .search-results .card-img-top {
            height: 150px;
            object-fit: cover;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .search-results .card-title a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .search-results .card-title a:hover {
            color: var(--accent);
        }

        .search-results .btn-outline-primary {
            border-color: var(--secondary);
            color: var(--secondary);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .search-results .btn-outline-primary:hover {
            background: var(--secondary);
            color: var(--text-light);
        }

        @media (max-width: 992px) {
            .container-wrapper {
                flex-direction: column;
            }

            .sidebar {
                flex: 1;
                position: static;
            }

            .main-content {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .container-wrapper {
                padding: 1rem;
            }

            .glass-card {
                padding: 1.25rem;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }

            .meta-container {
                flex-direction: column;
            }

            .img-preview {
                max-height: 300px;
            }

            .content {
                font-size: 1rem;
                padding: 1rem;
            }

            .search-results .card-img-top {
                height: 120px;
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <!-- Sidebar with Search Form -->
    <aside class="sidebar">
        <form class="search-form" method="get" action="berita_detail.php">
            <div class="form-group mb-3">
                <label for="search" class="font-weight-bold" style="color: var(--text-light);">Cari Berita</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Kata kunci..." value="<?= htmlspecialchars($search_keyword) ?>">
            </div>
            <div class="form-group mb-3">
                <label for="kategori" class="font-weight-bold" style="color: var(--text-light);">Kategori</label>
                <select class="form-control" id="kategori" name="kategori">
                    <option value="0">Semua Kategori</option>
                    <?php
                    mysqli_data_seek($kategori, 0);
                    while ($row = mysqli_fetch_assoc($kategori)):
                    ?>
                        <option value="<?= $row['id'] ?>" <?= $search_kategori == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nama_kategori']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-2"></i>Cari</button>
            <?php if (isset($_GET['id'])): ?>
                <input type="hidden" name="id" value="<?= intval($_GET['id']) ?>">
            <?php endif; ?>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="glass-card">
            <div class="card-header">
                <h2><i class="fas fa-newspaper mr-2"></i><?= htmlspecialchars($data['judul']) ?></h2>
            </div>
            <div class="card-body">
                <div class="meta-container">
                    <div class="meta-item"><b>Kategori:</b> <?= htmlspecialchars($data['nama_kategori'] ?: 'Tidak ada kategori') ?></div>
                    <div class="meta-item"><b>Penulis:</b> <?= htmlspecialchars($data['username']) ?></div>
                    <div class="meta-item"><b>Status:</b> <?= htmlspecialchars(ucfirst($data['status'])) ?></div>
                    <div class="meta-item"><b>Tanggal:</b> <?= htmlspecialchars(date('d F Y H:i', strtotime($data['created_at']))) ?> WIB</div>
                </div>
                <?php if ($data['gambar']): ?>
                    <img src="upload/<?= htmlspecialchars($data['gambar']) ?>" class="img-preview" alt="Gambar Berita">
                <?php endif; ?>
                <div class="content"><?= nl2br(htmlspecialchars($data['isi'])) ?></div>
                <a href="berita_list.php" class="btn-back"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar</a>
            </div>
        </div>

        <!-- Search Results -->
        <?php if ($search_performed): ?>
            <div class="search-results">
                <h4><i class="fas fa-list mr-2"></i>Hasil Pencarian Berita</h4>
                <?php if (count($search_results) === 0): ?>
                    <div class="alert alert-warning" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">Tidak ada berita ditemukan untuk pencarian Anda.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($search_results as $berita): ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100">
                                    <?php if ($berita['gambar']): ?>
                                        <img src="upload/<?= htmlspecialchars($berita['gambar']) ?>" class="card-img-top" alt="Gambar Berita">
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="berita_detail.php?id=<?= $berita['id'] ?>"><?= htmlspecialchars($berita['judul']) ?></a>
                                        </h5>
                                        <div class="mb-2"><span class="badge badge-info"><i class="fas fa-tag mr-1"></i><?= htmlspecialchars($berita['nama_kategori'] ?: 'Tanpa Kategori') ?></span></div>
                                        <div class="mb-2 text-muted" style="font-size: 0.9rem;">
                                            <i class="fas fa-user mr-1"></i> <?= htmlspecialchars($berita['username']) ?>
                                            | <i class="far fa-clock mr-1"></i> <?= date('d M Y', strtotime($berita['created_at'])) ?>
                                        </div>
                                        <div class="mb-3" style="color: var(--text-muted); font-size: 0.95rem;">
                                            <?= htmlspecialchars(mb_strimwidth(strip_tags($berita['isi']), 0, 100, '...')) ?>
                                        </div>
                                        <a href="berita_detail.php?id=<?= $berita['id'] ?>" class="btn btn-outline-primary mt-auto"><i class="fas fa-arrow-right mr-1"></i>Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>