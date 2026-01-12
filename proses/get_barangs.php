<?php
include_once("./conn/koneksi.php");

$result = mysqli_query($mysqli, "SELECT * FROM barang ORDER BY id ASC");

$barang = [];

while($row = mysqli_fetch_assoc($result)){
    $barangs[] = $row;
}

?>