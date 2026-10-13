<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit; 
}
require 'koneksi.php';

// Proses Simpan Data Penduduk Baru jika Tombol Diklik
if (isset($_POST['simpan_penduduk'])) {
    $nik          = htmlspecialchars(trim($_POST['nik']), ENT_QUOTES, 'UTF-8');
    $nama         = htmlspecialchars(trim($_POST['nama']), ENT_QUOTES, 'UTF-8');
    $tempat_lahir = htmlspecialchars(trim($_POST['tempat_lahir']), ENT_QUOTES, 'UTF-8');
    $tgl_lahir    = $_POST['tgl_lahir'];
    $jk           = $_POST['jk'];
    $alamat       = htmlspecialchars(trim($_POST['alamat']), ENT_QUOTES, 'UTF-8');
    $rt           = htmlspecialchars(trim($_POST['rt']), ENT_QUOTES, 'UTF-8');
    $rw           = htmlspecialchars(trim($_POST['rw']), ENT_QUOTES, 'UTF-8');
    $agama        = htmlspecialchars(trim($_POST['agama']), ENT_QUOTES, 'UTF-8');
    $status_kawin = $_POST['status_kawin'];

    try {
        $sql = "INSERT INTO penduduk (nik, nama, tempat_lahir, tgl_lahir, jk, alamat, rt, rw, agama, status_kawin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nik, $nama, $tempat_lahir, $tgl_lahir, $jk, $alamat, $rt, $rw, $agama, $status_kawin]);
        echo "<script>alert('Data warga berhasil ditambahkan!'); window.location='data_penduduk.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal! NIK sudah terdaftar atau terjadi kesalahan sistem.');</script>";
    }
}

// Ambil Semua Data Penduduk
$daftarPenduduk = $pdo->query("SELECT * FROM penduduk ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk - Sipersa</title>
    <!-- MENGGUNAKAN FILE LOKAL ANDA -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; display: flex; margin:0; overflow-x: hidden; }
        
        /* SIDEBAR GELAP HARMONIS (Sama seperti dashboard & surat keluar) */
        .sidebar { 
            width: 260px; 
            background-color: #1e2229 !important; 
            color: #dee2e6 !important; 
            min-height: 100vh; 
            padding: 25px 20px; 
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h4 { text-align: center; margin-bottom: 25px; border-bottom: 1px solid #3a4149; padding-bottom: 15px; color: #fff !important; font-weight: 600; }
        .sidebar a { display: block; color: #adb5bd !important; padding: 12px 15px; text-decoration: none !important; border-radius: 6px; margin-bottom: 8px; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background-color: #2c313c !important; color: #fff !important; font-weight: 500; }
        
        .main-content { flex: 1; padding: 30px; min-width: 0; }
        .card-table { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 25px; border: none; }
    </style>
</head>
<body>

    <!-- Menu Samping Gelap Pekat -->
    <div class="sidebar">
        <h4>Sipersa Desa</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="surat_keluar.php">Surat Keluar</a>
        <a href="surat_masuk.php">Surat Masuk</a>
        <a href="data_penduduk.php" class="active">Data Penduduk</a>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h3 class="fw-bold m-0 text-dark">Manajemen Data Penduduk</h3>
            <span class="badge bg-secondary p-2 fw-semibold">Sistem Lokal Perangkat Desa</span>
        </div>

        <!-- Formulir Input Penduduk Baru -->
        <div class="card-table">
            <h5 class="fw-bold text-dark mb-4">Tambah Data Warga Baru</h5>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">NIK (16 Digit) *</label>
                        <input type="text" name="nik" maxlength="16" class="form-control" required placeholder="Masukkan NIK">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Sesuai KTP">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota Lahir">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Jenis Kelamin</label>
                        <select name="jk" class="form-select">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Alamat Jalan/Dusun</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Nama Jalan atau Kampung">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold">RT</label>
                        <input type="text" name="rt" maxlength="3" class="form-control" placeholder="001">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold">RW</label>
                        <input type="text" name="rw" maxlength="3" class="form-control" placeholder="002">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label small fw-bold">Agama</label>
                        <input type="text" name="agama" class="form-control" placeholder="Islam/Kristen/dll">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label small fw-bold">Status Perkawinan</label>
                        <select name="status_kawin" class="form-select">
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai">Cerai</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="simpan_penduduk" class="btn btn-primary fw-bold px-4">Simpan Data Warga</button>
            </form>
        </div>

        <!-- Tabel Daftar Warga Tanpa Karakter <> -->
        <div class="card-table">
            <h5 class="fw-bold text-dark mb-3">Daftar Seluruh Penduduk Desa</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small m-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th>NIK</th>
                            <th>Nama Warga</th>
                            <th>L/P</th>
                            <th>TTL</th>
                            <th>Alamat Lengkap</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarPenduduk)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data penduduk terdaftar.</td></tr>
                        <?php else: ?>
                            <?php foreach ($daftarPenduduk as $p): ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= htmlspecialchars($p['nik']); ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($p['nama']); ?></td>
                                <td><?= htmlspecialchars($p['jk']); ?></td>
                                <td><?= htmlspecialchars($p['tempat_lahir'] . ', ' . $p['tgl_lahir']); ?></td>
                                <td><?= htmlspecialchars($p['alamat'] . ' RT ' . $p['rt'] . ' / RW ' . $p['rw']); ?></td>
                                <td><span class="badge bg-secondary px-2 py-1.5"><?= htmlspecialchars($p['status_kawin']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
