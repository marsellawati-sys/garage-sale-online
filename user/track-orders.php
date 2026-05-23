<?php
session_start();
error_reporting(0); // Mematikan pelaporan error untuk tampilan produksi
include('includes/config.php');

// Solusi untuk Warning: Undefined array key "oid"
// Mengecek apakah ada 'oid' di URL, jika tidak ada maka diisi string kosong
$oid = isset($_GET['oid']) ? $_GET['oid'] : ''; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexStore | Lacak Pesanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --bg-body: #fdfcfb;
            --accent: #111111;
            --beige-border: #e8e4d8;
            --gold-muted: #d4a373;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--accent);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Minimalis */
        .navbar-custom {
            background: #fff;
            border-bottom: 1px solid var(--beige-border);
            padding: 15px 0;
            margin-bottom: 60px;
        }
        .logo-text { 
            font-weight: 900; 
            font-size: 22px; 
            letter-spacing: -1.5px; 
            text-decoration: none !important; 
            color: var(--accent); 
        }

        /* Container Pelacakan */
        .track-box {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 28px;
            border: 1px solid var(--beige-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            text-align: center;
        }

        .icon-header {
            width: 65px;
            height: 65px;
            background: var(--bg-body);
            color: var(--gold-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin: 0 auto 25px;
            font-size: 24px;
        }

        .form-control-track {
            width: 100%;
            padding: 16px;
            background: var(--bg-body);
            border: 1px solid var(--beige-border);
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            transition: 0.3s;
        }

        .form-control-track:focus {
            outline: none;
            border-color: var(--accent);
            background: #fff;
        }

        .btn-track-submit {
            background: var(--accent);
            color: #fff;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            font-weight: 800;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-track-submit:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .back-link {
            margin-top: 25px;
            display: inline-block;
            color: #888;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .back-link:hover { color: var(--accent); }
    </style>
    
    <script>
        // Fungsi untuk membuka jendela pelacakan
        function popUpWindow(URLStr) {
            window.open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=600,height=600');
        }
    </script>
</head>
<body>

<nav class="navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="logo-text">NEXSTORE.</a>
        <a href="index.php" class="text-muted text-decoration-none small fw-bold">KEMBALI KE TOKO</a>
    </div>
</nav>

<div class="container">
    <div class="track-box">
        <div class="icon-header">
            <i class="fa-solid fa-box-open"></i>
        </div>
        
        <h2 class="fw-bold mb-2">Lacak Pesanan</h2>
        <p class="text-muted small mb-4">
            Masukkan Order ID untuk melihat status pengiriman barang Anda secara real-time.
        </p>

        <div class="text-start">
            <label class="small fw-bold mb-2 ms-1 text-uppercase" style="letter-spacing: 1px;">Order ID</label>
            <input type="text" id="orderid" class="form-control-track" placeholder="Contoh: 9" value="<?php echo htmlspecialchars($oid); ?>">
        </div>
        
        <button type="button" onClick="popUpWindow('track-order.php?oid=' + document.getElementById('orderid').value);" class="btn-track-submit">
            LACAK SEKARANG <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>

        <a href="index.php" class="back-link">
            <i class="fa-solid fa-chevron-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<footer class="mt-auto py-4 text-center">
    <p class="small text-muted mb-0">&copy; 2026 NEXSTORE STUDIO.</p>
</footer>

</body>
</html>