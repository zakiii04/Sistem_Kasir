<?php
include "../conn/koneksi.php";

$q = strtolower($_GET['q'] ?? '');

$data = [];

$result = $mysqli->query("
    SELECT nama FROM barang 
    WHERE nama LIKE '%$q%' 
    ORDER BY nama ASC LIMIT 10
");

while ($row = $result->fetch_assoc()) {
    $data[] = $row['nama'];
}

echo json_encode($data);
