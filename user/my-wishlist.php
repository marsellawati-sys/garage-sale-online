<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Proteksi: Jika belum login, lempar ke halaman login
if(strlen($_SESSION['login'])==0) {   
    header('location:login.php');
} else {
    $uid = $_SESSION['id'];

    // Logika Menambahkan ke Wishlist (Jika dipanggil dari index)
    if(isset($_GET['pid']) && $_GET['action']=="add" ) {
        $pid = intval($_GET['pid']);
        $query = mysqli_query($con, "SELECT id FROM wishlist WHERE userId='$uid' AND productId='$pid'");
        if(mysqli_num_rows($query) > 0) {
            echo "<script>alert('Produk sudah ada di wishlist');</script>";
        } else {
            mysqli_query($con, "INSERT INTO wishlist(userId,productId) VALUES('$uid','$pid')");
            echo "<script>alert('Produk ditambahkan ke wishlist');</script>";
        }
        header('location:my-wishlist.php');
    }

    // Logika Menghapus dari Wishlist
    if(isset($_GET['delid'])) {
        $delid = intval($_GET['delid']);
        mysqli_query($con, "DELETE FROM wishlist WHERE id='$delid' AND userId='$uid'");
        header('location:my-wishlist.php');
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Garage Sale</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        :root {
            --bg-base: #fdfcfb;
            --text-main: #111111;
            --accent: #d4a373;
            --border: #e8e4d8;
            --header-height: 100px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            padding-top: var(--header-height);
        }

        /* Reusable Header Style from Index */
        .header-modern {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: fixed; top: 0; width: 100%; z-index: 1000;
            padding: 15px 0;
        }
        .logo { font-weight: 900; font-size: 24px; letter-spacing: -1px; text-decoration: none; color: #111; }

        .page-title { font-weight: 800; font-size: 32px; letter-spacing: -1px; margin-bottom: 30px; }

        /* Wishlist Table/List Style */
        .wishlist-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .wishlist-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

        .prod-img { width: 100px; height: 100px; border-radius: 15px; object-fit: cover; margin-right: 20px; }
        
        .prod-info { flex-grow: 1; }
        .prod-name { font-weight: 700; color: #111; text-decoration: none; display: block; margin-bottom: 5px; }
        .prod-price { font-weight: 600; color: var(--accent); }

        .btn-remove {
            color: #ff4757; background: #fff1f2; border: none;
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s; text-decoration: none;
        }
        .btn-remove:hover { background: #ff4757; color: #fff; }

        .btn-add-cart {
            background: var(--text-main); color: #fff;
            padding: 10px 20px; border-radius: 12px;
            font-size: 12px; font-weight: 700; text-decoration: none;
            margin-right: 15px; transition: 0.3s;
        }
        .btn-add-cart:hover { background: #333; color: #fff; }

        .empty-state { text-align: center; padding: 100px 0; }
    </style>
</head>
<body>

<header class="header-modern">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="logo">Garage Sale.</a>
        <div class="tools">
            <a href="index.php" class="text-dark text-decoration-none fw-bold small"><i class="fa-solid fa-arrow-left me-2"></i> Kembali Belanja</a>
        </div>
    </div>
</header>

<main class="container">
    <h1 class="page-title">Wishlist Saya</h1>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <?php
            $query = mysqli_query($con, "SELECT products.productName AS pname, products.productImage1 AS pimage, products.productPrice AS pprice, products.id AS pid, wishlist.id AS wid FROM wishlist JOIN products ON products.id=wishlist.productId WHERE wishlist.userId='$uid'");
            $num = mysqli_num_rows($query);
            if($num > 0) {
                while($row = mysqli_fetch_array($query)) {
            ?>
                <div class="wishlist-card">
                    <img src="admin/productimages/<?php echo $row['pid'];?>/<?php echo $row['pimage'];?>" class="prod-img" alt="">
                    
                    <div class="prod-info">
                        <a href="product-details.php?pid=<?php echo $row['pid'];?>" class="prod-name"><?php echo $row['pname'];?></a>
                        <div class="prod-price">Rp <?php echo number_format($row['pprice'], 0, ',', '.'); ?></div>
                    </div>

                    <div class="d-flex align-items-center">
                        <a href="index.php?page=product&action=add&id=<?php echo $row['pid']; ?>" class="btn-add-cart">
                            <i class="fa-solid fa-cart-plus me-2"></i> TAMBAH KE KERANJANG
                        </a>
                        
                        <a href="my-wishlist.php?delid=<?php echo $row['wid'];?>" class="btn-remove" onclick="return confirm('Hapus dari wishlist?')">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                </div>
            <?php 
                } 
            } else { ?>
                <div class="empty-state">
                    <i class="fa-regular fa-heart mb-4 opacity-25" style="font-size: 80px;"></i>
                    <h4 class="fw-bold">Wishlist Anda Kosong</h4>
                    <p class="text-muted">Belum ada barang yang Anda simpan.</p>
                    <a href="category.php" class="btn btn-dark rounded-pill px-5 mt-3">Mulai Cari Produk</a>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php } ?>