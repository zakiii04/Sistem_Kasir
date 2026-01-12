<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Set timezone Indonesia
include "conn/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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


// =================== STATISTICS ===================
// TOTAL PRODUK
$q_produk = $mysqli->query("SELECT COUNT(*) AS total FROM barang");
if ($q_produk) {
    $row_produk = $q_produk->fetch_assoc();
    $total_produk = $row_produk['total'];
} else {
    $total_produk = 0;
}

// TOTAL KATEGORI
$q_kategori = $mysqli->query("SELECT COUNT(*) AS total FROM kategori");
if ($q_kategori) {
    $row_kategori = $q_kategori->fetch_assoc();
    $total_kategori = $row_kategori['total'];
} else {
    $total_kategori = 0;
}

// TRANSAKSI HARI INI
$hari_ini = date("Y-m-d");
$q_transaksi = $mysqli->query("SELECT COUNT(*) AS total FROM transaksi WHERE DATE(tanggal) = '$hari_ini'");
$transaksi_hari_ini = $q_transaksi->fetch_assoc()['total'];

// PENDAPATAN HARI INI
$q_pendapatan = $mysqli->query("SELECT SUM(total_harga) AS total FROM transaksi WHERE DATE(tanggal) = '$hari_ini'");
$pendapatan_hari_ini = $q_pendapatan->fetch_assoc()['total'] ?? 0;

