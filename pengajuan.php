<?php
require_once 'koneksi.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik           = htmlspecialchars(trim($_POST['nik'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nama_pemohon  = htmlspecialchars(trim($_POST['nama_pemohon'] ?? ''), ENT_QUOTES, 'UTF-8');
    $no_hp         = htmlspecialchars(trim($_POST['no_hp'] ?? ''), ENT_QUOTES, 'UTF-8');
    $jenis_surat   = htmlspecialchars(trim($_POST['jenis_surat'] ?? ''), ENT_QUOTES, 'UTF-8');
    $keterangan    = htmlspecialchars(trim($_POST['keterangan'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    $tanggal_masuk = date('Y-m-d');
    $isi_ringkas   = "NIK: {$nik} | No.HP: {$no_hp} | Keperluan: {$keterangan}";

    // Handle Upload Berkas
    $nama_file_lampiran = null;
    if (!empty($_FILES['lampiran']['name'])) {
        $file_name = $_FILES['lampiran']['name'];
        $file_tmp  = $_FILES['lampiran']['tmp_name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed)) {
            $nama_file_lampiran = "surat_" . bin2hex(random_bytes(6)) . "_" . time() . "." . $ext;
            $target_dir = "uploads/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (!move_uploaded_file($file_tmp, $target_dir . $nama_file_lampiran)) {
                $message = "Gagal mengunggah berkas lampiran!";
                $message_type = "danger";
            }
        } else {
            $message = "Format berkas tidak didukung! Gunakan format PDF, JPG, atau PNG.";
            $message_type = "danger";
        }
    }

    // Simpan ke Database
    if (empty($message)) {
        try {
            $sql = "INSERT INTO surat_masuk (tanggal_masuk, pengirim, perihal, isi_ringkas, lampiran) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $tanggal_masuk,
                $nama_pemohon,
                $jenis_surat,
                $isi_ringkas,
                $nama_file_lampiran
            ]);

            $message = "Pengajuan surat berhasil dikirim dan terdata di Surat Masuk!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Gagal menyimpan pengajuan ke sistem: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Surat Keterangan - Sipersa Desa</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-card {
            max-width: 680px;
            margin: 40px auto;
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .header-box {
            background-color: #1e2229;
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 25px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card form-card bg-white">
        <div class="header-box">
            <h4 class="fw-bold m-0">Layanan Pengajuan Surat</h4>
            <p class="small text-muted mb-0 mt-1">Sipersa Sistem Pelayanan Surat Desa</p>
        </div>
        
        <div class="card-body p-4 p-md-5">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                    <?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pemohon" class="form-control" placeholder="Nama sesuai KTP" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Jenis Surat Keterangan <span class="text-danger">*</span></label>
                    <select name="jenis_surat" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Jenis Surat --</option>
                        <option value="Surat Keterangan Usaha (SKU)">Surat Keterangan Usaha (SKU)</option>
                        <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                        <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                        <option value="Surat Keterangan Beda Nama">Surat Keterangan Beda Nama</option>
                        <option value="Surat Pengantar SKCK">Surat Pengantar SKCK</option>
                        <option value="Surat Keterangan Bepergian / Pindah">Surat Keterangan Bepergian / Pindah</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Keperluan / Keterangan Tambahan <span class="text-danger">*</span></label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan secara singkat tujuan/keperluan pembuatan surat..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Lampiran Pendukung (KTP/KK)</label>
                    <input type="file" name="lampiran" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
                    <div class="form-text small">Format: PDF, JPG, atau PNG (Maksimal 2MB)</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark fw-bold py-2">Kirim Pengajuan Surat</button>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>