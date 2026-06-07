<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

// Proteksi Keamanan: Jika user belum login, alihkan ke halaman login/akun
if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
    header('location:my-account.php');
    exit();
}

// Proteksi Sesi Ganda: Jika keranjang kosong, cegah user bypass mengetik URL langsung
if (empty($_SESSION['cart'])) {
    header('location:index.php');
    exit();
}

$uid = $_SESSION['id'];

// ========================================================
// 1. PENGAMBILAN DATA USER & DETEKSI ONGKIR OTOMATIS
// ========================================================
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($query_user);

// Ambil data dasar alamat dari database
$alamat_jalan   = $user['shippingAddress'] ?? ''; 
$wilayah_gabung = $user['shippingCity'] ?? ''; 
$kode_pos       = $user['shippingPincode'] ?? '';

$provinsi_tampil = "Belum diisi";
$kota_tampil     = "Belum diisi";

if (!empty($wilayah_gabung)) {
    $pecah_wilayah = explode(',', $wilayah_gabung);
    if (count($pecah_wilayah) >= 2) {
        $provinsi_tampil = trim(end($pecah_wilayah));
        $kota_tampil     = trim($pecah_wilayah[count($pecah_wilayah) - 2]);
    } else {
        $provinsi_tampil = trim($wilayah_gabung);
        $kota_tampil     = trim($wilayah_gabung);
    }
}

// ATUR NOMINAL ONGKOS KIRIM DI SINI
$ongkir_jawa      = 15000;  
$ongkir_luar_jawa = 45000;  

$ongkir_terpilih = 0;
$status_wilayah  = "Alamat Kosong";

$daftar_provinsi_jawa = [
    'banten', 'dki jakarta', 'jakarta', 'jawa barat', 
    'jawa tengah', 'di yogyakarta', 'yogyakarta', 'jawa timur'
];

$provinsi_cek = strtolower(trim($provinsi_tampil));

if (empty($alamat_jalan) || empty($wilayah_gabung)) {
    $ongkir_terpilih = 0;
    $status_wilayah  = "Alamat belum dilengkapi di My Account";
} elseif (in_array($provinsi_cek, $daftar_provinsi_jawa)) {
    $ongkir_terpilih = $ongkir_jawa;
    $status_wilayah  = "Pulau Jawa (Tarif Reguler)";
} else {
    $ongkir_terpilih = $ongkir_luar_jawa;
    $status_wilayah  = "Luar Pulau Jawa (Tarif Luar Pulau)";
}

// ========================================================
// 2. HITUNG TOTAL BELANJA 
// ========================================================
$total_produk = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $p_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $p_ids);
    $query_cart = mysqli_query($con, "SELECT productPrice FROM products WHERE id IN ($ids_string)");
    if ($query_cart) {
        while ($row_c = mysqli_fetch_array($query_cart)) {
            $total_produk += (float)$row_c['productPrice']; 
        }
    }
}
$grand_total = $total_produk + $ongkir_terpilih;

