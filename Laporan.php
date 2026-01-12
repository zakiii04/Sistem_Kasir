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
   AMBIL DATA LAPORAN PER PRODUK
============================= */
$sql = "
    SELECT 
        t.tanggal,
        b.nama AS nama_produk,
        td.jumlah AS qty,
        td.total AS subtotal
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN barang b ON td.id_barang = b.id
    WHERE DATE(t.tanggal) BETWEEN '$start_safe' AND '$end_safe'
    ORDER BY t.tanggal DESC
";
$laporan = $mysqli->query($sql);

/* =============================
   TOTAL PENDAPATAN PERIODE
============================= */
$sql_total = "
    SELECT SUM(td.total) AS total_pendapatan
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    WHERE DATE(t.tanggal) BETWEEN '$start_safe' AND '$end_safe'
";
$sum = $mysqli->query($sql_total)->fetch_assoc();
$pendapatan_periode = $sum['total_pendapatan'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Per Produk - Toko Berkah Jaya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

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
            <a href="laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan Produk</a>

        <?php elseif ($_SESSION['role'] == 'kasir'): ?>

            <a href="dashboard_kasir.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi</a>
            <a href="laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan Produk</a>

        <?php endif; ?>


        <a href="logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>


    <!-- CONTENT -->
    <div class="content">

        <h3 class="fw-bold">Laporan Penjualan Per Produk</h3>

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
                        <small>Laporan Penjualan Per Produk</small>
                    </div>
                    <div class="text-end">
                        <small>Periode:</small><br>
                        <strong><?= date('d/m/Y', strtotime($start)) ?> - <?= date('d/m/Y', strtotime($end)) ?></strong>
                    </div>
                </div>

                <hr>

                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($laporan->num_rows > 0): ?>
                            <?php $no = 1;
                            while ($r = $laporan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($r['tanggal'])) ?></td>
                                    <td><?= $r['nama_produk'] ?></td>
                                    <td><?= $r['qty'] ?> pcs</td>
                                    <td>Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada transaksi pada periode ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <hr>

                <div class="text-end">
                    <span>Total Pendapatan:</span><br>
                    <span class="fs-4 text-success fw-bold">
                        Rp <?= number_format($pendapatan_periode, 0, ',', '.') ?>
                    </span>
                </div>

            </div>
        </div>

    </div>

</body>

</html>