<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
require 'koneksi.php';

if (isset($_POST['simpan_surat'])) {
    $nik_pemohon = trim($_POST['nik_pemohon']);
    $jenis_surat = htmlspecialchars(trim($_POST['jenis_surat']));
    $nomor_surat = htmlspecialchars(trim($_POST['nomor_surat'])) ?: null;
    $status      = $_POST['status'];
    $id_user     = $_SESSION['user_id']; 

    $stmt = $pdo->prepare("SELECT nama FROM penduduk WHERE nik = ?");
    $stmt->execute([$nik_pemohon]);
    $penduduk = $stmt->fetch();

    if ($penduduk) {
        $sql = "INSERT INTO surat_keluar (nomor_surat, nik_pemohon, jenis_surat, status, id_user, tanggal_keluar) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($sql);
        $stmtInsert->execute([$nomor_surat, $nik_pemohon, $jenis_surat, $status, $id_user, date('Y-m-d')]);
        echo "<script>alert('Surat berhasil didaftarkan!'); window.location='surat_keluar.php';</script>";
    } else {
        echo "<script>alert('Gagal! NIK tidak ditemukan di database data penduduk.');</script>";
    }
}

$query = "SELECT sk.*, p.nama AS nama_warga FROM surat_keluar sk JOIN penduduk p ON sk.nik_pemohon = p.nik ORDER BY sk.id DESC LIMIT 10";
$daftarSurat = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keluar - Sipersa</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; display: flex; margin:0; overflow-x: hidden; }
        
        /* SIDEBAR GELAP HARMONIS */
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

    <div class="sidebar">
        <h4>Sipersa Desa</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="surat_keluar.php" class="active">Surat Keluar</a>
        <a href="surat_masuk.php">Surat Masuk</a>
        <a href="data_penduduk.php">Data Penduduk</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h3 class="fw-bold m-0 text-dark">Manajemen Surat Keluar</h3>
            <span class="badge bg-secondary p-2 fw-semibold">Sistem Pelayanan Surat</span>
        </div>

        <!-- Form Input -->
        <div class="card-table">
            <h5 class="fw-bold text-dark mb-4">Form Input Surat Keluar Baru</h5>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">NIK Warga Pemohon *</label>
                        <input type="text" name="nik_pemohon" class="form-control" placeholder="Contoh NIK: 1234567890123456" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Jenis Surat Keterangan *</label>
                        <input type="text" name="jenis_surat" class="form-control" placeholder="Contoh: Domisili, SKU, SKTM" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nomor Surat Keluar</label>
                        <input type="text" name="nomor_surat" class="form-control" placeholder="Contoh: 140/02/III/2026">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label small fw-bold">Status Surat *</label>
                        <select name="status" class="form-select">
                            <option value="disetujui">Disetujui (Siap Cetak)</option>
                            <option value="pending">Pending (Menunggu Kades)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="simpan_surat" class="btn btn-success fw-bold px-4">Simpan & Daftarkan Surat</button>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <div class="card-table">
            <h5 class="fw-bold text-dark mb-3">Riwayat Cetak & Pengajuan Surat Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small m-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th>No. Surat</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Status</th>
                            <th class="text-center">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarSurat)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada riwayat pengajuan surat.</td></tr>
                        <?php else: ?>
                            <?php foreach ($daftarSurat as $surat): ?>
                            <tr>
                                <td class="text-secondary"><?= htmlspecialchars($surat['nomor_surat'] ?? 'Belum Diisi'); ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($surat['nama_warga']); ?></td>
                                <td><span class="badge bg-secondary px-2 py-1.5"><?= htmlspecialchars($surat['jenis_surat']); ?></span></td>
                                <td>
                                    <span class="badge <?= $surat['status'] == 'disetujui' ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                                        <?= ucfirst($surat['status']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="ekspor_word.php?id=<?= $surat['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold">Unduh Word (.doc)</a>
                                </td>
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