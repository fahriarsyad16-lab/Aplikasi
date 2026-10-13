<?php
require 'koneksi.php';

// Proses Simpan Data Surat Masuk Baru jika Tombol Diklik
if (isset($_POST['simpan_masuk'])) {
    $nomor_surat   = $_POST['nomor_surat'];
    $asal_surat    = $_POST['asal_surat'];
    $perihal       = $_POST['perihal'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $tanggal_terima= date('Y-m-d'); // Otomatis tanggal hari ini

    try {
        $sql = "INSERT INTO surat_masuk (nomor_surat, asal_surat, perihal, tanggal_surat, tanggal_terima) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomor_surat, $asal_surat, $perihal, $tanggal_surat, $tanggal_terima]);
        echo "<script>alert('Surat masuk dari " . htmlspecialchars($asal_surat) . " berhasil dicatat!'); window.location='surat_masuk.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menyimpan data! Periksa kembali inputan Anda.');</script>";
    }
}

// Ambil Semua Data Surat Masuk
$daftarSuratMasuk = $pdo->query("SELECT * FROM surat_masuk ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Masuk Desa</title>
    <link href="bootstrap.min.css" rel="stylesheet" type="text/css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 240px; background-color: #212529; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h4 { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #444; padding-bottom: 15px; color: #fff; }
        .sidebar a { display: block; color: #bbb; padding: 12px; text-decoration: none; border-radius: 4px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background-color: #343a40; color: white; }
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #dee2e6; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .card-table { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; font-size: 14px; }
        th { background-color: #f8f9fa; color: #555; }
        .form-control { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background-color: #198754; color: white; border: none; padding: 10px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <!-- Menu Samping -->
    <!-- Menu Samping di file surat_masuk.php -->
    <!-- Menu Samping di file surat_masuk.php -->
    <!-- Menu Samping di file surat_masuk.php -->
     <div class="sidebar">
        <h4>Sipersa Desa</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="surat_keluar.php">Surat Keluar</a>
        <a href="surat_masuk.php">Surat Masuk</a>
        <a href="data_penduduk.php">Data Penduduk</a>
        <a href="dashboard.php" style="background-color: #495057; color: white; text-align: center; margin-top: 30px; font-weight: bold;">← Ke Dashboard</a>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="header">
            <h1>Arsip Surat Masuk</h1>
            <span style="color: #666; font-weight: bold;">Sistem Lokal Perangkat Desa</span>
        </div>

        <!-- Formulir Input Surat Masuk -->
        <div class="card-table">
            <h5 style="margin-top:0;">Registrasi Surat Masuk Baru</h5>
            <form method="POST" action="">
                <div style="display:flex; gap:15px;">
                    <div style="flex:1;">
                        <label style="font-size:13px; font-weight:bold;">Nomor Surat *</label>
                        <input type="text" name="nomor_surat" class="form-control" required placeholder="Contoh: 005/122/Kec/2026">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:13px; font-weight:bold;">Asal Instansi/Pengirim *</label>
                        <input type="text" name="asal_surat" class="form-control" required placeholder="Contoh: Kantor Kecamatan, Dinas Sosial">
                    </div>
                </div>
                <div style="display:flex; gap:15px;">
                    <div style="flex:2;">
                        <label style="font-size:13px; font-weight:bold;">Perihal / Isi Ringkas *</label>
                        <input type="text" name="perihal" class="form-control" required placeholder="Contoh: Undangan Rapat Koordinasi">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:13px; font-weight:bold;">Tanggal Surat dibuat *</label>
                        <input type="date" name="tanggal_surat" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="simpan_masuk" class="btn-submit">Arsipkan Surat Masuk</button>
            </form>
        </div>

        <!-- Tabel Daftar Surat Masuk -->
        <div class="card-table">
            <h5>Daftar Surat Masuk Desa</h5>
            <table>
                <thead>
                    <tr>
                        <th>No. Surat</th>
                        <th>Asal Surat</th>
                        <th>Perihal / Perkara</th>
                        <th>Tanggal Surat</th>
                        <th>Tanggal Terima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarSuratMasuk)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">Belum ada data surat masuk.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarSuratMasuk as $sm): ?>
                        <tr>
                            <td style="font-weight: bold; color: #555;"><?= htmlspecialchars($sm['nomor_surat']); ?></td>
                            <td><?= htmlspecialchars($sm['asal_surat']); ?></td>
                            <td><?= htmlspecialchars($sm['perihal']); ?></td>
                            <td><?= date('d-m-Y', strtotime($sm['tanggal_surat'])); ?></td>
                            <td><span class="badge" style="background-color: #e2e3e5; color: #41464b;"><?= date('d-m-Y', strtotime($sm['tanggal_terima'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>