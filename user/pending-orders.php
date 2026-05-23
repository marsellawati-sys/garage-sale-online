<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Ambil metode dari URL
$method = $_GET['method']; 
$total = $_SESSION['tp']; 

if(strlen($_SESSION['login'])==0) {   
    header('location:login.php');
} else { 
    // Baris ini adalah pembuka '{' yang tadi lupa ditutup
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Selesaikan Pembayaran | NexStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        :root { --beige-dark: #f5f4ef; --beige-border: #e8e4d8; --accent: #111; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--beige-dark); color: var(--accent); padding: 40px 15px; }
        .invoice-card { background: #fff; max-width: 500px; margin: 0 auto; border: 1px solid var(--beige-border); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header-status { background: var(--accent); color: #fff; padding: 20px; text-align: center; border-radius: 12px 12px 0 0; }
        .content { padding: 35px; text-align: center; }
        .amount-box { background: #fdfcfb; border: 1px dashed var(--beige-border); padding: 20px; border-radius: 8px; margin: 20px 0; }
        .amount-box h2 { font-weight: 900; margin: 0; color: #111; }
        .bank-details { text-align: left; background: #fff; border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-top: 15px; }
        .btn-confirm { background: var(--accent); color: #fff !important; width: 100%; padding: 18px; font-weight: 800; border-radius: 6px; display: block; text-decoration: none; margin-top: 25px; text-transform: uppercase; transition: 0.3s; }
    </style>
</head>
<body>

<div class="invoice-card">
    <div class="header-status">
        <span style="font-size: 11px; font-weight: 800; letter-spacing: 2px;">INSTRUKSI PEMBAYARAN</span>
    </div>

    <div class="content">
        <p class="mb-1 text-muted">Total Tagihan:</p>
        <div class="amount-box">
            <h2>Rs. <?php echo number_format($total, 2); ?></h2>
        </div>

        <?php if($method == "QRIS"): ?>
            <div class="payment-instruction">
                <p class="font-weight-bold mb-3">Scan Kode QRIS:</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=NexStore-Payment" width="180" style="border: 2px solid #111; padding: 5px; border-radius: 10px;">
            </div>

        <?php elseif(strpos($method, 'Transfer') !== false): ?>
            <p class="font-weight-bold mb-2">Transfer ke Rekening:</p>
            <div class="bank-details text-center">
                <?php 
                if($method == "Transfer BCA") { echo "<strong>BANK BCA</strong><br><span style='font-size: 22px; font-weight: 800;'>1234 567 890</span>"; }
                elseif($method == "Transfer Mandiri") { echo "<strong>BANK MANDIRI</strong><br><span style='font-size: 22px; font-weight: 800;'>900 0012 3456</span>"; }
                elseif($method == "Transfer BNI") { echo "<strong>BANK BNI</strong><br><span style='font-size: 22px; font-weight: 800;'>009 876 5432</span>"; }
                ?>
                <br><small class="text-muted">A/N: PT. NEXSTORE INDONESIA</small>
            </div>

        <?php elseif($method == "COD"): ?>
            <div class="py-3 text-center">
                <i class="fa fa-truck fa-3x mb-3" style="opacity: 0.2;"></i>
                <h5 class="font-weight-bold">Metode COD</h5>
                <p class="small text-muted">Siapkan uang tunai saat kurir datang.</p>
            </div>
        <?php endif; ?>

        <a href="order-history.php" class="btn-confirm">Saya Sudah Bayar</a>
    </div>
</div>

</body>
</html>
<?php 
} // INI ADALAH PENUTUP UNTUK 'else' DI BARIS 12
?>