// ========================================================
// 3. PROSES KETIKA TOMBOL SUBMIT ORDER DIKLIK (PRODUK TERKUNCI AMAN)
// ========================================================
if (isset($_POST['submit_order'])) {
    $pay_method = mysqli_real_escape_string($con, $_POST['pay_method'] ?? 'COD');
    
    if (empty($alamat_jalan) || empty($wilayah_gabung)) {
        echo "<script>alert('Harap lengkapi alamat pengiriman Anda terlebih dahulu di menu My Account!'); window.location='my-account.php';</script>";
        exit();
    }

    if (!empty($_SESSION['cart'])) {
        
        // AMAN: Validasi Race Condition Stok Khusus Konsep Thrift Item 1-of-1
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $pid_secure = intval($pid);
            $check_stock = mysqli_query($con, "SELECT productAvailability FROM products WHERE id='$pid_secure'");
            if ($check_stock && mysqli_num_rows($check_stock) > 0) {
                $stock_row = mysqli_fetch_array($check_stock);
                $availability = trim($stock_row['productAvailability'] ?? '');
                
                // Jika keduluan dibeli pembeli lain (stok sudah Out of Stock)
                if (strcasecmp($availability, 'Out of Stock') == 0 || $availability == '') {
                    echo "<script>
                        alert('Maaf, salah satu pakaian di keranjang Anda baru saja terjual beberapa detik yang lalu! Pesanan otomatis dibatalkan.'); 
                        window.location='index.php';
                    </script>";
                    exit();
                }
            }
        }

        // Trik Jitu: Ambil salinan id produk di dalam keranjang ke session invoice sebelum dihancurkan
        $_SESSION['last_invoice_items'] = array_keys($_SESSION['cart']);

        foreach ($_SESSION['cart'] as $pid => $qty) {
            $pid_secure = intval($pid);
            // Memasukkan data pesanan ke database 
            mysqli_query($con, "INSERT INTO orders(userId, productId, quantity, paymentMethod) VALUES('$uid', '$pid_secure', '1', '$pay_method')");
            
            // Otomatis ubah status produk menjadi Out of Stock setelah sukses checkout agar tidak bisa dibeli lagi
            mysqli_query($con, "UPDATE products SET productAvailability='Out of Stock' WHERE id='$pid_secure'");
        }
        
        // Hancurkan isi keranjang belanja utama website
        unset($_SESSION['cart']);
        
        // Alihkan halaman ke invoice.php membawa tanda sukses
        echo "<script>
            alert('Pesanan Anda berhasil ditempatkan!');
            window.location='invoice.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Keranjang Anda kosong!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>NexStore | Metode Pembayaran & Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --beige-bg: #fdfcfb;
            --beige-border: #e8e4d8;
            --accent-dark: #111111;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; }
        .logo { font-weight: 900; font-size: 28px; letter-spacing: -1.5px; text-decoration: none; color: var(--accent-dark); }
        .top-brand-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; border-bottom: 1px solid var(--beige-border); }
        
        .checkout-section { padding: 50px 5%; }
        .address-card { background: #fff; padding: 25px; border: 1px solid var(--beige-border); border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.01); height: 100%; }
        .address-title { font-weight: 800; font-size: 15px; color: var(--accent-dark); border-bottom: 2px solid var(--accent-dark); padding-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .info-label { font-size: 12px; color: #888; font-weight: 600; text-transform: uppercase; display: block; margin-top: 12px; margin-bottom: 2px; }
        .info-value { font-size: 15px; font-weight: 700; color: #222; display: block; }
        
        .summary-card { background: var(--accent-dark); color: #fff; border-radius: 28px; padding: 35px; position: sticky; top: 30px; }
        
        .payment-method-box { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 15px; margin-bottom: 15px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        .payment-method-box:hover { background: rgba(255,255,255,0.1); }
        .payment-method-box input[type="radio"] { accent-color: #fff; width: 18px; height: 18px; }

        .btn-submit-order { background: #fff; color: #000; width: 100%; padding: 18px; border-radius: 18px; font-weight: 800; border: none; margin-top: 20px; display: block; text-align: center; text-decoration: none; transition: 0.3s; }
        .btn-submit-order:hover { background: #eee; transform: translateY(-3px); color: #000; }
    </style>
</head>
<body>

<div class="top-brand-row">
    <a href="index.php" class="logo">GarageSale.</a>
    <div class="d-flex align-items-center">
        <a href="my-cart.php" class="text-dark fw-bold text-decoration-none small"><i class="fa fa-chevron-left me-1"></i> Kembali ke Keranjang</a>
    </div>
</div>

<div class="container-fluid checkout-section">
    <form name="payment" method="post" action="payment-method.php">
        <div class="row g-5">
            
            <div class="col-lg-8">
                <h3 class="fw-800 mb-4">Informasi Checkout Pengiriman</h3>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="address-card">
                            <h5 class="address-title mb-3"><i class="fa-regular fa-file-lines me-2"></i>Billing Address</h5>
                            
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['name'] ?? '-'); ?></span>

                            <span class="info-label">Alamat Rumah / Jalan</span>
                            <span class="info-value"><?php echo htmlspecialchars($alamat_jalan ?: 'Belum diisi'); ?></span>

                            <span class="info-label">Kota / Kabupaten</span>
                            <span class="info-value"><?php echo htmlspecialchars($kota_tampil); ?></span>

                            <span class="info-label">Provinsi</span>
                            <span class="info-value"><?php echo htmlspecialchars($provinsi_tampil); ?></span>

                            <span class="info-label">Kode Pos</span>
                            <span class="info-value"><?php echo htmlspecialchars($kode_pos ?: '-'); ?></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="address-card">
                            <h5 class="address-title mb-3"><i class="fa fa-truck me-2"></i>Shipping Address</h5>
                            
                            <span class="info-label">Tujuan Pengiriman</span>
                            <span class="info-value"><?php echo htmlspecialchars($alamat_jalan ?: 'Belum diisi'); ?></span>

                            <span class="info-label">Kota Tujuan</span>
                            <span class="info-value"><?php echo htmlspecialchars($kota_tampil); ?></span>

                            <span class="info-label">Provinsi Tujuan</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($provinsi_tampil); ?> 
                                <br>
                                <span class="badge bg-dark mt-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                                    🚀 <?php echo $status_wilayah; ?>
                                </span>
                            </span>

                            <span class="info-label">Kode Pos Tujuan</span>
                            <span class="info-value"><?php echo htmlspecialchars($kode_pos ?: '-'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-start">
                    <a href="my-account.php" class="btn btn-sm btn-outline-dark rounded-3 px-3 fw-bold">
                        <i class="fa fa-marker me-1"></i> Sesuaikan Alamat di My Account
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h4 class="fw-800 mb-4">Metode Pembayaran</h4>
                    
                    <label class="payment-method-box">
                        <input type="radio" name="pay_method" value="COD" checked>
                        <div>
                            <span class="fw-bold d-block text-white">Cash On Delivery (COD)</span>
                            <small class="text-white-50" style="font-size: 11px;">Bayar di tempat saat barang tiba</small>
                        </div>
                    </label>

                    <h5 class="fw-800 mt-5 mb-3">Ringkasan Tagihan</h5>
                    
                    <div class="d-flex justify-content-between mb-2 opacity-75">
                        <span>Total Produk (1 Pcs)</span>
                        <span>Rp. <?php echo number_format($total_produk, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 opacity-75">
                        <span>Ongkos Kirim Sistem</span>
                        <span class="text-warning fw-bold">Rp. <?php echo number_format($ongkir_terpilih, 0, ',', '.'); ?></span>
                    </div>
                    
                    <hr style="border-color: rgba(255,255,255,0.15)">
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <span class="h6 mb-0 fw-bold">Grand Total</span>
                        <span class="h3 fw-800 mb-0 text-white">Rp. <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                    </div>
                    
                    <button type="submit" name="submit_order" class="btn-submit-order">
                        BUAT PESANAN SEKARANG
                    </button>
                    
                    <div class="mt-4 text-center opacity-50" style="font-size: 12px;">
                        <i class="fa fa-shield-halved me-1"></i> Transaksi Terenkripsi & Aman
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
