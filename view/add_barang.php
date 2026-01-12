<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #74ABE2, #5563DE);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 450px;
            border: none;
            border-radius: 18px;
            background: #ffffffee;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(90deg, #5563DE, #74ABE2);
            color: white;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 600;
            padding: 15px;
        }

        .btn-box {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="card-header">Tambah Barang Baru</div>

        <div class="card-body p-4">

            <form action="../proses/proses_add.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" class="form-control" name="harga" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah / Stok</label>
                    <input type="number" class="form-control" name="jumlah" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori Barang</label>
                    <select class="form-select" name="kategori" required>
                        <option disabled selected>Pilih kategori</option>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="rokok">Rokok</option>
                        <option value="perlengkapan">Perlengkapan Rumah</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Barang</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                </div>

                <div class="btn-box">
                    <button type="submit" class="btn btn-success w-50">Simpan</button>
                    <a href="../index.php" class="btn btn-secondary w-50">Kembali</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>