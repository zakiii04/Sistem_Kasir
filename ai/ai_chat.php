<?php
header("Content-Type: application/json");
include "../conn/koneksi.php";

// Ambil input user
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$ask  = strtolower(trim($data["prompt"] ?? ""));

if ($ask == "") {
    echo json_encode(["reply" => "Pertanyaannya kosong."]);
    exit;
}

/* Fungsi bantu */
function getPendapatanHariIni($db) {
    $hari = date("Y-m-d");
    $q = $db->query("SELECT SUM(total_harga) AS total FROM transaksi WHERE DATE(tanggal)='$hari'");
    return $q->fetch_assoc()['total'] ?? 0;
}

function totalProduk($db) {
    return $db->query("SELECT COUNT(*) AS t FROM barang")->fetch_assoc()['t'];
}

function produkTerlaris($db) {
    $q = $db->query("
        SELECT b.nama, SUM(td.jumlah) AS terjual
        FROM transaksi_detail td
        JOIN barang b ON b.id = td.id_barang
        GROUP BY td.id_barang
        ORDER BY terjual DESC
        LIMIT 5
    ");

    $out = "";
    $no = 1;

    while($d = $q->fetch_assoc()) {
        $out .= "$no. {$d['nama']} — {$d['terjual']} terjual\n";
        $no++;
    }
    return $out ?: "Belum ada data penjualan.";
}

/* RULE ENGINE */
$reply = "Saya tidak mengerti. Coba tanya: pendapatan hari ini, stok, produk terlaris, laporan.";

// Pendapatan hari ini
if (strpos($ask, "pendapatan hari ini") !== false) {
    $rp = getPendapatanHariIni($mysqli);
    $reply = "Pendapatan hari ini: Rp ".number_format($rp,0,',','.');
}

// Total produk
elseif (strpos($ask, "total produk") !== false) {
    $reply = "Total produk saat ini: ".totalProduk($mysqli)." item.";
}

// Produk terlaris
elseif (strpos($ask, "produk terlaris") !== false) {
    $reply = "Top 5 produk terlaris:\n\n".produkTerlaris($mysqli);
}

echo json_encode(["reply" => $reply]);
