<?php
session_start();
// Menonaktifkan error reporting di halaman produksi agar tidak ada kebocoran baris teks error PHP ke pengguna
error_reporting(0); 
ini_set('display_errors', 0);

include('includes/config.php');

// --- LOGIKA ADD TO CART (DIKUNCI KE KUANTITAS 1 UNTUK GARAGE SALE) ---
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id=intval($_GET['id']);
    
    // Sesuai konsep thrift/garage sale, kuantitas selalu dipaksa bernilai 1
    $_SESSION['cart'][$id] = array("quantity" => 1, "price" => 0);
    
    // Ambil harga asli dari database untuk disinkronkan ke dalam session cart
    $sql_p="SELECT productPrice FROM products WHERE id='{$id}'";
    $query_p=mysqli_query($con,$sql_p);
    if($query_p && mysqli_num_rows($query_p)!=0){
        $row_p=mysqli_fetch_array($query_p);
        $_SESSION['cart'][$id]['price'] = $row_p['productPrice'];
    }

    if(isset($_GET['ajax'])){ exit; }
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Sale | Timeless Essentials</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
     
    <style>
        :root {
            --bg-base: #fdfcfb;
            --text-main: #111111;
            --accent: #d4a373;
            --border: #e8e4d8;
            --white: #ffffff;
            --header-height: 120px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            margin: 0;
            padding-top: var(--header-height);
            overflow-x: hidden;
        }

        /* --- MODERN HEADER --- */
        .header-modern {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: fixed; top: 0; width: 100%; z-index: 1000;
            transition: all 0.4s ease;
        }
        .header-modern.shrink { padding: 5px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .nav-top-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-weight: 900; font-size: 28px; letter-spacing: -1.5px; text-decoration: none; color: var(--text-main); }
        .header-tools { display: flex; gap: 22px; align-items: center; }
        .header-tools a, .search-trigger { color: var(--text-main); font-size: 18px; text-decoration: none; position: relative; cursor: pointer; transition: 0.3s; }
        .cart-badge {
            position: absolute; top: -8px; right: -10px;
            background: var(--text-main); color: white;
            font-size: 9px; width: 17px; height: 17px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800;
        }

        /* --- SEARCH OVERLAY --- */
        #search-overlay {
            position: fixed; top: -100%; left: 0; width: 100%;
            background: #fff; padding: 60px 0;
            border-bottom: 1px solid var(--border); z-index: 1001;
            transition: 0.5s cubic-bezier(0.77, 0, 0.175, 1);
        }
        #search-overlay.active { top: 0; }

        /* --- HERO CARD --- */
        .hero-card {
            background: #e9e5d9; height: 480px; border-radius: 40px;
            display: flex; align-items: center; padding: 0 8%;
            margin-bottom: 60px; position: relative; overflow: hidden;
            background-image: url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&q=80&w=2070');
            background-size: cover; background-position: center;
        }
        .hero-text { z-index: 2; position: relative; }
        .hero-text h1 { font-size: clamp(45px, 8vw, 85px); font-weight: 800; line-height: 0.85; letter-spacing: -4px; margin-bottom: 20px; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }

        /* --- PRODUCT GRID (ZALORA STYLE) --- */
        .product-item { transition: 0.3s ease; margin-bottom: 35px; position: relative; }
        
        .img-wrapper {
            background: #f4f4f4; border-radius: 0;
            aspect-ratio: 3/4;
            overflow: hidden; position: relative; 
            border: none;
            margin-bottom: 12px;
        }
        
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
        .product-item:hover img { transform: scale(1.05); }

        /* Overlay Button */
        .action-overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(100%);
            transition: 0.3s ease;
            display: flex; flex-direction: column;
            z-index: 3;
        }
        .product-item:hover .action-overlay { transform: translateY(0); }

        .btn-quick-add {
            border: none; background: #000; color: #fff;
            padding: 12px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-quick-add:hover { background: #333; }

        /* Gaya Khusus untuk Badge Sold Out */
        .sold-out-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(17, 17, 17, 0.85);
            backdrop-filter: blur(5px);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 6px 14px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            z-index: 4;
            border-radius: 4px;
        }

        /* Typography Produk */
        .product-brand { font-size: 10px; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .product-name-link { font-size: 13px; color: #333; text-decoration: none; font-weight: 400; display: block; margin-bottom: 4px; }
        .product-name-link:hover { text-decoration: underline; }
        .price { font-weight: 700; color: #000; font-size: 14px; }

        /* --- FOOTER & SERVICE --- */
        .service-card { padding: 35px; border-radius: 24px; border: 1px solid var(--border); background: #fff; transition: 0.3s; }
        .footer-main { background: #fff; border-top: 1px solid var(--border); padding-top: 80px; margin-top: 100px; }
        .newsletter-pill { background: #f8f8f8; border-radius: 50px; padding: 6px; display: flex; border: 1px solid #eee; }
    </style>
</head>
<body>

<div id="search-overlay">
    <div class="container text-center">
        <form method="post" action="search-result.php" class="position-relative" style="max-width: 700px; margin: auto;">
            <input type="text" name="product" placeholder="Apa yang Anda cari hari ini?" class="form-control form-control-lg border-0 border-bottom rounded-0 shadow-none fs-2 fw-light" required>
            <button type="submit" name="search" class="btn position-absolute end-0 top-50 translate-middle-y fs-3"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <p class="mt-5 small text-muted text-uppercase fw-bold" onclick="toggleSearch()" style="cursor:pointer; letter-spacing: 2px;">Tutup Menu [ESC]</p>
    </div>
</div>

<header class="header-modern" id="mainHeader">
    <div class="container">
        <div class="nav-top-row">
            <a href="index.php" class="logo">Garage Sale.</a>
            <div class="header-tools">
                <div class="search-trigger" onclick="toggleSearch()"><i class="fa-solid fa-magnifying-glass"></i></div>
                <a href="order-history.php"><i class="fa-solid fa-truck-fast"></i></a>
                <a href="my-wishlist.php" class="position-relative">
                    <i class="fa-regular fa-heart"></i>
                    <?php 
                    if(isset($_SESSION['login']) && $_SESSION['login'] != ""){
                        $uid = $_SESSION['id'];
                        $query_wish_count = mysqli_query($con, "SELECT id FROM wishlist WHERE userId='$uid'");
                        if($query_wish_count) {
                            $num_wish = mysqli_num_rows($query_wish_count);
                            if($num_wish > 0) { echo '<span class="cart-badge" style="background: #ff4757;">'.$num_wish.'</span>'; }
                        }
                    }
                    ?>
                </a>
                <a href="my-cart.php" class="position-relative">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <?php if(!empty($_SESSION['cart'])): ?>
                        <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <?php if(empty($_SESSION['login'])): ?>
                    <a href="login.php"><i class="fa-regular fa-user"></i></a>
                <?php else: ?>
                    <a href="my-account.php"><i class="fa-solid fa-user-check"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="nav-menu d-none d-md-flex justify-content-center pb-3">
            <a href="index.php" class="small fw-bold px-4 text-dark text-decoration-none">NEW ARRIVALS</a>
            <a href="category.php?cid=1" class="small fw-bold px-4 text-muted text-decoration-none">SHOP ALL</a>
            <a href="category.php?cid=2" class="small fw-bold px-4 text-muted text-decoration-none">COLLECTIONS</a>
            <a href="about-us.php" class="small fw-bold px-4 text-muted text-decoration-none">OUR STORY</a>
        </nav>
    </div>
</header>

<main class="container py-4">
    <div class="hero-card">
        <div class="hero-text">
            <p class="small mb-2 fw-bold text-white">SPRING / SUMMER 2026</p>
            <h1>TIMELESS<br>ESSENTIALS.</h1>
            <a href="#shop" class="btn btn-light rounded-0 px-5 py-3 mt-4 fw-bold">SHOP NOW</a>
        </div>
    </div>

    <div class="row" id="shop">
        <div class="col-12 mb-5 d-flex justify-content-between align-items-end">
            <div>
                <h3 class="fw-bold mb-0" style="letter-spacing:-1px">Latest Drops</h3>
                <p class="text-muted small">Eksklusif dikurasi untuk Anda.</p>
            </div>
            <a href="category.php?cid=1" class="text-dark fw-bold small text-decoration-none border-bottom border-1">View Collection</a>
        </div>

        <?php
        $ret = mysqli_query($con, "SELECT * FROM products WHERE productPrice > 0 AND productName != '' ORDER BY rand() LIMIT 8");
        if ($ret && mysqli_num_rows($ret) > 0) {
            while ($row = mysqli_fetch_array($ret)) {
                // SINKRONISASI STOK: Deteksi apakah status produk 'Out of Stock'
                $availability = isset($row['productAvailability']) ? trim($row['productAvailability']) : '';
                $is_sold_out = (strcasecmp($availability, 'Out of Stock') == 0 || $availability == '');
        ?>
        <div class="col-6 col-md-3">
            <div class="product-item" style="<?php echo $is_sold_out ? 'opacity: 0.6;' : ''; ?>">
                
                <div class="img-wrapper">
                    <?php if($is_sold_out): ?>
                        <div class="sold-out-badge">
                            <i class="fa-solid fa-circle-minus me-1"></i> Sold Out
                        </div>
                    <?php endif; ?>

                    <a href="product-details.php?pid=<?php echo $row['id'];?>">
                        <img src="admin/productimages/<?php echo $row['id'];?>/<?php echo $row['productImage1'];?>" 
                             onerror="this.onerror=null; this.src='https://placehold.co/450x600/e8e4d8/111111?text=No+Image+Available';" 
                             alt="">
                    </a>

                    <div class="action-overlay">
                        <?php if($is_sold_out): ?>
                            <button class="btn-quick-add text-center" disabled style="background:#e0e0e0; color:#888; cursor:not-allowed;">
                                STOK HABIS
                            </button>
                        <?php else: ?>
                            <a href="<?php echo (empty($_SESSION['login'])) ? 'login.php' : 'payment-method.php?id='.$row['id']; ?>" class="btn-quick-add text-center text-decoration-none" style="background:#fff; color:#000;">
                                Beli Sekarang
                            </a>
                            <button onclick="addToCart(<?php echo $row['id']; ?>)" class="btn-quick-add" id="btn-<?php echo $row['id']; ?>">
                                + Keranjang
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-info">
                    <span class="product-brand">Garage Sale Studio</span>
                    <a href="product-details.php?pid=<?php echo $row['id'];?>" class="product-name-link"><?php echo htmlentities($row['productName']);?></a>
                    <div class="price">
                        <?php if($is_sold_out): ?>
                            <span class="text-decoration-line-through text-muted opacity-50 me-1" style="font-size:12px;">Rp <?php echo number_format($row['productPrice'], 0, ',', '.'); ?></span>
                            <span class="text-danger fw-bold" style="font-size:13px;">TERJUAL</span>
                        <?php else: ?>
                            Rp <?php echo number_format($row['productPrice'], 0, ',', '.'); ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center py-5'><p class='text-muted'>Belum ada katalog pakaian yang tersedia.</p></div>";
        }
        ?>
    </div>
    
    <section class="row g-4 my-5 pt-5">
        <div class="col-6 col-md-3 text-center">
            <div class="service-card h-100">
                <i class="fa-solid fa-shield-check fs-2 mb-3" style="color:var(--accent)"></i>
                <h6 class="fw-bold small">Original Only</h6>
                <p class="text-muted extra-small mb-0" style="font-size: 11px;">Jaminan produk 100% asli.</p>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="service-card h-100">
                <i class="fa-solid fa-truck-fast fs-2 mb-3" style="color:var(--accent)"></i>
                <h6 class="fw-bold small">Fast Delivery</h6>
                <p class="text-muted extra-small mb-0" style="font-size: 11px;">Pengiriman harian aman.</p>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="service-card h-100">
                <i class="fa-solid fa-arrow-right-arrow-left fs-2 mb-3" style="color:var(--accent)"></i>
                <h6 class="fw-bold small">Easy Returns</h6>
                <p class="text-muted extra-small mb-0" style="font-size: 11px;">Kebijakan retur 7 hari.</p>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="service-card h-100">
                <i class="fa-solid fa-headset fs-2 mb-3" style="color:var(--accent)"></i>
                <h6 class="fw-bold small">Expert Support</h6>
                <p class="text-muted extra-small mb-0" style="font-size: 11px;">Bantuan belanja 24/7.</p>
            </div>
        </div>
    </section>
</main>

<footer class="footer-main pb-5">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-4 col-md-12">
                <a href="index.php" class="logo d-block mb-4">Garage Sale.</a>
                <p class="small text-muted mb-4 pe-lg-5">Kurasi fashion minimalis untuk gaya hidup modern.</p>
                <div class="newsletter-pill">
                    <input type="text" placeholder="Ikuti kami di Instagram" class="form-control border-0 bg-transparent ps-3 shadow-none small" readonly>
                    <a href="https://www.instagram.com/garage_icha/?hl=en" target="_blank" class="btn btn-dark rounded-pill px-4 fw-bold small ls-1">FOLLOW</a>
                </div>
            </div>
            <div class="col-6 col-lg-2 offset-lg-1">
                <h6 class="small fw-bold mb-4 ls-1">LINKS</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="#" class="text-reset text-decoration-none">New Arrivals</a></li>
                    <li class="mb-2"><a href="order-history.php" class="text-reset text-decoration-none">Track Order</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-12 text-lg-end">
                <h6 class="small fw-bold mb-4 ls-1">CONNECT</h6>
                <div class="d-flex justify-content-lg-end gap-3">
                    <a href="#" class="text-dark fs-5"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-dark fs-5"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center opacity-75">
            <p class="small text-muted mb-0">&copy; 2026 Garage Sale Store. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleSearch() { $('#search-overlay').toggleClass('active'); }

    function addToCart(pid) {
        const btn = $('#btn-' + pid);
        const originalText = btn.html();
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');
        $.ajax({
            url: 'index.php?action=add&ajax=1&id=' + pid,
            success: function() {
                btn.html('<i class="fa-solid fa-check"></i>');
                btn.css('background', '#27ae60');
                setTimeout(() => {
                    btn.html(originalText);
                    btn.css('background', '#000');
                    location.reload();
                }, 800);
            }
        });
    }

    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) { $('#mainHeader').addClass('shrink'); }
        else { $('#mainHeader').removeClass('shrink'); }
    });
</script>

</body>
</html>
