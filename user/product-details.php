<?php 
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include('includes/config.php');

// 1. LOGIKA UTAMA: Add to Cart & Buy Now (Checkout Langsung)
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id = intval($_GET['id']);
    
    // Fitur Beli Langsung: Bersihkan keranjang lama agar fokus ke produk ini saja
    if(isset($_GET['buy']) && $_GET['buy'] == "now"){
        unset($_SESSION['cart']);
    }

    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $sql_p="SELECT * FROM products WHERE id={$id}";
        $query_p=mysqli_query($con,$sql_p);
        if(mysqli_num_rows($query_p)!=0){
            $row_p=mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);
        }
    }

    if(isset($_GET['buy']) && $_GET['buy'] == "now"){
        // REPARASI: Dialihkan langsung ke payment-method.php menggunakan JS agar terhindar dari error 'Header Already Sent'
        echo "<script>window.location.href='payment-method.php';</script>";
    } else {
        echo "<script>alert('Produk berhasil ditambahkan ke keranjang'); window.location.href='my-cart.php'</script>";
    }
    exit();
}

// Mengambil ID Produk dari URL
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curated Find | Garage Sale Studio</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,700;1,9..144,700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-canvas: #f2f0eb;
            --thrift-dark: #1e1e1e;
            --thrift-clay: #8d775f;
            --thrift-sand: #e6e2d6;
            --thrift-cream: #ffffff;
            --thrift-disabled: #a0a0a0;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-canvas); 
            color: var(--thrift-dark); 
            margin: 0; padding: 0;
        }

        /* --- Header / Home Navigation --- */
        .top-nav {
            padding: 20px 0;
            background: transparent;
        }
        .home-link {
            text-decoration: none;
            color: var(--thrift-dark);
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            transition: 0.3s;
        }
        .home-link:hover { color: var(--thrift-clay); }

        /* --- Container Utama --- */
        .glass-container {
            background: var(--thrift-cream);
            border-radius: 32px;
            padding: 40px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 20px 60px rgba(0,0,0,0.03);
            margin-bottom: 50px;
        }

        /* --- Image Styling --- */
        .image-wrapper {
            position: sticky;
            top: 30px;
            background: var(--thrift-sand);
            border-radius: 24px;
            overflow: hidden;
            aspect-ratio: 4/5;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .product-img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .image-wrapper:hover .product-img { transform: scale(1.08); }

        /* Badge Overlay saat Produk Habis */
        .soldout-badge-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 2;
        }
        .soldout-text-pill {
            background: #000; color: #fff;
            padding: 12px 30px; border-radius: 50px;
            font-weight: 800; font-size: 16px; letter-spacing: 2px;
        }

        /* --- Detail Text & Branding --- */
        .brand-pill {
            display: inline-block;
            background: var(--thrift-clay);
            color: white;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .product-name {
            font-family: 'Fraunces', serif;
            font-size: clamp(32px, 5vw, 48px);
            line-height: 1.1;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        .price-tag {
            font-size: 32px;
            font-weight: 800;
            margin: 25px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .old-price {
            font-size: 18px;
            color: #b0aca2;
            text-decoration: line-through;
            font-weight: 400;
        }

        /* --- Tombol Aksi / Action Buttons --- */
        .action-group {
            display: flex;
            gap: 12px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .btn-main {
            flex: 1; min-width: 160px;
            height: 64px;
            border-radius: 18px;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            text-decoration: none !important;
            transition: all 0.3s ease;
        }
        .btn-dark { background: var(--thrift-dark); color: white !important; border: none; }
        .btn-outline { background: transparent; color: var(--thrift-dark) !important; border: 2px solid var(--thrift-dark); }
        .btn-main:hover:not(.disabled) { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* Style Kunci Mati Tombol Sold Out */
        .btn-main.disabled {
            background: #e0e0e0 !important;
            color: var(--thrift-disabled) !important;
            border: 2px solid #e0e0e0 !important;
            pointer-events: none;
            cursor: not-allowed;
        }

        /* Tombol Share Bundar Modern */
        .btn-icon {
            width: 64px; height: 64px;
            border-radius: 18px;
            border: 1px solid #e0e0e0;
            background: white;
            display: flex; align-items: center; justify-content: center;
            color: var(--thrift-dark); transition: 0.3s;
            cursor: pointer;
        }
        .btn-icon:hover {
            background: var(--thrift-dark);
            color: white;
        }

        /* --- Bagian Informasi & Deskripsi --- */
        .details-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .details-section h5 {
            font-weight: 800; font-size: 12px;
            letter-spacing: 2px; color: var(--thrift-clay);
            margin-bottom: 20px; text-transform: uppercase;
        }
        .desc-text { line-height: 1.8; color: #5a5a5a; font-size: 15px; }

        /* Toast Notifikasi Minimalis */
        #custom-toast {
            visibility: hidden; 
            background: rgba(17, 17, 17, 0.95);
            backdrop-filter: blur(8px);
            color: white; 
            padding: 14px 28px; 
            border-radius: 16px;
            position: fixed; 
            bottom: 30px; 
            left: 50%;
            transform: translate(-50%, 20px); 
            z-index: 9999; 
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.16);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
        }
        #custom-toast.show { 
            visibility: visible; 
            opacity: 1; 
            transform: translate(-50%, 0); 
        }

        /* --- Modal Share Custom Premium --- */
        .share-modal-content {
            border-radius: 24px;
            border: none;
            background: var(--thrift-cream);
            padding: 15px;
        }
        .share-modal-header {
            border-bottom: 1px solid #f0eee9;
            padding-bottom: 15px;
        }
        .share-modal-title {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            color: var(--thrift-dark);
        }
        .share-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 20px 0 10px 0;
            text-align: center;
        }
        .share-item {
            text-decoration: none !important;
            color: var(--thrift-dark);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .share-item:hover {
            transform: translateY(-3px);
        }
        .share-icon-circle {
            width: 55px; height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
        }
        .bg-wa { background-color: #25D366; }
        .bg-ig { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
        .bg-x  { background-color: #000000; }
        .bg-copy { background-color: var(--thrift-clay); }

        @media (max-width: 768px) {
            .glass-container { padding: 25px; border-radius: 0; }
            .image-wrapper { position: relative; top: 0; margin-bottom: 30px; }
        }
    </style>
</head>
<body>

<div class="container">
    <nav class="top-nav d-flex justify-content-between align-items-center">
        <a href="index.php" class="home-link">
            <i class="fa fa-chevron-left me-2"></i> KEMBALI KE HOME
        </a>
        <div style="font-weight: 900; letter-spacing: 2px;">GS. STUDIO</div>
    </nav>

    <?php 
    // Mengambil data produk dari database berdasarkan PID
    $ret = mysqli_query($con, "SELECT * FROM products WHERE id='$pid'");
    while($row = mysqli_fetch_array($ret)) {
        
        // --- LOGIKA VALIDASI GABUNGAN AMANKAN STATUS SOLD OUT ---
        $is_soldout = false;
        $check_avail = isset($row['productAvailability']) ? strtolower(trim($row['productAvailability'])) : '';

        if (
            $check_avail === 'out of stock' || 
            (isset($row['stock']) && $row['stock'] == 0 && $row['stock'] !== '') || 
            (isset($row['qty']) && $row['qty'] == 0 && $row['qty'] !== '')
        ) {
            $is_soldout = true;
        }
    ?>
    
    <div class="glass-container">
        <div class="row">
            <div class="col-md-5">
                <div class="image-wrapper">
                    <?php if($is_soldout) { ?>
                        <div class="soldout-badge-overlay">
                            <span class="soldout-text-pill">SOLD OUT</span>
                        </div>
                    <?php } ?>
                    
                    <img src="admin/productimages/<?php echo $row['id'];?>/<?php echo $row['productImage1'];?>" 
                         onerror="this.onerror=null; this.src='https://placehold.co/600x750/e6e2d6/8d775f?text=No+Image+Available';" 
                         class="product-img" 
                         alt="">
                </div>
            </div>

            <div class="col-md-7 ps-md-5">
                <div class="mt-4 mt-md-0">
                    <span class="brand-pill">Garage Sale — Curated Find</span>
                    <h1 class="product-name"><?php echo htmlentities($row['productName']);?></h1>
                    
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="stars" style="color: #1a1a1a;">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                        </div>
                        
                        <?php if($is_soldout) { ?>
                            <span class="small text-danger fw-bold">(Habis Terjual)</span>
                        <?php } else { ?>
                            <span class="small text-muted">(Ready Stock — 1 of 1)</span>
                        <?php } ?>
                    </div>

                    <div class="price-tag">
                        Rp <?php echo number_format($row['productPrice'],0,',','.');?>
                        <?php if($row['productPriceBeforeDiscount'] > 0){ ?>
                            <span class="old-price">Rp <?php echo number_format($row['productPriceBeforeDiscount'],0,',','.');?></span>
                        <?php } ?>
                    </div>

                    <p class="desc-text mb-4" style="font-style: italic; color: var(--thrift-clay);">
                        "Setiap potong pakaian menceritakan kisahnya sendiri. Amankan koleksi unik ini sebelum orang lain mengambilnya."
                    </p>

                    <div class="action-group">
                        <?php if($is_soldout) { ?>
                            <a href="javascript:void(0);" class="btn-main disabled">
                                <i class="fa-solid fa-ban me-2"></i> Habis Terjual
                            </a>
                            <a href="javascript:void(0);" class="btn-main disabled">
                                Sold Out
                            </a>
                        <?php } else { ?>
                            <a href="product-details.php?action=add&id=<?php echo $row['id']; ?>&pid=<?php echo $row['id']; ?>" class="btn-main btn-outline">
                                <i class="fa-solid fa-cart-shopping me-2"></i> + Keranjang
                            </a>
                            <a href="product-details.php?action=add&id=<?php echo $row['id']; ?>&pid=<?php echo $row['id']; ?>&buy=now" class="btn-main btn-dark">
                                Checkout Sekarang
                            </a>
                        <?php } ?>
                        
                        <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>

                    <div class="details-section">
                        <h5>Info Produk & Kondisi</h5>
                        <div class="desc-text">
                            <?php 
                            if(!empty($row['productDescription'])) {
                                echo nl2br($row['productDescription']);
                            } else {
                                echo "
                                <ul class='ps-3 mb-0' style='list-style-type: square;'>
                                    <li><b>Kondisi:</b> Sangat Baik / Layak Pakai (9.5/10)</li>
                                    <li><b>Ukuran:</b> Standard Fit (Sesuai gambar produk)</li>
                                    <li><b>Bahan:</b> Katun Premium / Nyaman digunakan sehari-hari</li>
                                    <li><b>Minus:</b> Tidak ada noda atau robek (Kondisi bersih terawat)</li>
                                </ul>";
                            }
                            ?>
                        </div>
                        <div class="mt-4 p-3" style="background: var(--bg-canvas); border-radius: 12px; font-size: 13px;">
                            <i class="fa-solid fa-shield-heart me-2"></i> 
                            Barang ini adalah <b>Thrift/Pre-loved</b> yang sudah melalui proses pencucian bersih, sterilisasi higienis, dan terkurasi kualitasnya.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content share-modal-content">
            <div class="modal-header share-modal-header">
                <h5 class="modal-title share-modal-title">Bagikan Produk</h5>
                <button type="text" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="share-grid">
                    <a href="https://api.whatsapp.com/send?text=Cek%20koleksi%20thrift%20keren%20ini%20di%20GS.%20Studio!%20" target="_blank" class="share-item">
                        <div class="share-icon-circle bg-wa"><i class="fa-brands fa-whatsapp"></i></div>
                        WhatsApp
                    </a>
                    <a href="https://instagram.com" target="_blank" class="share-item">
                        <div class="share-icon-circle bg-ig"><i class="fa-brands fa-instagram"></i></div>
                        Instagram
                    </a>
                    <a href="https://x.com" target="_blank" class="share-item">
                        <div class="share-icon-circle bg-x"><i class="fa-brands fa-x-twitter"></i></div>
                        X / Twitter
                    </a>
                    <a href="javascript:void(0);" id="copy-link-btn" class="share-item">
                        <div class="share-icon-circle bg-copy"><i class="fa-solid fa-link"></i></div>
                        Salin Tautan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="custom-toast">Tautan berhasil disalin!</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Aksi tombol salin tautan di dalam modal share
    $('#copy-link-btn').on('click', function() {
        var currentUrl = window.location.href;
        var $tempInput = $("<textarea>");
        $("body").append($tempInput);
        $tempInput.val(currentUrl).select();
        document.execCommand("copy");
        $tempInput.remove();

        // Sembunyikan modal
        $('#shareModal').modal('hide');

        // Memunculkan toast
        var toast = $("#custom-toast");
        toast.text("Link produk berhasil disalin!");
        toast.addClass("show");
        
        setTimeout(function() { 
            toast.removeClass("show"); 
        }, 3000);
    });
});
</script>
</body>
</html>
