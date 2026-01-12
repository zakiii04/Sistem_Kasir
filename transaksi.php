<?php
session_start();
include "conn/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   INISIALISASI KERANJANG
========================= */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* =========================
   TAMBAH PRODUK
========================= */
if (isset($_GET['add'])) {
    $id_barang = intval($_GET['add']);
    $q = $mysqli->query("SELECT * FROM barang WHERE id = $id_barang");

    if ($q && $q->num_rows > 0) {
        $b = $q->fetch_assoc();

        // CEK STOK
        $stok_asli = $b['jumlah'];

        // Hitung berapa yang sudah ada di keranjang
        $qty_dalam_cart = 0;
        foreach ($_SESSION['cart'] as $item) {
            if ($item['id'] == $id_barang) {
                $qty_dalam_cart = $item['jumlah'];
            }
        }

        // Kalau stok habis → tolak
        if ($stok_asli <= $qty_dalam_cart) {
            echo "<script>alert('Stok tidak cukup! Stok tersedia: $stok_asli'); 
                  window.location='transaksi.php';</script>";
            exit;
        }
        // check cart
        $found = false;
        foreach ($_SESSION['cart'] as $i => $item) {
            if ($item['id'] == $id_barang) {
                $_SESSION['cart'][$i]['jumlah']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id_barang,
                'nama' => $b['nama'],
                'harga' => $b['harga'],
                'jumlah' => 1
            ];
        }
    }
    header("Location: transaksi.php");
    exit;
}
/* =========================
   HAPUS ITEM
========================= */
if (isset($_GET['hapus'])) {
    $index = intval($_GET['hapus']);
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: transaksi.php");
    exit;
}

