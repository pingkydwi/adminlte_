<?php
include_once 'conf/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$level = $_SESSION['level'];
$user_id = $_SESSION['user_id'];

// Filter berita
$where = '';
if ($level == 'wartawan') {
    $where = "WHERE b.id_pengirim='$user_id'";
}
if ($level == 'editor') {
    $where = "WHERE b.status='draft'";
}
$query = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, u.username FROM berita b LEFT JOIN tb_kategori k ON b.id_kategori=k.id LEFT JOIN tb_users u ON b.id_pengirim=u.id $where ORDER BY b.created_at DESC");

// Count status for chart
$status_counts = ['draft' => 0, 'published' => 0, 'rejected' => 0];
while ($row = mysqli_fetch_assoc($query)) {
    $status_counts[$row['status']]++;
}
mysqli_data_seek($query, 0); // Reset pointer for table display
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary: #6B46C1;
            --secondary: #9B59B6;
            --accent: #00C4CC;
            --bg-gradient: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%);
            --card-bg: rgba(26, 26, 46, 0.95);
            --shadow: 0 10px 30px rgba(107, 70, 193, 0.15);
            --text-light: #E6E6FA;
            --text-muted: #A0AEC0;
            --glow: 0 0 15px rgba(0, 196, 204, 0.5);
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
            padding: 2rem;
            display: block;
        }

        .glass-card {
            background: var(--card-bg);
            border-radius: 2rem;
            box-shadow: var(--shadow);
            width: 100%;
            height: 100%;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            overflow: hidden;
            border-image: linear-gradient(to right, var(--primary), var(--accent)) 1;
            animation: cardGlow 4s infinite alternate;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        @keyframes cardGlow {
            0% { box-shadow: var(--shadow), var(--glow); }
            100% { box-shadow: var(--shadow), 0 0 25px rgba(0, 196, 204, 0.7); }
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            padding: 2rem;
            margin: -2.5rem -2.5rem 2rem -2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .card-header h2 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-light);
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .btn-add, .btn-refresh {
            background: linear-gradient(90deg, var(--accent), #1ABC9C);
            border: none;
            border-radius: 1.2rem;
            padding: 1rem 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-light);
            box-shadow: 0 4px 12px rgba(0, 196, 204, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-add::before, .btn-refresh::before {
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

        .btn-add:hover::before, .btn-refresh:hover::before {
            width: 300%;
            height: 300%;
        }

        .btn-add:hover, .btn-refresh:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 196, 204, 0.4);
        }

        .search-bar {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            font-size: 1.2rem;
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .search-bar:focus {
            border-color: var(--accent);
            box-shadow: var(--glow);
        }

        .table-responsive {
            border-radius: 1.2rem;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
        }

        .table {
            margin-bottom: 0;
            font-size: 1.2rem;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--text-light);
        }

        .table thead th {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            border: none;
            padding: 1.5rem;
            text-align: center;
            font-size: 1.3rem;
            white-space: nowrap;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(107, 70, 193, 0.1);
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }

        .table td {
            vertical-align: middle;
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 1.15rem;
            white-space: nowrap;
        }

        .img-preview {
            max-width: 120px;
            border-radius: 0.75rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 196, 204, 0.3);
        }

        .btn-action {
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0.3rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-action::before {
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

        .btn-action:hover::before {
            width: 200%;
            height: 200%;
        }

        .btn-warning {
            background: linear-gradient(90deg, #F1C40F, #FFD700);
            border: none;
            color: var(--text-light);
        }

        .btn-danger {
            background: linear-gradient(90deg, #E74C3C, #C0392B);
            border: none;
            color: var(--text-light);
        }

        .btn-success {
            background: linear-gradient(90deg, #27AE60, #2ECC71);
            border: none;
            color: var(--text-light);
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .badge-status {
            font-size: 1rem;
            padding: 0.6em 1.2em;
            border-radius: 0.9rem;
            font-weight: 500;
            transition: transform 0.2s ease;
        }

        .badge-status:hover {
            transform: scale(1.05);
        }

        .badge-draft {
            background: #E9ECEF;
            color: #6C757D;
        }

        .badge-published {
            background: #D4EDDA;
            color: #155724;
        }

        .badge-rejected {
            background: #F8D7DA;
            color: #721C24;
        }

        @media (max-width: 768px) {
            .container-wrapper {
                padding: 1.5rem;
            }

            .glass-card {
                padding: 1.5rem;
            }

            .card-header h2 {
                font-size: 2rem;
            }

            .btn-add, .btn-refresh {
                font-size: 1.2rem;
                padding: 0.8rem 2rem;
            }

            .search-bar {
                font-size: 1rem;
                max-width: 100%;
            }

            .table {
                font-size: 1rem;
            }

            .table td, .table th {
                padding: 1rem;
                font-size: 1rem;
            }

            .btn-action {
                font-size: 0.95rem;
                padding: 0.6rem 1.2rem;
            }

            .img-preview {
                max-width: 80px;
            }
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <div class="glass-card">
        <div class="card-header">
            <h2><i class="fas fa-newspaper mr-2"></i>Daftar Berita</h2>
            <div>
                <?php if ($level == 'wartawan'): ?>
                    <a href="berita_form.php" class="btn btn-add"><i class="fas fa-plus mr-2"></i>Tambah Berita</a>
                <?php endif; ?>
                <button class="btn btn-refresh" onclick="location.reload();"><i class="fas fa-sync-alt mr-2"></i>Refresh</button>
            </div>
        </div>
        <input type="text" class="search-bar" id="searchInput" placeholder="Cari judul berita..." onkeyup="filterTable()">
        <div class="table-responsive">
            <table class="table" id="newsTable">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Pengirim</th>
                        <th>Status</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori'] ?: 'Tidak ada kategori') ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="badge-status badge-<?= $row['status'] == 'draft' ? 'draft' : ($row['status'] == 'published' ? 'published' : 'rejected') ?>">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['gambar']): ?>
                                <img src="upload/<?= htmlspecialchars($row['gambar']) ?>" class="img-preview" alt="Gambar Berita">
                            <?php else: ?>
                                <span class="text-muted">Tidak ada gambar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($level == 'wartawan' && $row['status'] == 'draft' && $row['id_pengirim'] == $user_id): ?>
                                <a href="berita_form.php?id=<?= $row['id'] ?>" class="btn btn-action btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                <a href="berita_hapus.php?id=<?= $row['id'] ?>" class="btn btn-action btn-danger" onclick="return confirm('Hapus berita?')"><i class="fas fa-trash"></i> Hapus</a>
                            <?php endif; ?>
                            <?php if ($level == 'editor' && $row['status'] == 'draft'): ?>
                                <a href="berita_approval.php?id=<?= $row['id'] ?>&aksi=publish" class="btn btn-action btn-success"><i class="fas fa-check"></i> Publish</a>
                                <a href="berita_approval.php?id=<?= $row['id'] ?>&aksi=reject" class="btn btn-action btn-danger"><i class="fas fa-times"></i> Tolak</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($query) == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada berita.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <button onclick="openCanvas()">Lihat Statistik</button>
    <div id="canvasPanel" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--card-bg); padding: 2rem; border-radius: 1rem; box-shadow: var(--shadow); z-index: 1000;">
        <button onclick="closeCanvas()">Tutup</button>
        <div id="chartContainer"></div>
    </div>
</div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function filterTable() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let table = document.getElementById('newsTable');
        let tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td')[0]; // Filter by Judul column
            if (td) {
                let text = td.textContent || td.innerText;
                tr[i].style.display = text.toLowerCase().indexOf(input) > -1 ? '' : 'none';
            }
        }
    }

    function openCanvas() {
        document.getElementById('canvasPanel').style.display = 'block';
        document.getElementById('chartContainer').innerHTML = '<pre><code class="chartjs">{\n  "type": "bar",\n  "data": {\n    "labels": ["Draft", "Published", "Rejected"],\n    "datasets": [{\n      "label": "Jumlah Berita",\n      "data": [<?= $status_counts['draft'] ?>, <?= $status_counts['published'] ?>, <?= $status_counts['rejected'] ?>],\n      "backgroundColor": ["#E9ECEF", "#D4EDDA", "#F8D7DA"],\n      "borderColor": ["#6C757D", "#155724", "#721C24"],\n      "borderWidth": 1\n    }]\n  },\n  "options": {\n    "scales": {\n      "y": {\n        "beginAtZero": true\n      }\n    }\n  }\n}</code></pre>';
    }

    function closeCanvas() {
        document.getElementById('canvasPanel').style.display = 'none';
    }
</script>
</body>
</html>