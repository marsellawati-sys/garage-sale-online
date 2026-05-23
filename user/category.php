<?php
session_start();
// Aktifkan error reporting untuk pengembangan
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// --- LOGIKA KERANJANG (CART) ---
$total_price = 0; 
if(!empty($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $id => $data) {
        $id = intval($id);
        $sql_p = mysqli_query($con, "SELECT productPrice FROM products WHERE id=$id");
        if($sql_p && $row_p = mysqli_fetch_array($sql_p)){
            $qty = is_array($data) ? (isset($data['quantity']) ? $data['quantity'] : 0) : $data;
            $total_price += ($row_p['productPrice'] * $qty);
        }
    }
}

// Logika Tambah ke Keranjang
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id = intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])){
        if(is_array($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id]++;
        }
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    
    if(isset($_GET['ajax'])){ exit; } 
    header('location:category.php?cid='.intval($_GET['cid']));
    exit();
}

$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

// --- DEFINISI QUERY KATEGORI ---
$sql_cat = mysqli_query($con, "SELECT id, categoryName FROM category");

// Ambil Nama Kategori untuk Judul
$cat_title = "Semua Koleksi"; // Default jika cid = 0
if($cid != 0) {
    $get_cat_name = mysqli_query($con, "SELECT categoryName FROM category WHERE id='$cid'");
    if($cn_row = mysqli_fetch_array($get_cat_name)) {
        $cat_title = $cn_row['categoryName'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Sale | <?php echo $cat_title; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-base: #fdfcfb;
            --text-main: #111111;
            --accent: #d4a373;
            --border: #e8e4d8;
            --header-height: 140px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            padding-top: var(--header-height);
        }

        .header-modern {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: fixed; top: 0; width: 100%; z-index: 1000;
        }
        .nav-top-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-weight: 900; font-size: 26px; letter-spacing: -1.5px; text-decoration: none; color: var(--text-main); }
        
        .header-tools { display: flex; gap: 20px; align-items: center; }
        .cart-pill {
            background: var(--text-main); color: #fff; padding: 8px 18px;
            border-radius: 50px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; text-decoration: none;
        }

        .category-nav { display: flex; justify-content: center; gap: 30px; padding-bottom: 15px; }
        .cat-link { text-decoration: none; color: #888; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .cat-link.active { color: var(--text-main); border-bottom: 2px solid var(--text-main); }

        .sidebar-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #aaa; margin-bottom: 20px; }
        .side-cat-list { list-style: none; padding: 0; }
        .side-cat-item a {
            display: block; padding: 12px 0; text-decoration: none; color: #666;
            font-size: 14px; border-bottom: 1px solid var(--border); transition: 0.3s;
        }
        .side-cat-item a:hover, .side-cat-item a.active { color: var(--text-main); font-weight: 700; transform: translateX(5px); }

        .btn-back-home {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: var(--text-main); font-size: 12px;
            font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back-home:hover { color: var(--accent); transform: translateX(-5px); }

        .product-card {
            background: #fff; border-radius: 24px; padding: 15px;
            border: 1px solid var(--border); transition: 0.4s; height: 100%;
        }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        
        .img-container {
            aspect-ratio: 1/1.2; overflow: hidden; border-radius: 18px;
            background: #f9f9f9; margin-bottom: 15px;
        }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }

        .btn-action-row { display: flex; gap: 8px; margin-top: 15px; }
        .btn-bag { width: 45px; height: 45px; border-radius: 12px; border: 1px solid var(--border); background: #fff; cursor: pointer; }
        .btn-buy {
            flex-grow: 1; background: var(--text-main); color: #fff;
            border-radius: 12px; font-size: 11px; font-weight: 800; text-decoration: none;
            display: flex; align-items: center; justify-content: center;
        }

        .footer-premium { background: #fff; border-top: 1px solid var(--border); padding: 60px 0; margin-top: 100px; }
    </style>
</head>
<body>

<header class="header-modern">
    <div class="container">
        <div class="nav-top-row">
            <a href="index.php" class="logo">Garage Sale.</a>
            <div class="header-tools">
                <a href="my-cart.php" class="cart-pill">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                </a>
                <?php if(empty($_SESSION['login'])): ?>
                    <a href="login.php" class="text-dark"><i class="fa-regular fa-user"></i></a>
                <?php else: ?>
                    <a href="my-account.php" class="text-dark"><i class="fa-solid fa-user-check"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="category-nav">
            <a href="category.php" class="cat-link <?php echo ($cid == 0) ? 'active' : ''; ?>">Semua</a>
            <?php 
            mysqli_data_seek($sql_cat, 0); 
            while($row_cat = mysqli_fetch_array($sql_cat)) {
                $active = ($cid == $row_cat['id']) ? 'active' : '';
                echo '<a href="category.php?cid='.$row_cat['id'].'" class="cat-link '.$active.'">'.$row_cat['categoryName'].'</a>';
            }
            ?>
        </nav>
    </div>
</header>

<main class="container-fluid px-md-5 mt-4">
    <div class="row">
        <aside class="col-md-2 d-none d-md-block pe-lg-5">
            <h6 class="sidebar-title">Filter Kategori</h6>
            <ul class="side-cat-list">
                <li class="side-cat-item"><a href="category.php" class="<?php echo ($cid == 0) ? 'active' : ''; ?>">Semua Produk</a></li>
                <?php 
                mysqli_data_seek($sql_cat, 0); 
                while($row_side = mysqli_fetch_array($sql_cat)) {
                    $s_active = ($cid == $row_side['id']) ? 'active' : '';
                    echo '<li class="side-cat-item"><a href="category.php?cid='.$row_side['id'].'" class="'.$s_active.'">'.$row_side['categoryName'].'</a></li>';
                }
                ?>
            </ul>
        </aside>

        <div class="col-md-10">
            <a href="index.php" class="btn-back-home">
                <i class="fa-solid fa-chevron-left"></i> Kembali ke Beranda
            </a>

            <div class="mb-5">
                <h2 class="fw-800 mb-1"><?php echo strtoupper($cat_title); ?></h2>
                <p class="text-muted small">Menampilkan kurasi produk terbaik kami.</p>
            </div>

            <div class="row g-4">
                <?php
                // LOGIKA QUERY: Ambil semua/kategori & Sembunyikan Harga 0
                if($cid == 0) {
                    $ret = mysqli_query($con, "SELECT * FROM products WHERE productPrice > 0 ORDER BY id DESC");
                } else {
                    $ret = mysqli_query($con, "SELECT * FROM products WHERE category='$cid' AND productPrice > 0 ORDER BY id DESC");
                }

                if(mysqli_num_rows($ret) > 0) {
                    while($row = mysqli_fetch_array($ret)) {
                ?>
                    <div class="col-6 col-lg-3">
                        <div class="product-card">
                            <div class="img-container">
                                <a href="product-details.php?pid=<?php echo $row['id']; ?>">
                                    <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" alt="">
                                </a>
                            </div>
                            <a href="product-details.php?pid=<?php echo $row['id']; ?>" class="text-dark text-decoration-none fw-bold small d-block mb-1 text-truncate">
                                <?php echo $row['productName']; ?>
                            </a>
                            <div class="fw-800 text-accent small">
                                Rp <?php echo number_format($row['productPrice'], 0, ',', '.'); ?>
                            </div>
                            
                            <div class="btn-action-row">
                                <button onclick="addToCart(<?php echo $row['id']; ?>, <?php echo $cid; ?>)" class="btn-bag" id="btn-<?php echo $row['id']; ?>">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                                
                                <?php if(empty($_SESSION['login'])): ?>
                                    <a href="login.php" class="btn-buy">BELI</a>
                                <?php else: ?>
                                    <a href="payment-method.php?id=<?php echo $row['id']; ?>" class="btn-buy">BELI</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                    } 
                } else { 
                ?>
                    <div class="col-12 text-center py-5">
                        <h5 class="text-muted">Produk tidak ditemukan atau koleksi sedang kosong.</h5>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<footer class="footer-premium">
    <div class="container text-center">
        <h4 class="logo mb-2">Garage Sale.</h4>
        <p class="small text-muted">© 2026 Garage Sale Store.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addToCart(pid, cid) {
    const btn = $('#btn-' + pid);
    btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');
    $.ajax({
        url: 'category.php?action=add&ajax=1&id=' + pid + '&cid=' + cid,
        success: function() {
            btn.html('<i class="fa-solid fa-check text-success"></i>');
            setTimeout(() => { 
                btn.html('<i class="fa-solid fa-cart-plus"></i>'); 
                location.reload(); 
            }, 600);
        }
    });
}
</script>

</body>
</html>