/* =========================
   SIMPAN TRANSAKSI
========================= */
if (isset($_POST['simpan_transaksi'])) {

    // update qty
    foreach ($_POST['qty'] as $i => $qty) {
        $_SESSION['cart'][$i]['jumlah'] = max(1, intval($qty));
    }

    // TOTAL SEBELUM DISKON
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $i => $c) {
        $sub = $c['harga'] * $c['jumlah'];
        $_SESSION['cart'][$i]['total'] = $sub;
        $subtotal += $sub;
    }

    // DISKON
    $diskonPersen = intval($_POST['diskon']);
    $potongan = $subtotal * ($diskonPersen / 100);

    // TOTAL SETELAH DISKON
    $total_akhir = $subtotal - $potongan;

    // NAMA PELANGGAN
    $nama_pelanggan = $mysqli->real_escape_string($_POST['nama_pelanggan']);

    $tanggal = date("Y-m-d H:i:s");

    // SIMPAN TRANSAKSI
    $q1 = $mysqli->query("
        INSERT INTO transaksi (tanggal, nama_pelanggan, subtotal, diskon, total_harga)
        VALUES ('$tanggal', '$nama_pelanggan', '$subtotal', '$potongan', '$total_akhir')
    ");

    if (!$q1) {
        die("Error simpan transaksi: " . $mysqli->error);
    }

    $id_transaksi = $mysqli->insert_id;

    // SIMPAN DETAIL
    foreach ($_SESSION['cart'] as $c) {
        $id_barang = $c['id'];
        $jumlah = $c['jumlah'];
        $total_item = $c['total'];

        $mysqli->query("
            INSERT INTO transaksi_detail (id_transaksi, id_barang, jumlah, total)
            VALUES ($id_transaksi, $id_barang, $jumlah, $total_item)
        ");

        // UPDATE STOK
        $mysqli->query("
            UPDATE barang SET jumlah = jumlah - $jumlah WHERE id = $id_barang
        ");
    }

    $_SESSION['cart'] = []; // kosongkan keranjang

    echo "<script>
            alert('Transaksi berhasil disimpan');
            window.location='transaksi.php';
          </script>";
    exit;
}


/* =========================
   FILTER PRODUK
========================= */
$kategori_aktif = $_GET['kategori'] ?? '';
$cari = $_GET['cari'] ?? '';

$where = "WHERE 1=1";

$allowed = ['makanan', 'minuman', 'rokok', 'perlengkapan'];
if ($kategori_aktif && in_array($kategori_aktif, $allowed)) {
    $where .= " AND kategori='$kategori_aktif'";
}

if ($cari) {
    $cari_esc = $mysqli->real_escape_string($cari);
    $where .= " AND nama LIKE '%$cari_esc%'";
}

$produk = $mysqli->query("SELECT * FROM barang $where ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Toko Berkah Jaya</title>

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
            padding: 20px;
        }

        /* KATEGORI BUTTON */
        .category-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .category-menu a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            background: #e3f0ff;
            color: #0d6efd;
            font-size: 14px;
            font-weight: 500;
        }

        .category-menu a.active {
            background: #0d6efd;
            color: white;
        }

        /* KARTU PRODUK */
        .card-produk {
            background: #ffffff;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 15px;
            text-align: center;
        }

        .card-produk img {
            border-radius: 10px;
            margin-bottom: 8px;
        }

        /* KERANJANG */
        .cart-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .total-box {
            background: #0d6efd;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-size: 18px;
            margin-top: 10px;
        }

        /* Search animation */
        .search-box {
            transition: 0.3s;
        }

        .search-input {
            transition: 0.3s;
        }

        .search-input:focus {
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.4);
            border-color: #0d6efd;
        }

        /* Icon animation */
        .search-icon {
            transition: 0.3s;
        }

        .search-input:focus+.search-icon,
        .search-box:focus-within .search-icon {
            background: #0d6efd;
            color: white;
        }

        /* Autocomplete box */
        .autocomplete-box {
            position: absolute;
            top: 46px;
            left: 0;
            width: 100%;
            background: white;
            border-radius: 10px;
            border: 1px solid #ddd;
            z-index: 999;
            display: none;
            max-height: 180px;
            overflow-y: auto;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .autocomplete-item {
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f3f3f3;
            transition: 0.2s;
        }

        .autocomplete-item:hover {
            background: #e9f3ff;
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

        @media print {
            body * {
                visibility: hidden !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #struk,
            #struk * {
                visibility: visible !important;
            }

            #struk {
                position: absolute;
                left: 0;
                top: 0;
                width: 230px;
                /* kertas 58mm */
                padding: 5px;
                font-family: monospace;
                font-size: 11px;
                line-height: 1.3;
            }

            .center {
                text-align: center;
            }

            .line {
                border-bottom: 1px dashed #000;
                margin: 5px 0;
            }

            .row3 {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .name {
                width: 90px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .qty {
                width: 30px;
                text-align: right;
            }

            .sub {
                width: 60px;
                text-align: right;
            }

            #struk div {
                margin: 0;
                padding: 0;
            }


        }
    </style>
    <style>
        /* Background keranjang */
        .cart-box {
            background: #f0f7ff;
            /* biru muda lembut */
            border: 1px solid #cde1ff;
            border-radius: 12px;
        }

        /* Judul keranjang dengan icon */
        .keranjang-title {
            background: #0d6efd;
            color: white;
            padding: 10px 14px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }

        /* Icon keranjang */
        .keranjang-title i {
            font-size: 18px;
        }

        /* ======= UI PREMIUM BLUE MODERN ======= */
        body {
            background: #eef3fb;
            font-family: 'Poppins', sans-serif;
        }

        /* CARD PRODUK */
        .card-produk {
            background: white;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            transition: 0.2s;
            border: 1px solid #e7eefa;
        }

        .card-produk:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.12);
        }

        /* Gambar produk */
        .card-produk img {
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
        }

        /* Tombol Tambah */
        .btn-add {
            background: #0d6efd;
            color: white;
            border-radius: 12px;
            padding: 6px 10px;
            transition: .2s;
        }

        .btn-add:hover {
            background: #0a58ca;
            transform: translateY(-2px);
        }

        /* CATEGORY CHIP MENU */
        .category-menu a {
            padding: 7px 18px;
            border-radius: 25px;
            background: #dde9fb;
            font-size: 13.5px;
            border: 1px solid #c6d7f5;
        }

        .category-menu a.active {
            background: #0d6efd;
            color: white !important;
            border-color: #0b5ed7;
            font-weight: 600;
        }

        /* SEARCH BAR PREMIUM */
        .search-box {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #dbe3f4;
            background: white;
        }

        .search-input {
            border: none !important;
        }

        .search-input:focus {
            outline: none !important;
            box-shadow: none;
        }

        .search-icon {
            background: #eef3ff;
            border: none;
            color: #6a8cd5;
        }

        /* KERANJANG PREMIUM */
        .cart-box {
            background: white;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e8edfa;
        }

        /* Header keranjang */
        .keranjang-title {
            background: linear-gradient(135deg, #0d6efd, #529cff);
            color: white;
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 12px;
            font-size: 16px;
        }

        /* Tombol Hapus kecil modern */
        .btn-delete {
            background: #ffeded;
            color: #d9534f;
            border-radius: 10px;
            width: 34px;
            height: 34px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: #ffdede;
            transform: translateY(-2px);
        }

        /* TOTAL BOX */
        .total-box {
            background: #0d6efd;
            color: white;
            border-radius: 14px;
            padding: 14px;
            font-size: 20px;
            font-weight: 600;
            margin-top: 15px;
        }

        /* BUTTON SIMPAN TRANSAKSI */
        .btn-finish {
            background: #28c76f;
            color: white;
            border-radius: 12px;
            padding: 12px;
            transition: .2s;
        }

        .btn-finish:hover {
            background: #20ad5b;
            transform: translateY(-2px);
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
        <h3 class="fw-bold">Transaksi / Kasir</h3>

        <div class="row g-3">

            <!-- ===================== DAFTAR PRODUK ====================== -->
            <div class="col-md-8">

                <form method="GET" class="mb-3 position-relative" autocomplete="off">
                    <div class="input-group search-box">
                        <span class="input-group-text search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="cari" id="searchInput" class="form-control search-input"
                            placeholder="Cari nama produk..." value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">
                    </div>

                    <div id="autocompleteBox" class="autocomplete-box"></div>
                </form>

                <div class="mb-2 fw-semibold">Kategori</div>
                <div class="category-menu mb-3">
                    <a href="transaksi.php" class="<?= $kategori_aktif == '' ? 'active' : '' ?>">Semua</a>
                    <a href="transaksi.php?kategori=makanan"
                        class="<?= $kategori_aktif == 'makanan' ? 'active' : '' ?>">Makanan</a>
                    <a href="transaksi.php?kategori=minuman"
                        class="<?= $kategori_aktif == 'minuman' ? 'active' : '' ?>">Minuman</a>
                    <a href="transaksi.php?kategori=rokok"
                        class="<?= $kategori_aktif == 'rokok' ? 'active' : '' ?>">Rokok</a>
                    <a href="transaksi.php?kategori=perlengkapan"
                        class="<?= $kategori_aktif == 'perlengkapan' ? 'active' : '' ?>">Perlengkapan Rumah</a>
                </div>

                <div class="row">
                    <?php if ($produk->num_rows == 0): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size:64px;color:#ddd;"></i>
                                <h5 class="mt-3 text-muted">Produk Tidak Tersedia</h5>
                                <p class="text-muted">Produk yang Anda cari tidak ditemukan</p>
                                <a href="transaksi.php" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php while ($p = $produk->fetch_assoc()): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card-produk">

                                    <?php if (!empty($p['gambar'])): ?>
                                        <img src="uploads/<?= $p['gambar'] ?>" class="img-fluid"
                                            style="height:130px;object-fit:cover;">
                                    <?php else: ?>
                                        <div class="bg-light mb-2" style="height:130px;border-radius:10px;"></div>
                                    <?php endif; ?>

                                    <div class="fw-semibold"><?= htmlspecialchars($p['nama']) ?></div>
                                    <div class="text-muted mb-2">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>

                                    <?php if ($p['jumlah'] == 0): ?>
                                        <button type="button" class="btn btn-danger btn-sm w-100"
                                            style="background:#d60000;border:none;" onclick="alert('❌ Stok produk habis')">
                                            <i class="bi bi-x-circle"></i> Stok habis
                                        </button>
                                    <?php else: ?>
                                        <a href="transaksi.php?add=<?= $p['id'] ?>" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-cart-plus"></i> Tambah
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ===================== KERANJANG ====================== -->
            <div class="col-md-4">

                <div class="cart-box">
                    <h5 class="fw-bold keranjang-title">
                        <i class="bi bi-cart-fill"></i> Keranjang
                    </h5>

                    <hr>

                    <form method="POST" id="form-cart">

                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                if (!empty($_SESSION['cart'])):
                                    foreach ($_SESSION['cart'] as $i => $c):
                                        $subtotal = $c['harga'] * $c['jumlah'];
                                        $grand_total += $subtotal;
                                        ?>
                                        <tr class="cart-row" data-harga="<?= $c['harga'] ?>">
                                            <td><?= htmlspecialchars($c['nama']) ?></td>
                                            <td>Rp <?= number_format($c['harga'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="qty[<?= $i ?>]"
                                                    class="form-control form-control-sm jumlah" min="1"
                                                    value="<?= $c['jumlah'] ?>" style="width:70px;">
                                            </td>
                                            <td>
                                                <a href="transaksi.php?hapus=<?= $i ?>" class="btn btn-delete"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?');">
                                                    <i class="bi bi-x"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Keranjang kosong</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- ===================== NAMA PELANGGAN ====================== -->
                        <div class="mt-2">
                            <label class="form-label fw-bold">Nama Pelanggan</label>
                            <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control"
                                placeholder="Opsional, boleh dikosongkan">
                        </div>

                        <!-- ===================== DISKON ====================== -->
                        <div class="mt-2">
                            <label class="form-label fw-bold">Diskon (%)</label>
                            <input type="number" id="diskon" name="diskon" class="form-control" min="0" max="100"
                                value="0">
                        </div>

                        <!-- ===================== TOTAL ====================== -->
                        <div class="total-box">
                            Total:
                            <span class="float-end" id="grand_total">
                                Rp <?= number_format($grand_total, 0, ',', '.') ?>
                            </span>
                        </div>
                        <input type="hidden" id="input_total" name="total" value="<?= $grand_total ?>">

                        <!-- ===================== UANG BAYAR ====================== -->
                        <div class="mt-3">
                            <label class="form-label">Uang Bayar</label>
                            <input type="number" id="uang_bayar" class="form-control">
                        </div>

                        <div class="mt-2">
                            <label class="form-label">Kembalian</label>
                            <input type="text" id="kembalian" class="form-control" readonly>
                        </div>

                        <!-- ===================== SUBMIT ====================== -->
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <button type="submit" name="simpan_transaksi" class="btn btn-success w-100 mt-3"
                                onclick="return beforeSubmit()">
                                <i class="bi bi-printer"></i> Selesai & Print Struk
                            </button>
                        <?php endif; ?>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== STRUK PRINT AREA ====================== -->
    <div id="struk" style="display:none;">

        <div class="center bold">TOKO BERKAH JAYA</div>
        <div class="center">Jl. Raya Sukamaju 21</div>
        <div class="center">Telp: 0812-3456-7890</div>


        <div class="line"></div>

        <div>Tanggal : <?= date("d/m/Y H:i") ?></div>
        <div>Pelanggan : <span id="struk_nama"></span></div>

        <div class="line"></div>
        <div class="row3 bold">
            <span class="name">Barang</span>
            <span class="qty">Qty</span>
            <span class="sub">Subt</span>
        </div>

        <div class="line"></div>

        <div id="struk_list"></div>

        <div class="line"></div>

        <div class="row3"><span>Subtotal</span><span id="struk_sub"></span></div>
        <div class="row3"><span>Diskon</span><span id="struk_diskon"></span></div>
        <div class="row3"><strong>Total</strong><strong id="struk_total"></strong></div>
        <div class="row3"><span>Bayar</span><span id="struk_uang"></span></div>
        <div class="row3"><span>Kembali</span><span id="struk_kembali"></span></div>

        <div class="line"></div>

        <div class="center">TERIMA KASIH</div>
        <div class="center">SIMPAN STRUK ANDA</div>

    </div>



    <script>
        // ==================== HITUNG TOTAL & KEMBALIAN OTOMATIS ====================
        function updateTotals() {
            let rows = document.querySelectorAll(".cart-row");
            let total = 0;

            rows.forEach(row => {
                let harga = parseInt(row.dataset.harga) || 0;
                let qty = parseInt(row.querySelector(".jumlah").value) || 1;
                total += harga * qty;
            });

            // Diskon %
            let diskonPersen = parseFloat(document.getElementById("diskon").value) || 0;
            if (diskonPersen < 0) diskonPersen = 0;
            if (diskonPersen > 100) diskonPersen = 100;

            let potongan = total * (diskonPersen / 100);
            let totalAkhir = Math.round(total - potongan);

            // Tampilkan total akhir
            document.getElementById("grand_total").innerText =
                "Rp " + totalAkhir.toLocaleString('id-ID');

            document.getElementById("input_total").value = totalAkhir;

            // Hitung kembalian
            let bayar = parseInt(document.getElementById("uang_bayar").value) || 0;
            let kembali = bayar - totalAkhir;

            document.getElementById("kembalian").value =
                bayar > 0
                    ? (kembali >= 0 ? "Rp " + kembali.toLocaleString('id-ID') : "Uang kurang!")
                    : "";
        }

        // Event untuk diskon
        document.getElementById("diskon").addEventListener("input", updateTotals);

        // Event untuk uang bayar
        document.getElementById("uang_bayar").addEventListener("input", updateTotals);

        // Event qty → total otomatis + validasi
        document.querySelectorAll(".jumlah").forEach(q => {
            q.addEventListener("input", function () {
                // Validasi qty tidak boleh < 1
                let qty = parseInt(this.value);
                if (qty < 1 || isNaN(qty)) {
                    alert('⚠️ Jumlah tidak valid! Minimal jumlah adalah 1.');
                    this.value = 1; // Set ke 1 otomatis
                }
                updateTotals();
            });
        });


        // ==================== SEBELUM SUBMIT → SIAPKAN STRUK & PRINT ====================
        function beforeSubmit() {
            // VALIDASI: Cek uang bayar tidak boleh kurang
            let totalAkhir = parseInt(document.getElementById("input_total").value) || 0;
            let uangBayar = parseInt(document.getElementById("uang_bayar").value) || 0;

            if (uangBayar === 0) {
                alert('Silakan masukkan uang bayar terlebih dahulu!');
                return false;
            }

            if (uangBayar < totalAkhir) {
                alert('❌ Uang bayar tidak boleh kurang dari total!\\n\\nTotal: Rp ' + totalAkhir.toLocaleString('id-ID') + '\\nBayar: Rp ' + uangBayar.toLocaleString('id-ID') + '\\nKurang: Rp ' + (totalAkhir - uangBayar).toLocaleString('id-ID'));
                document.getElementById("uang_bayar").focus();
                return false; // Cancel submit
            }

            // Nama pelanggan
            let nama = document.getElementById("nama_pelanggan").value;
            if (nama.trim() === "") nama = "-";
            document.getElementById("struk_nama").innerText = nama;

            // List barang
            let list = "";
            let subtotal = 0;

            document.querySelectorAll(".cart-row").forEach(row => {
                let nama = row.children[0].innerText;
                let harga = parseInt(row.dataset.harga);
                let qty = parseInt(row.querySelector(".jumlah").value);
                let sub = harga * qty;

                subtotal += sub;

                list += `
        <div class="row3">
            <span class="name">${nama}</span>
            <span class="qty">${qty}</span>
            <span class="sub">${sub.toLocaleString('id-ID')}</span>
        </div>
    `;

            });

            document.getElementById("struk_list").innerHTML = list;

            // Diskon
            let diskonPersen = parseFloat(document.getElementById("diskon").value) || 0;
            let potongan = subtotal * (diskonPersen / 100);

            // Total akhir
            let total = subtotal - potongan;
            let bayar = parseInt(document.getElementById("uang_bayar").value);
            let kembali = bayar - total;

            document.getElementById("struk_sub").innerText =
                "Rp " + subtotal.toLocaleString("id-ID");

            document.getElementById("struk_diskon").innerText =
                diskonPersen + "% (Rp " + potongan.toLocaleString("id-ID") + ")";

            document.getElementById("struk_total").innerText =
                "Rp " + total.toLocaleString("id-ID");

            document.getElementById("struk_uang").innerText =
                "Rp " + bayar.toLocaleString("id-ID");

            document.getElementById("struk_kembali").innerText =
                "Rp " + kembali.toLocaleString("id-ID");

            // Print proses
            document.getElementById("struk").style.display = "block";
            window.print();
            document.getElementById("struk").style.display = "none";

            return true; // lanjut simpan transaksi

        }







        // ==================== AUTOCOMPLETE PRODUK ====================
        const searchInput = document.getElementById("searchInput");
        const autocompleteBox = document.getElementById("autocompleteBox");

        searchInput.addEventListener("keyup", () => {
            let keyword = searchInput.value.trim();

            if (keyword.length < 1) {
                autocompleteBox.style.display = "none";
                return;
            }

            fetch("ajax/search_produk.php?q=" + keyword)
                .then(res => res.json())
                .then(data => {
                    let html = "";
                    data.forEach(item => {
                        html += `<div class="autocomplete-item">${item}</div>`;
                    });

                    autocompleteBox.innerHTML = html;
                    autocompleteBox.style.display = html ? "block" : "none";

                    document.querySelectorAll(".autocomplete-item").forEach(el => {
                        el.addEventListener("click", () => {
                            searchInput.value = el.innerText;
                            autocompleteBox.style.display = "none";
                        });
                    });
                });
        });

        // Klik di luar autocomplete → sembunyikan
        document.addEventListener("click", (e) => {
            if (!searchInput.contains(e.target)) {
                autocompleteBox.style.display = "none";
            }
        });
    </script>






</body>

</html>