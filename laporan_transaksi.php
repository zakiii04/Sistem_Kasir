<?php
session_start();
include "conn/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* =============================
   FILTER TANGGAL
============================= */
$today = date('Y-m-d');
$start = $_GET['start'] ?? $today;
$end = $_GET['end'] ?? $today;

$start_safe = $mysqli->real_escape_string($start);
$end_safe = $mysqli->real_escape_string($end);

/* =============================
   AMBIL DATA LAPORAN TRANSAKSI
============================= */
$sql = "
    SELECT 
        t.id,
        t.tanggal,
        t.nama_pelanggan,
        t.subtotal,
        t.diskon,
        t.total_harga,
        COUNT(td.id) AS total_item
    FROM transaksi t
    LEFT JOIN transaksi_detail td ON t.id = td.id_transaksi
    WHERE DATE(t.tanggal) BETWEEN '$start_safe' AND '$end_safe'
    GROUP BY t.id
    ORDER BY t.tanggal DESC
";
$laporan = $mysqli->query($sql);

/* =============================
   STATISTIK PERIODE
============================= */
// Total Transaksi
$q_total_transaksi = $mysqli->query("
    SELECT COUNT(*) AS total FROM transaksi 
    WHERE DATE(tanggal) BETWEEN '$start_safe' AND '$end_safe'
");
$total_transaksi = $q_total_transaksi->fetch_assoc()['total'];

// Total Pendapatan
$q_total_pendapatan = $mysqli->query("
    SELECT SUM(total_harga) AS total FROM transaksi 
    WHERE DATE(tanggal) BETWEEN '$start_safe' AND '$end_safe'
");
$total_pendapatan = $q_total_pendapatan->fetch_assoc()['total'] ?? 0;

// Total Diskon Diberikan
$q_total_diskon = $mysqli->query("
    SELECT SUM(diskon) AS total FROM transaksi 
    WHERE DATE(tanggal) BETWEEN '$start_safe' AND '$end_safe'
");
$total_diskon = $q_total_diskon->fetch_assoc()['total'] ?? 0;

// Total Item Terjual
$q_total_item = $mysqli->query("
    SELECT SUM(td.jumlah) AS total
    FROM transaksi_detail td
    JOIN transaksi t ON t.id = td.id_transaksi
    WHERE DATE(t.tanggal) BETWEEN '$start_safe' AND '$end_safe'
");
$total_item_terjual = $q_total_item->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Toko Berkah Jaya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        /* ======== SIDEBAR PREMIUM ======== */
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

        /* PRINT */
        @media print {
            body * {
                visibility: hidden !important;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        .card-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }

        /* Stat cards */
        .stat-mini {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-mini.blue {
            border-color: #0d6efd;
        }

        .stat-mini.green {
            border-color: #28a745;
        }

        .stat-mini.red {
            border-color: #dc3545;
        }

        .stat-mini.orange {
            border-color: #fd7e14;
        }

        .stat-mini .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .stat-mini .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4><i class="bi bi-bag"></i> Berkah Jaya</h4>

        <?php if ($_SESSION['role'] == 'admin'): ?>

            <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi/Kasir</a>
            <a href="index.php"><i class="bi bi-box-seam"></i> Daftar Produk</a>
            <a href="kategori/kategori.php"><i class="bi bi-tags"></i> Kategori</a>
            <a href="karyawan/karyawan.php"><i class="bi bi-people"></i> Kelola Kasir</a>
            <a href="laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <?php elseif ($_SESSION['role'] == 'kasir'): ?>

            <a href="dashboard_kasir.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi</a>
            <a href="laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <?php endif; ?>


        <a href="logout.php" class="text-warning mt-auto pt-4 border-top border-white border-opacity-10"><i
                class="bi bi-box-arrow-right"></i> Logout</a>
    </div>


    <!-- CONTENT -->
    <div class="content">

        <h3 class="fw-bold"><i class="bi bi-receipt-cutoff"></i> Laporan Transaksi</h3>

        <!-- STATISTIK PERIODE -->
        <div class="row g-3 mb-3 mt-4 no-print">
            <div class="col-md-3">
                <div class="stat-mini blue">
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value">
                        <?= $total_transaksi ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-mini green">
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value">Rp
                        <?= number_format($total_pendapatan, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-mini orange">
                    <div class="stat-label">Total Item Terjual</div>
                    <div class="stat-value">
                        <?= $total_item_terjual ?> pcs
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-mini red">
                    <div class="stat-label">Total Diskon</div>
                    <div class="stat-value">Rp
                        <?= number_format($total_diskon, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card-box mb-3 no-print">
            <form class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start" value="<?= $start ?>" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end" value="<?= $end ?>" class="form-control">
                </div>

                <div class="col-md-4 d-flex gap-2 align-items-end">
                    <button class="btn btn-primary w-50"><i class="bi bi-funnel"></i> Tampilkan</button>
                    <button type="button" onclick="window.print()" class="btn btn-success w-50">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>
        </div>

        <!-- PRINT AREA -->
        <div id="print-area">
            <div class="card-box">

                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0">Toko Berkah Jaya</h5>
                        <small>Laporan Transaksi</small>
                    </div>
                    <div class="text-end">
                        <small>Periode:</small><br>
                        <strong>
                            <?= date('d/m/Y', strtotime($start)) ?> -
                            <?= date('d/m/Y', strtotime($end)) ?>
                        </strong>
                    </div>
                </div>

                <hr>

                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Item</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($laporan->num_rows > 0): ?>
                            <?php $no = 1;
                            while ($r = $laporan->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($r['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($r['nama_pelanggan'] ?: '-') ?>
                                    </td>
                                    <td>
                                        <?= $r['total_item'] ?> item
                                    </td>
                                    <td>Rp
                                        <?= number_format($r['subtotal'], 0, ',', '.') ?>
                                    </td>
                                    <td>Rp
                                        <?= number_format($r['diskon'], 0, ',', '.') ?>
                                    </td>
                                    <td><strong>Rp
                                            <?= number_format($r['total_harga'], 0, ',', '.') ?>
                                        </strong></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada data transaksi pada periode ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Transaksi:</strong></td>
                                <td>
                                    <?= $total_transaksi ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Item Terjual:</strong></td>
                                <td>
                                    <?= $total_item_terjual ?> pcs
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Diskon:</strong></td>
                                <td class="text-danger">Rp
                                    <?= number_format($total_diskon, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Pendapatan:</strong></td>
                                <td><strong class="fs-5">Rp
                                        <?= number_format($total_pendapatan, 0, ',', '.') ?>
                                    </strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>