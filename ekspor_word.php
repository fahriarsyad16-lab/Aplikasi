<?php
session_start();
if (!isset($_SESSION['user_id'])) { die("Akses ditolak."); }
require 'koneksi.php';

$id = $_GET['id'] ?? 0;
$query = "SELECT sk.*, p.* FROM surat_keluar sk JOIN penduduk p ON sk.nik_pemohon = p.nik WHERE sk.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) { die("Data surat tidak ditemukan."); }

$nama_file = "Surat_" . str_replace(' ', '_', $data['jenis_surat']) . "_" . $data['nik'] . ".doc";
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=" . $nama_file);
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://w3.org'>
<head>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; }
        .kopsurat { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 5px; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 5px; }
        .nomor { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="kopsurat">
        <h3 style="margin:0;">PEMERINTAH KABUPATEN INDONESIA</h3>
        <h2 style="margin:0;">KECAMATAN PILIHAN</h2>
        <h1 style="margin:0;">KANTOR KEPALA DESA SEJAHTERA</h1>
        <p style="margin:0; font-size: 10pt;">Alamat: Jl. Raya Utama No. 01 Kode Pos 12345</p>
    </div>

    <div class="judul">SURAT KETERANGAN <?= htmlspecialchars($data['jenis_surat']); ?></div>
    <div class="nomor">Nomor: <?= htmlspecialchars($data['nomor_surat'] ?? '..../..../....'); ?></div>

    <p>Yang bertanda tangan di bawah ini Kepala Desa Sejahtera, Kecamatan Pilihan, menerangkan dengan sebenarnya bahwa:</p>
    
    <table style="margin-left: 30px; margin-bottom: 20px;">
        <tr><td>Nama</td><td>:</td><td><strong><?= htmlspecialchars($data['nama']); ?></strong></td></tr>
        <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($data['nik']); ?></td></tr>
        <tr><td>Tempat/Tgl Lahir</td><td>:</td><td><?= htmlspecialchars($data['tempat_lahir'] . ', ' . $data['tgl_lahir']); ?></td></tr>
        <tr><td>Jenis Kelamin</td><td>:</td><td><?= $data['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td><?= htmlspecialchars($data['alamat'] . ' RT ' . $data['rt'] . ' / RW ' . $data['rw']); ?></td></tr>
    </table>

    <p>Orang tersebut di atas adalah benar-benar warga Desa Sejahtera yang bertempat tinggal di alamat tersebut dan sepanjang sepengetahuan kami berperilaku baik.</p>
    <p>Demikian surat keterangan ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    <br><br>
    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td align="center">
                Desa Sejahtera, <?= date('d M Y'); ?><br>
                Kepala Desa Sejahtera<br><br><br><br>
                ( .................................... )
            </td>
        </tr>
    </table>
</body>
</html>