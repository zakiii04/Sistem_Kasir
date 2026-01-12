<?php
session_start();
include "../conn/koneksi.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$kategori = $mysqli->query("SELECT * FROM kategori ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }



        /* CONTENT */
        .content {
            margin-left: 260px;
            padding: 25px;
        }

        /* CARD LIST */
        .card {
            border: none;
            border-radius: 14px;
            background: #ffffffdd;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
        }

        .table img {
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* ======== SIDEBAR PREMIUM ======== */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;

            /* Glassmorphism */
            background: rgba(13, 40, 80, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);

            /* Border & Shadow */
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 6px 0 20px rgba(0, 0, 0, 0.25);

            padding: 25px 15px;
            color: white;

            display: flex;
            flex-direction: column;
        }

        /* Title */
        .sidebar h4 {
            text-align: center;
            margin-bottom: 35px;
            font-weight: 700;
            color: #ffffff;
            font-size: 20px;
            letter-spacing: .5px;
        }

        /* Menu base */
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;

            text-decoration: none;
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 12px;

            color: #e9e9e9;
            font-weight: 500;
            font-size: 15px;

            transition: .25s ease;
        }

        /* ICON styling */
        .sidebar a i {
            font-size: 18px;
            opacity: 0.85;
            transition: .25s ease;
        }

        /* Hover Glow */
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15);
        }

        .sidebar a:hover i {
            opacity: 1;
        }

        /* ACTIVE MENU */
        .sidebar .active {
            background: linear-gradient(135deg, #0d6efd, #00b7ff);
            color: #fff !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0, 180, 255, 0.45);
        }

        .sidebar .active i {
            opacity: 1;
        }
    </style>
</head>

<body>

    <!-- ======================= SIDEBAR ======================= -->
    <div class="sidebar">
        <h4><i class="bi bi-bag"></i> Berkah Jaya</h4>

        <?php if ($_SESSION['role'] == 'admin'): ?>

            <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi/Kasir</a>
            <a href="../index.php"><i class="bi bi-box-seam"></i> Daftar Produk</a>
            <a href="../kategori/kategori.php"><i class="bi bi-tags"></i> Kategori</a>
            <a href="../karyawan/karyawan.php"><i class="bi bi-people"></i> Kelola Kasir</a>
            <a href="../laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <?php elseif ($_SESSION['role'] == 'kasir'): ?>

            <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi</a>
            <a href="laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <?php endif; ?>


        <a href="../logout.php" class="text-warning mt-auto pt-4 border-top border-white border-opacity-10"><i
                class="bi bi-box-arrow-right"></i> Logout</a>
    </div>


    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Kelola Kategori</h3>
            <a href="tambah_kategori.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kategori
            </a>
        </div>

        <div class="card p-4">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($kategori->num_rows > 0):
                        $no = 1;
                        while ($k = $kategori->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $k['nama'] ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit_kategori.php?id=<?= $k['id'] ?>"
                                            class="btn btn-warning btn-sm text-white">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>

                                        <a href="hapus_kategori.php?id=<?= $k['id'] ?>"
                                            onclick="return confirm('Hapus kategori ini?')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada kategori.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</body>

</html>