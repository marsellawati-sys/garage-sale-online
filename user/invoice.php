<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

$uid = $_SESSION['id'];

// Ambil data profil pembeli
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($query_user);

// Tangkap list id item belanjaan yang tersimpan dari halaman payment tadi
$item_ids = $_SESSION['last_invoice_items'] ?? [];

$total_produk = 0;
$items_invoice = [];

if (!empty($item_ids)) {
    $ids_string = implode(',', $item_ids);
    // Ambil detail nama barang & harga dari database berdasarkan apa yang baru dibeli
    $query_p = mysqli_query($con, "SELECT productName, productPrice FROM products WHERE id IN ($ids_string)");
    while ($row = mysqli_fetch_array($query_p)) {
        $items_invoice[] = $row;
        $total_produk += (float)$row['productPrice'];
    }
}

// Hitung ongkir invoice sesuai data kota/provinsi tujuan
$wilayah_gabung = $user['shippingCity'] ?? '';
$provinsi_tampil = "Luar Jawa";
if (!empty($wilayah_gabung)) {
    $pecah = explode(',', $wilayah_gabung);
    $provinsi_tampil = trim(end($pecah));
}

$daftar_jawa = ['banten', 'dki jakarta', 'jakarta', 'jawa barat', 'jawa tengah', 'di yogyakarta', 'yogyakarta', 'jawa timur'];
$ongkir = (in_array(strtolower(trim($provinsi_tampil)), $daftar_jawa)) ? 15000 : 45000;
$grand_total = $total_produk + $ongkir;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran - GarageSale</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; color: #333; padding: 40px 0; }
        .invoice-card { background: #fff; border: 1px solid #eef0f2; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); padding: 40px; max-width: 800px; margin: 0 auto; }
        .invoice-header { border-bottom: 2px dashed #e8e4d8; padding-bottom: 25px; margin-bottom: 25px; }
        .brand-name { font-weight: 900; font-size: 28px; letter-spacing: -1px; color: #111; }
        .invoice-title { font-weight: 800; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; color: #888; }
        .section-title { font-weight: 800; font-size: 13px; text-transform: uppercase; color: #111; margin-bottom: 10px; border-bottom: 1px solid #111; padding-bottom: 4px; }
        .table-invoice th { font-weight: 800; text-transform: uppercase; font-size: 12px; color: #666; background: #faf9f5; border: none; }
        .table-invoice td { font-weight: 600; font-size: 14px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; padding: 12px 8px; }
        .grand-total-box { background: #111; color: #fff; border-radius: 14px; padding: 15px 20px; font-weight: 800; font-size: 18px; }
        .no-print { display: flex; gap: 10px; justify-content: center; margin-top: 30px; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-card { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <div class="brand-name">GarageSale.</div>
            <small class="text-muted">Nota Transaksi Pembelian Sah</small>
        </div>
        <div class="text-end">
            <span class="invoice-title d-block">Nota Invoice</span>
            <span class="fw-bold text-dark" style="font-size: 15px;">#INV-<?php echo time(); ?></span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-6">
            <div class="section-title"><i class="fa fa-user me-1"></i> Pelanggan</div>
            <div class="small fw-600"><strong>Nama:</strong> <?php echo htmlspecialchars($user['name'] ?? '-'); ?></div>
            <div class="small fw-600"><strong>Telepon:</strong> <?php echo htmlspecialchars($user['contactno'] ?? '-'); ?></div>
            <div class="small fw-600"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? '-'); ?></div>
        </div>
        <div class="col-6">
            <div class="section-title"><i class="fa fa-truck me-1"></i> Destinasi Kirim</div>
            <div class="small text-secondary fw-600"><?php echo htmlspecialchars($user['shippingAddress'] ?? 'Belum diisi'); ?></div>
            <div class="small fw-bold text-dark"><?php echo htmlspecialchars($user['shippingCity'] ?? ''); ?> (<?php echo htmlspecialchars($user['shippingPincode'] ?? '-'); ?>)</div>
        </div>
    </div>

    <div class="row bg-light rounded-3 p-3 mb-4 g-2 small fw-bold">
        <div class="col-6">
            <span class="text-muted d-block" style="font-size: 11px;">Metode Transaksi</span>
            <span class="text-dark"><i class="fa fa-wallet me-1"></i> COD (Cash On Delivery)</span>
        </div>
        <div class="col-6 text-end">
            <span class="text-muted d-block" style="font-size: 11px;">Waktu Checkout</span>
            <span class="text-dark"><i class="fa fa-clock me-1"></i> <?php echo date("Y-m-d H:i:s"); ?></span>
        </div>
    </div>

    <div class="section-title"><i class="fa fa-box me-1"></i> Daftar Barang</div>
    <table class="table table-invoice mb-4">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-center">Kuantitas</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($items_invoice)): ?>
                <?php foreach($items_invoice as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['productName']); ?></td>
                    <td class="text-center">1 Pcs</td>
                    <td class="text-end">Rs. <?php echo number_format($item['productPrice'], 0, ',', '.'); ?></td>
                    <td class="text-end">Rs. <?php echo number_format($item['productPrice'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada barang belanjaan terdeteksi. Silakan berbelanja kembali.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="row justify-content-end">
        <div class="col-md-6">
            <div class="d-flex justify-content-between mb-2 small">
                <span>Total Belanja:</span>
                <span>Rs. <?php echo number_format($total_produk, 0, ',', '.'); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span>Ongkos Kirim:</span>
                <span>Rs. <?php echo number_format($ongkir, 0, ',', '.'); ?></span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center grand-total-box mt-3">
                <span>Total Bayar:</span>
                <span>Rs. <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 text-center border-top text-muted small fw-600">
        <i class="fa fa-circle-info me-1"></i> Terima kasih telah berbelanja! Lembar ini otomatis disimpan pada arsip pembelian sistem.
    </div>

    <div class="no-print">
        <button onclick="window.print();" class="btn btn-dark rounded-pill px-4 fw-bold">
            <i class="fa fa-print me-1"></i> Cetak / PDF
        </button>
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="fa fa-house me-1"></i> Beranda Utama
        </a>
    </div>
</div>

</body>
</html>
