<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE); 
include('includes/config.php');

if(strlen($_SESSION['login'])==0) {   
    header('location:login.php');
} else {
    $uid = $_SESSION['id'];
    
    // 1. Ambil Data User
    $query_u = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
    $user_data = mysqli_fetch_array($query_u);

    // 2. Ambil Data Produk (Bisa dari URL ?id=... atau dari Keranjang terakhir)
    $pid_url = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if($pid_url > 0) {
        $product_q = mysqli_query($con, "SELECT id as pid, productName, productImage1, productPrice FROM products WHERE id='$pid_url'");
    } else {
        $product_q = mysqli_query($con, "SELECT products.id as pid, products.productName, products.productImage1, products.productPrice 
                                         FROM cart JOIN products ON cart.productId=products.id 
                                         WHERE cart.userId='$uid' ORDER BY cart.id DESC LIMIT 1");
    }

    if($product_q && mysqli_num_rows($product_q) > 0) {
        $p_data = mysqli_fetch_array($product_q);
        $p_id    = $p_data['pid'];
        $p_name  = $p_data['productName'];
        $p_img   = $p_data['productImage1'];
        $p_price = (int)$p_data['productPrice'];
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location.href='index.php';</script>";
        exit();
    }

    // --- LOGIKA DISKON & ONGKIR ---
    // Diskon 5% jika belanja minimal 100rb
    $diskon = ($p_price >= 100000) ? ($p_price * 0.05) : 0;
    $base_total = $p_price - $diskon;
    $ongkir_awal = 15000; // Default J&T
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Checkout | Garage Sale Official</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --dark: #111111; --border: #ececec; --bg: #fdfcfb; --accent: #d4a373; }
        body { background-color: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; color: #333; }
        .main-container { max-width: 1100px; margin-top: 40px; margin-bottom: 60px; }
        
        /* Card UI */
        .card-custom { background: #fff; border-radius: 24px; border: 1px solid var(--border); padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .product-hero { width: 100%; height: 320px; background: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; border: 1px solid #f0f0f0; margin-bottom: 20px; overflow: hidden; }
        .product-hero img { max-width: 90%; max-height: 90%; object-fit: contain; }

        /* Selection Box */
        .box-select { border: 2px solid #f0f0f0; border-radius: 16px; padding: 18px; margin-bottom: 12px; cursor: pointer; transition: 0.3s; position: relative; }
        .box-select.active { border-color: var(--dark); background: #fafafa; }
        .box-select.active::after { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: 20px; top: 20px; color: var(--dark); font-size: 18px; }

        /* Sidebar Payment */
        .sidebar-pay { background: var(--dark); color: #fff; border-radius: 35px; padding: 35px; position: sticky; top: 25px; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; opacity: 0.8; }
        .pay-item { border: 1px solid #444; border-radius: 18px; padding: 18px; margin-bottom: 12px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
        .pay-item.active { border-color: #fff; background: rgba(255,255,255,0.1); }
        .btn-pay { background: #fff; color: #000; border-radius: 16px; padding: 20px; width: 100%; font-weight: 800; border: none; margin-top: 20px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .btn-pay:hover { background: #f0f0f0; transform: translateY(-3px); }

        .btn-back { color: #666; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; margin-bottom: 25px; transition: 0.3s; }
        .btn-back:hover { color: var(--dark); transform: translateX(-5px); }
    </style>
</head>
<body>

<div class="container main-container">
    <a href="javascript:history.back()" class="btn-back">
        <i class="fa fa-arrow-left me-2"></i> Kembali ke Toko
    </a>

    <div class="row g-4">
        <div class="col-lg-7">
            <h3 class="fw-800 mb-4">Checkout</h3>

            <div class="card-custom">
                <div class="product-hero">
                    <img src="admin/productimages/<?php echo $p_id; ?>/<?php echo $p_img; ?>" onerror="this.src='https://via.placeholder.com/500';">
                </div>
                <h5 class="fw-800 mb-1"><?php echo htmlspecialchars($p_name); ?></h5>
                <p class="text-muted fw-bold mb-0">Rp <?php echo number_format($p_price, 0, ',', '.'); ?></p>
            </div>

            <div class="card-custom">
                <h6 class="fw-bold small text-uppercase text-muted mb-3">Alamat Pengiriman</h6>
                <div class="box-select active">
                    <div class="fw-800"><?php echo htmlspecialchars($user_data['name']); ?></div>
                    <div class="small text-muted mt-1">
                        <?php echo htmlspecialchars($user_data['shippingAddress'] ?? 'Alamat belum diatur'); ?>, 
                        <?php echo htmlspecialchars($user_data['shippingCity'] ?? ''); ?>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <h6 class="fw-bold small text-uppercase text-muted mb-3">Opsi Pengiriman (Flat)</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="box-select active" id="jnt" onclick="updateExp('jnt', 15000)">
                            <div class="fw-bold">J&T Express</div>
                            <div class="small text-muted">Rp 15.000</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="box-select" id="jne" onclick="updateExp('jne', 20000)">
                            <div class="fw-bold">JNE Reguler</div>
                            <div class="small text-muted">Rp 20.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="sidebar-pay shadow-lg">
                <h5 class="fw-800 mb-4">Detail Pembayaran</h5>
                
                <div class="price-row">
                    <span>Harga Barang</span>
                    <span>Rp <?php echo number_format($p_price, 0, ',', '.'); ?></span>
                </div>

                <div class="price-row text-warning">
                    <span>
                        Diskon <?php echo ($p_price >= 100000) ? '(Promo 5%)' : '<small style="font-size:10px; opacity:0.6;">(Min. Belanja 100rb)</small>'; ?>
                    </span>
                    <span>- Rp <?php echo number_format($diskon, 0, ',', '.'); ?></span>
                </div>

                <div class="price-row">
                    <span>Ongkos Kirim</span>
                    <span id="label-ongkir">Rp 15.000</span>
                </div>

                <hr style="opacity: 0.1; margin: 25px 0;">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5">Total Bayar</span>
                    <span class="fs-2 fw-800" id="label-total">Rp <?php echo number_format($base_total + $ongkir_awal, 0, ',', '.'); ?></span>
                </div>

                <form action="invoice.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $p_id; ?>">
                    <input type="hidden" name="shipping_cost" id="in-ongkir" value="15000">
                    <input type="hidden" name="grand_total" id="in-total" value="<?php echo ($base_total + $ongkir_awal); ?>">
                    <input type="hidden" name="payment_method" id="in-method" value="Transfer Bank">

                    <label class="small text-uppercase opacity-50 fw-bold mb-3 d-block" style="letter-spacing:1px; font-size:10px;">Pilih Metode Pembayaran</label>

                    <div class="pay-item active" id="m-bank" onclick="setMethod('Transfer Bank')">
                        <div class="small">
                            <b class="d-block mb-1">Transfer Bank (Manual)</b>
                            <span class="opacity-50">BCA 8820 1234 567 a/n Garage Sale</span>
                        </div>
                        <i class="fa fa-check-circle" id="c-bank"></i>
                    </div>

                    <div class="pay-item" id="m-cod" onclick="setMethod('COD')">
                        <div class="small">
                            <b class="d-block mb-1">Cash on Delivery (COD)</b>
                            <span class="opacity-50">Bayar langsung ke kurir saat sampai</span>
                        </div>
                        <i class="fa fa-circle opacity-20" id="c-cod"></i>
                    </div>

                    <button type="submit" name="submit" class="btn-pay shadow">Konfirmasi & Bayar</button>
                </form>
            </div>
            <p class="text-center mt-4 small text-muted">Aman & Terpercaya di Garage Sale Official</p>
        </div>
    </div>
</div>

<script>
    const basePrice = <?php echo (int)$base_total; ?>;

    function updateExp(kurir, harga) {
        // Update UI Box
        document.getElementById('jnt').classList.toggle('active', kurir === 'jnt');
        document.getElementById('jne').classList.toggle('active', kurir === 'jne');
        
        // Update Label Text
        document.getElementById('label-ongkir').innerText = "Rp " + harga.toLocaleString('id-ID');
        document.getElementById('label-total').innerText = "Rp " + (basePrice + harga).toLocaleString('id-ID');
        
        // Update Hidden Input Form
        document.getElementById('in-ongkir').value = harga;
        document.getElementById('in-total').value = basePrice + harga;
    }

    function setMethod(method) {
        // Update Hidden Input
        document.getElementById('in-method').value = method;
        
        // Update UI Box
        document.getElementById('m-bank').classList.toggle('active', method === 'Transfer Bank');
        document.getElementById('m-cod').classList.toggle('active', method === 'COD');
        
        // Update Icon
        document.getElementById('c-bank').className = (method === 'Transfer Bank') ? "fa fa-check-circle" : "fa fa-circle opacity-20";
        document.getElementById('c-cod').className = (method === 'COD') ? "fa fa-check-circle" : "fa fa-circle opacity-20";
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>