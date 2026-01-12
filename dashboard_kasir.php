<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Set timezone Indonesia
include "conn/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Redirect admin ke dashboard admin
if ($_SESSION['role'] == 'admin') {
    header("Location: dashboard.php");
    exit;
}

// =================== GREETING ===================
$hour = date('H');
if ($hour < 12) {
    $greeting = 'Pagi';
    $greeting_icon = '🌅';
} elseif ($hour < 18) {
    $greeting = 'Siang';
    $greeting_icon = '☀️';
} else {
    $greeting = 'Malam';
    $greeting_icon = '🌙';
}

// =================== STATISTICS KASIR ===================
$id_kasir = $_SESSION['id_user'] ?? 0;
$hari_ini = date("Y-m-d");

// Transaksi hari ini (Kasir ini)
$q_transaksi_kasir = $mysqli->query("
    SELECT COUNT(*) AS total 
    FROM transaksi 
    WHERE DATE(tanggal) = '$hari_ini'
");
$transaksi_kasir_hari_ini = $q_transaksi_kasir->fetch_assoc()['total'];

// Pendapatan hari ini (Kasir ini)
$q_pendapatan_kasir = $mysqli->query("
    SELECT SUM(total_harga) AS total 
    FROM transaksi 
    WHERE DATE(tanggal) = '$hari_ini'
");
$pendapatan_kasir_hari_ini = $q_pendapatan_kasir->fetch_assoc()['total'] ?? 0;

// Items terjual hari ini (Kasir ini)
$q_items_kasir = $mysqli->query("
    SELECT SUM(td.jumlah) AS total
    FROM transaksi_detail td
    JOIN transaksi t ON t.id = td.id_transaksi
    WHERE DATE(t.tanggal) = '$hari_ini'
");
$items_kasir_hari_ini = $q_items_kasir->fetch_assoc()['total'] ?? 0;

// Riwayat transaksi hari ini
$riwayat_transaksi = $mysqli->query("
    SELECT * FROM transaksi
    WHERE DATE(tanggal) = '$hari_ini'
    ORDER BY tanggal DESC
    LIMIT 10
");

// Total pendapatan bulan ini
$bulan_ini = date("Y-m");
$q_pendapatan_bulan = $mysqli->query("
    SELECT SUM(total_harga) AS total 
    FROM transaksi 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'
");
$pendapatan_bulan_ini = $q_pendapatan_bulan->fetch_assoc()['total'] ?? 0;

// Target penjualan (contoh: 10 juta per bulan)
$target_penjualan = 10000000;
$progress_penjualan = ($pendapatan_bulan_ini / $target_penjualan) * 100;
$progress_penjualan = min(100, $progress_penjualan); // max 100%
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Toko Berkah Jaya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2f7 0%, #dfe7f2 100%);
            min-height: 100vh;
        }

        /* SIDEBAR PREMIUM */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: rgba(13, 40, 80, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 6px 0 20px rgba(0, 0, 0, 0.25);
            padding: 25px 15px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 35px;
            font-weight: 700;
            color: #ffffff;
            font-size: 20px;
            letter-spacing: .5px;
        }

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

        .sidebar a i {
            font-size: 18px;
            opacity: 0.85;
            transition: .25s ease;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15);
        }

        .sidebar a:hover i {
            opacity: 1;
        }

        .sidebar .active {
            background: linear-gradient(135deg, #0d6efd, #00b7ff);
            color: #fff !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0, 180, 255, 0.45);
        }

        .sidebar .active i {
            opacity: 1;
        }

        /* CONTENT */
        .content {
            margin-left: 260px;
            padding: 25px;
        }

        /* WELCOME HEADER */
        .welcome-header {
            background: linear-gradient(135deg, #0d6efd, #4a90e2);
            color: white;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(13, 110, 253, 0.25);
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* STAT CARDS */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-card.blue {
            border-color: #0d6efd;
        }

        .stat-card.green {
            border-color: #28a745;
        }

        .stat-card.orange {
            border-color: #fd7e14;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin: 8px 0;
        }

        .stat-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 40px;
            opacity: 0.2;
        }

        /* BIG BUTTON */
        .btn-big {
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
        }

        .btn-big:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
        }

        /* QUICK ACTION CARDS */
        .action-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: #0d6efd;
        }

        .action-card i {
            font-size: 42px;
            margin-bottom: 12px;
            color: #0d6efd;
        }

        .action-card .action-title {
            font-weight: 600;
            font-size: 15px;
        }

        /* TABLE */
        .table-box {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-title {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }

        /* PROGRESS BAR */
        .progress-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .progress {
            height: 25px;
            border-radius: 12px;
        }

        .progress-bar {
            border-radius: 12px;
            background: linear-gradient(90deg, #28a745, #5fdc7a);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4><i class="bi bi-bag"></i> Berkah Jaya</h4>

        <a href="dashboard_kasir.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi</a>
        <a href="laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <a href="logout.php" class="text-warning mt-auto pt-4 border-top border-white border-opacity-10"><i
                class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- WELCOME HEADER -->
        <div class="welcome-header">
            <h3 class="mb-1">
                <?= $greeting_icon ?> Selamat
                <?= $greeting ?>,
                <?= htmlspecialchars($_SESSION['username']) ?>!
            </h3>
            <p class="mb-0 opacity-75">
                <?= date('d F Y, H:i') ?> WIB
            </p>
        </div>

        <!-- STAT CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card blue">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Transaksi Hari Ini</div>
                            <div class="stat-number text-primary">
                                <?= $transaksi_kasir_hari_ini ?>
                            </div>
                        </div>
                        <i class="bi bi-receipt stat-icon text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card green">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Pendapatan Hari Ini</div>
                            <div class="stat-number text-success">Rp
                                <?= number_format($pendapatan_kasir_hari_ini, 0, ',', '.') ?>
                            </div>
                        </div>
                        <i class="bi bi-currency-dollar stat-icon text-success"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card orange">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Items Terjual</div>
                            <div class="stat-number text-warning">
                                <?= $items_kasir_hari_ini ?>
                            </div>
                        </div>
                        <i class="bi bi-box-seam stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- BIG BUTTON -->
        <div class="mb-4">
            <a href="transaksi.php" class="btn btn-primary btn-big w-100">
                <i class="bi bi-cart-plus me-2"></i> Mulai Transaksi Baru
            </a>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="mb-4">
            <a href="laporan_transaksi.php" class="action-card">
                <i class="bi bi-bar-chart-fill"></i>
                <div class="action-title">Laporan Transaksi</div>
            </a>
        </div>

        <div class="row g-4">
            <!-- RIWAYAT TRANSAKSI -->
            <div class="col-md-8">
                <div class="table-box">
                    <div class="table-title"><i class="bi bi-clock-history"></i> Transaksi Hari Ini</div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayat_transaksi->num_rows > 0):
                                    $no = 1;
                                    while ($r = $riwayat_transaksi->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?= $no++ ?>
                                            </td>
                                            <td>
                                                <?= date('H:i', strtotime($r['tanggal'])) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($r['nama_pelanggan'] ?: '-') ?>
                                            </td>
                                            <td><strong>Rp
                                                    <?= number_format($r['total_harga'], 0, ',', '.') ?>
                                                </strong></td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada transaksi hari ini
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TARGET PENJUALAN -->
            <div class="col-md-4">
                <div class="progress-box">
                    <div class="table-title"><i class="bi bi-bullseye"></i> Target Penjualan Bulan Ini</div>

                    <div class="text-center mb-3">
                        <div style="font-size: 48px; font-weight: 700; color: #28a745;">
                            <?= round($progress_penjualan) ?>%
                        </div>
                        <small class="text-muted">dari target Rp
                            <?= number_format($target_penjualan, 0, ',', '.') ?>
                        </small>
                    </div>

                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: <?= $progress_penjualan ?>%"
                            aria-valuenow="<?= $progress_penjualan ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Terkumpul:</small>
                        <small class="fw-bold">Rp
                            <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?>
                        </small>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Sisa:</small>
                        <small class="fw-bold text-danger">Rp
                            <?= number_format($target_penjualan - $pendapatan_bulan_ini, 0, ',', '.') ?>
                        </small>
                    </div>

                    <?php if ($progress_penjualan >= 100): ?>
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-trophy-fill"></i> Target tercapai! 🎉
                        </div>
                    <?php elseif ($progress_penjualan >= 75): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-fire"></i> Hampir tercapai! Sedikit lagi! 💪
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</body>

</html>