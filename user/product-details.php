<?php 
session_start();
error_reporting(0); 
include('includes/config.php');

// 1. Logika Add to Cart & Buy Now
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id = intval($_GET['id']);
    
    // Fitur Beli Langsung: Kosongkan keranjang lama agar fokus ke produk ini saja
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
        header('location:checkout.php'); 
    } else {
        echo "<script>alert('Produk berhasil ditambahkan ke keranjang'); window.location.href='my-cart.php'</script>";
    }
    exit();
}

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

        /* --- Container & Card --- */
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
        }
        .product-img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .image-wrapper:hover .product-img { transform: scale(1.08); }

        /* --- Text Styling --- */
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

        /* --- Action Buttons --- */
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
        .btn-main:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .btn-icon {
            width: 64px; height: 64px;
            border-radius: 18px;
            border: 1px solid #e0e0e0;
            background: white;
            display: flex; align-items: center; justify-content: center;
            color: var(--thrift-dark); transition: 0.3s;
        }

        /* --- Info Section --- */
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

        /* --- Toast --- */
        #custom-toast {
            visibility: hidden; background: var(--thrift-dark);
            color: white; padding: 12px 24px; border-radius: 12px;
            position: fixed; bottom: 30px; left: 50%;
            transform: translateX(-50%); z-index: 1000; font-weight: 600;
        }
        #custom-toast.show { visibility: visible; animation: fadeInUp 0.4s; }

        @keyframes fadeInUp { from { opacity:0; transform: translate(-50%, 20px); } to { opacity:1; transform: translate(-50%, 0); } }

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
    $ret = mysqli_query($con, "SELECT * FROM products WHERE id='$pid'");
    while($row = mysqli_fetch_array($ret)) {
    ?>
    
    <div class="glass-container">
        <div class="row">
            <div class="col-md-5">
                <div class="image-wrapper">
                    <img src="admin/productimages/<?php echo $row['id'];?>/<?php echo $row['productImage1'];?>" class="product-img" alt="Product Image">
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
                        <span class="small text-muted">(Ready Stock — 1 of 1)</span>
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
                        <a href="product-details.php?action=add&id=<?php echo $row['id']; ?>&pid=<?php echo $row['id']; ?>" class="btn-main btn-outline">
                            <i class="fa-solid fa-cart-shopping me-2"></i> + Keranjang
                        </a>
                        <a href="product-details.php?action=add&id=<?php echo $row['id']; ?>&pid=<?php echo $row['id']; ?>&buy=now" class="btn-main btn-dark">
                            Checkout Sekarang
                        </a>
                        <button class="btn-icon" onclick="copyLink()">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>

                    <div class="details-section">
                        <h5>Info Produk & Kondisi</h5>
                        <div class="desc-text">
                            <?php echo $row['productDescription'];?>
                        </div>
                        <div class="mt-4 p-3" style="background: var(--bg-canvas); border-radius: 12px; font-size: 13px;">
                            <i class="fa-solid fa-shield-heart me-2"></i> 
                            Barang ini adalah <b>Thrift/Pre-loved</b> yang sudah dicuci bersih dan dikurasi kualitasnya.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div id="custom-toast">Link disalin ke clipboard!</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    const toast = document.getElementById("custom-toast");
    toast.classList.add("show");
    setTimeout(function(){ toast.classList.remove("show"); }, 3000);
}
</script>

</body>
</html>