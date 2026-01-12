<?php
session_start();
include "../conn/koneksi.php";

// Cegah akses jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin yang boleh lihat halaman ini
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk Admin.");
}

// Ambil semua user/karyawan
$users = $mysqli->query("SELECT * FROM user ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Kasir</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #ffffff;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.08);
            padding: 20px 10px;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: #e8f0ff;
            color: #0d6efd;
        }

        .sidebar .active {
            background: #0d6efd;
            color: white;
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

    <!-- SIDEBAR (SAMA DENGAN DASHBOARD) -->
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

    <!-- CONTENT -->
    <div class="content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Kelola Kasir</h3>
            <a href="tambah_karyawan.php" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Tambah Kasir
            </a>
        </div>

        <div class="card p-4">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    while ($u = $users->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $u['nama'] ?></td>
                            <td><?= $u['username'] ?></td>
                            <td><?= ucfirst($u['role']) ?></td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_karyawan.php?id=<?= $u['id'] ?>"
                                        class="btn btn-warning btn-sm text-white">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <a href="hapus_karyawan.php?id=<?= $u['id'] ?>"
                                        onclick="return confirm('Yakin ingin menghapus karyawan?')"
                                        class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                    <?php if ($users->num_rows == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada karyawan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>