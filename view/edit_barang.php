<?php
include "../conn/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id']);
$q = $mysqli->query("SELECT * FROM barang WHERE id = $id");

if ($q->num_rows == 0) {
    echo "<script>alert('Barang tidak ditemukan'); window.location='../index.php';</script>";
    exit;
}

$data = $q->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #74ABE2, #5563DE);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 420px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: #5563DE;
            color: white;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="card p-3">
        <div class="card-header">Edit Barang</div>

        <div class="card-body">

            <form action="../proses/proses_edit.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <input type="hidden" name="gambar_lama" value="<?= $data['gambar'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" name="nama" value="<?= $data['nama'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" class="form-control" name="harga" value="<?= $data['harga'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah / Stok</label>
                    <input type="number" class="form-control" name="jumlah" value="<?= $data['jumlah'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-select" name="kategori" required>
                        <option value="makanan" <?= $data['kategori'] == "makanan" ? "selected" : "" ?>>Makanan</option>
                        <option value="minuman" <?= $data['kategori'] == "minuman" ? "selected" : "" ?>>Minuman</option>
                        <option value="rokok" <?= $data['kategori'] == "rokok" ? "selected" : "" ?>>Rokok</option>
                        <option value="perlengkapan" <?= $data['kategori'] == "perlengkapan" ? "selected" : "" ?>>Perlengkapan
                            Rumah</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Barang</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*">

                    <div class="mt-2">
                        <small>Gambar saat ini:</small><br>
                        <img src="../uploads/<?= $data['gambar'] ?>" width="120" class="rounded shadow">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                    <a href="../index.php" class="btn btn-secondary">Kembali</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>