// TRANSAKSI TERBARU
$transaksi_terbaru = $mysqli->query("
    SELECT t.*, td.jumlah, b.nama 
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN barang b ON td.id_barang = b.id
    ORDER BY t.id DESC
    LIMIT 5
");

// PRODUK TERLARIS
$produk_terlaris = $mysqli->query("
    SELECT b.nama, b.kategori, SUM(td.jumlah) AS terjual
    FROM transaksi_detail td
    JOIN barang b ON td.id_barang = b.id
    GROUP BY td.id_barang
    ORDER BY terjual DESC
    LIMIT 5
");

// ================= OMSET HARIAN (30 HARI) ===================
$q_harian = $mysqli->query("
    SELECT DATE(tanggal) AS tgl, SUM(total_harga) AS total
    FROM transaksi
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(tanggal)
    ORDER BY tgl ASC
");

$label_harian = [];
$value_harian = [];

while ($d = $q_harian->fetch_assoc()) {
    $label_harian[] = $d['tgl'];
    $value_harian[] = $d['total'];
}

// ================= OMSET BULANAN (12 BULAN) ===================
$q_bulanan = $mysqli->query("
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') AS bln,
        SUM(total_harga) AS total
    FROM transaksi
    GROUP BY bln
    ORDER BY bln ASC
    LIMIT 12
");


$label_bulanan = [];
$value_bulanan = [];

while ($m = $q_bulanan->fetch_assoc()) {
    $label_bulanan[] = $m['bln'];
    $value_bulanan[] = $m['total'];
}

// Convert ke JSON
$label_harian_json = json_encode($label_harian);
$value_harian_json = json_encode($value_harian);
$label_bulanan_json = json_encode($label_bulanan);
$value_bulanan_json = json_encode($value_bulanan);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Toko Berkah Jaya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        /* ===================== Dashboard UI ======================= */

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        /* Sidebar */
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

        /* Content */
        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .card-box {
            background: white\\;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .big-number {
            font-size: 30px;
            font-weight: bold;
        }

        .list-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .list-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        /* Warna untuk kartu statistik */
        .stat-blue {
            background: linear-gradient(135deg, #0d6efd, #66a3ff);
            color: white;
        }

        .stat-green {
            background: linear-gradient(135deg, #28a745, #6fdc7a);
            color: white;
        }

        .stat-yellow {
            background: linear-gradient(135deg, #ffc107, #ffdd57);
            color: #333;
        }

        .stat-red {
            background: linear-gradient(135deg, #dc3545, #ff6b81);
            color: white;
        }

        .card-box .big-number {
            font-size: 32px;
            font-weight: 700;
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


        /* =================== AI Chat Floating ====================== */

        /* Floating Button */
        #ai-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            font-size: 28px;
        }

        /* Chat Window */
        #ai-window {
            position: fixed;
            bottom: 95px;
            right: 25px;
            width: 360px;
            height: 520px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
        }

        /* Header */
        .ai-header {
            background: #0d6efd;
            color: white;
            padding: 14px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Body */
        .ai-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background: #f1f5ff;
        }

        /* AI Bubble */
        .ai-msg-ai {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .ai-bot-icon {
            width: 30px;
            height: 30px;
            background: #222;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
        }

        .ai-msg-ai .bubble {
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        /* User Bubble */
        .ai-msg-user {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .ai-msg-user .bubble {
            background: #d7ebff;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        /* Input */
        .ai-input-box {
            padding: 12px;
            display: flex;
            gap: 8px;
            background: white;
            border-top: 1px solid #ddd;
        }

        .ai-input-box input {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .ai-send {
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 15px;
        }

        /* Typing animation */
        .typing span {
            height: 6px;
            width: 6px;
            background: #555;
            display: inline-block;
            border-radius: 50%;
            margin-right: 3px;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: .2
            }

            50% {
                opacity: 1
            }

            100% {
                opacity: .2
            }
        }

        .typing span {
            height: 6px;
            width: 6px;
            background: #333;
            display: inline-block;
            border-radius: 50%;
            margin-right: 3px;
            animation: blink 1s infinite;
        }

        .typing span:nth-child(2) {
            animation-delay: .2s;
        }

        .typing span:nth-child(3) {
            animation-delay: .4s;
        }

        @keyframes blink {
            0% {
                opacity: .2;
                transform: translateY(0);
            }

            50% {
                opacity: 1;
                transform: translateY(-3px);
            }

            100% {
                opacity: .2;
                transform: translateY(0);
            }
        }

        /* Efek Hover */
        .card-box {
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .card-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Animasi angka */
        .big-number[data-count] {
            opacity: 0;
            transform: translateY(10px);
            transition: .3s ease;
        }

        /* Kotak nomor produk terlaris */
        .rank-box {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0d6efd, #4a90e2);
            color: white;
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
        }

        /* Nama produk */
        .top-product-name {
            font-weight: 700;
            font-size: 15px;
        }

        .list-box .list-item span {
            font-weight: normal !important;
        }

        .section-title {
            background: linear-gradient(135deg, #0d6efd, #69a6ff);
            color: white;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        /* Welcome header */
        .welcome-header {
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

        /* Comparison text */
        .comparison {
            font-size: 11px;
            opacity: 0.85;
            display: block;
            margin-top: 4px;
        }

        /* Card hover effect */
        .card-box {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .card-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>


</head>

<body>

    <!-- =================== SIDEBAR =================== -->
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

            <a href="transaksi.php"><i class="bi bi-cash-coin"></i> Transaksi</a>
            <a href="laporan_transaksi.php"><i class="bi bi-file-earmark-text"></i> Laporan Transaksi</a>

        <?php endif; ?>


        <a href="logout.php" class="text-warning mt-auto pt-4 border-top border-white border-opacity-10"><i
                class="bi bi-box-arrow-right"></i> Logout</a>
    </div>


    <!-- =================== CONTENT =================== -->

    <div class="content">

        <!-- WELCOME HEADER -->
        <div class="welcome-header">
            <h3><?= $greeting_icon ?> Selamat <?= $greeting ?>, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
            <p><?= date('d F Y, H:i') ?> WIB</p>
        </div>

        <div class="row g-3">

            <div class="col-md-3">
                <div class="card-box stat-blue d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold">Total Produk</div>
                        <div class="big-number"><?= $total_produk ?></div>
                    </div>
                    <i class="bi bi-box-seam" style="font-size:40px; opacity:0.7;"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box stat-green d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold">Total Kategori</div>
                        <div class="big-number"><?= $total_kategori ?></div>
                    </div>
                    <i class="bi bi-tags" style="font-size:40px; opacity:0.7;"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box stat-yellow d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold">Transaksi Hari Ini</div>
                        <div class="big-number"><?= $transaksi_hari_ini ?></div>
                    </div>
                    <i class="bi bi-cash-coin" style="font-size:40px; opacity:0.7;"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box stat-red d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold">Pendapatan Hari Ini</div>
                        <div class="big-number">Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></div>
                    </div>
                    <i class="bi bi-currency-dollar" style="font-size:40px; opacity:0.7;"></i>
                </div>
            </div>

        </div>

        <div class="row mt-4 g-4">

            <div class="col-md-8">
                <div class="list-box">
                    <h5 class="section-title"><i class="bi bi-receipt"></i> Transaksi Terbaru</h5>

                    <?php if ($transaksi_terbaru->num_rows == 0): ?>
                        <div class="text-center text-muted py-5">Belum ada transaksi</div>
                    <?php else: ?>
                        <?php while ($t = $transaksi_terbaru->fetch_assoc()): ?>
                            <div class="list-item">
                                <span><?= $t['nama'] ?></span>
                                <span class="float-end"><?= date('d M Y', strtotime($t['tanggal'])) ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="list-box">
                    <h5 class="section-title"><i class="bi bi-stars"></i> Produk Terlaris</h5>

                    <?php if ($produk_terlaris->num_rows == 0): ?>
                        <div class="text-center text-muted py-5">Belum ada data</div>
                    <?php else: ?>
                        <?php $no = 1;
                        while ($p = $produk_terlaris->fetch_assoc()): ?>
                            <div class="list-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="rank-box me-2"><?= $no++ ?></div>
                                    <span><?= $p['nama'] ?></span>
                                </div>
                                <div class="terjual-badge">
                                    <?= $p['terjual'] ?> terjual
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <h5 class="fw-bold">Grafik Omzet Harian & Bulanan</h5>
                <div class="card-box mt-2">
                    <canvas id="grafikOmset" height="130"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>

        // Show / hide AI chat
        function toggleAI() {
            let win = document.getElementById("ai-window");
            win.style.display = (win.style.display === "flex") ? "none" : "flex";
        }

        // Scroll always bottom
        function scrollAI() {
            let c = document.getElementById("ai-body");
            c.scrollTop = c.scrollHeight;
        }

        // Tambahkan pesan user
        function addUserBubble(text) {
            let body = document.getElementById("ai-body");
            body.innerHTML += `
        <div class="ai-msg-user">
            <div class="bubble">${text}</div>
        </div>`;
            scrollAI();
        }

        // Tambahkan pesan AI
        function addAIBubble(text) {
            let body = document.getElementById("ai-body");
            body.innerHTML += `
        <div class="ai-msg-ai">
            <div class="ai-bot-icon"><i class="bi bi-robot"></i></div>
            <div class="bubble">${text}</div>
        </div>`;
            scrollAI();
        }

        // Typing effect AI
        function typeAIMessage(fullText) {
            let body = document.getElementById("ai-body");

            let wrap = document.createElement("div");
            wrap.className = "ai-msg-ai";
            wrap.innerHTML = `
        <div class="ai-bot-icon"><i class="bi bi-robot"></i></div>
        <div class="bubble"></div>
    `;
            body.appendChild(wrap);

            let bubble = wrap.querySelector(".bubble");

            let i = 0;
            function type() {
                if (i < fullText.length) {
                    bubble.innerHTML += fullText[i];
                    i++;
                    scrollAI();
                    setTimeout(type, 15);
                }
            }
            type();
        }

        // Kirim pesan ke server
        function sendAI() {

            let input = document.getElementById("ai-input");
            let text = input.value.trim();
            if (!text) return;

            addUserBubble(text);
            input.value = "";

            // Typing animation dummy
            let body = document.getElementById("ai-body");
            let typing = document.createElement("div");
            typing.className = "ai-msg-ai";
            typing.id = "typing";
            typing.innerHTML = `
        <div class="ai-bot-icon"><i class="bi bi-robot"></i></div>
        <div class="bubble"><span class="typing"><span></span><span></span><span></span></span></div>`;
            body.appendChild(typing);
            scrollAI();

            // Send to backend
            fetch("ai/ai_chat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ prompt: text })
            })
                .then(r => r.json())
                .then(res => {

                    typing.remove(); // remove dummy loading

                    typeAIMessage(res.reply);
                });
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById("grafikOmset").getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: <?= $label_harian_json ?>, // default tampil harian
                datasets: [
                    {
                        label: "Omset Harian (30 hari)",
                        data: <?= $value_harian_json ?>,
                        borderColor: "#0d6efd",
                        backgroundColor: "rgba(13,110,253,0.25)",
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                    },
                    {
                        label: "Omset Bulanan (12 bulan)",
                        data: <?= $value_bulanan_json ?>,
                        borderColor: "#dc3545",
                        backgroundColor: "rgba(220,53,69,0.25)",
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        yAxisID: "y1" // pakai skala berbeda
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                stacked: false,
                scales: {
                    y: {
                        type: "linear",
                        position: "left",
                        title: { display: true, text: "Omset Harian" }
                    },
                    y1: {
                        type: "linear",
                        position: "right",
                        title: { display: true, text: "Omset Bulanan" },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    </script>

</body>

</html>