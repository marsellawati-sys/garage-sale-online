<?php

session_start();

error_reporting(0);

include('includes/config.php');



if(strlen($_SESSION['login'])==0) {  

    header('location:login.php');

} else {

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>garagesale| Riwayat Pesanan</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

   

    <style>

        :root {

            --bg-body: #fdfcfb;

            --beige-border: #e8e4d8;

            --accent: #111111;

            --gold-muted: #d4a373;

            --white: #ffffff;

        }



        body {

            font-family: 'Plus Jakarta Sans', sans-serif;

            background-color: var(--bg-body);

            color: var(--accent);

            letter-spacing: -0.2px;

        }



        /* Minimalist Header */

        .header-nav {

            background: var(--white);

            border-bottom: 1px solid var(--beige-border);

            padding: 15px 0;

            margin-bottom: 40px;

            position: sticky;

            top: 0;

            z-index: 1000;

        }

        .logo-text { font-weight: 900; font-size: 22px; letter-spacing: -1.5px; text-decoration: none; color: var(--accent); }



        /* Back Button Style */

        .btn-back-round {

            width: 45px;

            height: 45px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 1px solid var(--beige-border);

            background: var(--white);

            color: var(--accent);

            transition: all 0.3s ease;

            text-decoration: none;

        }

        .btn-back-round:hover {

            background: var(--accent);

            color: var(--white);

            transform: translateX(-5px);

        }



        .page-header {

            display: flex;

            align-items: center;

            gap: 20px;

            margin-bottom: 35px;

        }



        .page-title {

            font-weight: 800;

            font-size: 32px;

            letter-spacing: -1px;

            margin: 0;

        }



        /* Order Card Style */

        .order-card {

            background: var(--white);

            border: 1px solid var(--beige-border);

            border-radius: 20px;

            padding: 25px;

            margin-bottom: 20px;

            transition: all 0.3s ease;

        }

        .order-card:hover {

            box-shadow: 0 15px 35px rgba(0,0,0,0.05);

        }



        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            border-bottom: 1px solid #f8f8f8;

            padding-bottom: 15px;

            margin-bottom: 20px;

        }



        .status-badge {

            background: #f0fdf4;

            color: #16a34a;

            padding: 6px 14px;

            border-radius: 100px;

            font-size: 11px;

            font-weight: 700;

            text-transform: uppercase;

        }



        .product-img {

            width: 80px;

            height: 80px;

            object-fit: cover;

            border-radius: 12px;

            background: #f9f9f9;

        }



        .product-name {

            font-weight: 700;

            font-size: 16px;

            color: var(--accent);

            text-decoration: none;

            display: block;

            margin-bottom: 4px;

        }



        .price-text {

            font-weight: 800;

            font-size: 18px;

            color: var(--accent);

        }



        .meta-label {

            font-size: 11px;

            font-weight: 600;

            color: #a0a0a0;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            margin-bottom: 2px;

        }



        .btn-track {

            background: var(--accent);

            color: var(--white);

            border: none;

            padding: 12px 24px;

            border-radius: 12px;

            font-weight: 700;

            font-size: 13px;

            transition: 0.3s;

        }

        .btn-track:hover {

            background: #333;

            transform: translateY(-2px);

        }



        .empty-state {

            text-align: center;

            padding: 80px 0;

            background: #fff;

            border-radius: 30px;

            border: 1px dashed var(--beige-border);

        }

    </style>



    <script>

        function popUpWindow(URLStr) {

            window.open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=600,height=600');

        }

    </script>

</head>

<body>



<nav class="header-nav">

    <div class="container d-flex justify-content-between align-items-center">

        <a href="index.php" class="logo-text">GarageSale.</a>

        <div class="d-flex align-items-center gap-4">

            <a href="index.php" class="text-muted text-decoration-none small fw-bold">SHOP</a>

            <a href="logout.php" class="text-danger text-decoration-none small fw-bold">LOGOUT</a>

        </div>

    </div>

</nav>



<div class="container mb-5">

    <div class="page-header">

        <a href="index.php" class="btn-back-round" title="Kembali ke Beranda">

            <i class="fa-solid fa-arrow-left"></i>

        </a>

        <h1 class="page-title">Riwayat Pesanan</h1>

    </div>



    <?php

    // Query untuk mengambil data pesanan yang sudah memiliki metode pembayaran (sukses checkout)

    $query = mysqli_query($con, "SELECT products.productImage1 as pimg1, products.productName as pname, products.id as proid, orders.productId as opid, orders.quantity as qty, products.productPrice as pprice, products.shippingCharge as shippingcharge, orders.paymentMethod as paym, orders.orderDate as odate, orders.id as orderid FROM orders JOIN products ON orders.productId=products.id WHERE orders.userId='".$_SESSION['id']."' AND orders.paymentMethod IS NOT NULL ORDER BY odate DESC");

   

    $cnt = mysqli_num_rows($query);

    if($cnt > 0) {

        while($row = mysqli_fetch_array($query)) {

            $qty = (int)$row['qty'];

            $price = (float)$row['pprice'];

            $shipping = (float)$row['shippingcharge'];

            $grandtotal = ($qty * $price) + $shipping;

    ?>



    <div class="order-card">

        <div class="order-header">

            <div class="d-flex gap-4">

                <div>

                    <div class="meta-label">ID PESANAN</div>

                    <div class="fw-bold small">#id-<?php echo $row['orderid']; ?></div>

                </div>

                <div>

                    <div class="meta-label">METODE</div>

                    <div class="fw-bold small text-uppercase"><?php echo $row['paym']; ?></div>

                </div>

            </div>

            <div class="text-end">

                <div class="meta-label">TANGGAL TRANSAKSI</div>

                <div class="fw-bold small"><?php echo date('d M Y, H:i', strtotime($row['odate'])); ?></div>

            </div>

        </div>



        <div class="row align-items-center">

            <div class="col-md-auto">

                <img src="admin/productimages/<?php echo $row['proid'];?>/<?php echo $row['pimg1'];?>" class="product-img" alt="Produk">

            </div>

            <div class="col-md">

                <div class="meta-label">NAMA PRODUK</div>

                <a href="product-details.php?pid=<?php echo $row['opid'];?>" class="product-name"><?php echo $row['pname'];?></a>

                <span class="small text-muted"><?php echo $qty; ?> Unit × Rs. <?php echo number_format($price, 0, ',', '.'); ?></span>

            </div>

            <div class="col-md-3 text-md-end">

                <div class="meta-label">TOTAL PEMBAYARAN</div>

                <div class="price-text mb-2">Rs. <?php echo number_format($grandtotal, 0, ',', '.'); ?></div>

                <span class="status-badge">Pesanan Diproses</span>

            </div>

            <div class="col-md-auto ps-md-4 text-end">

                <button onClick="popUpWindow('track-order.php?oid=<?php echo $row['orderid'];?>');" class="btn-track">

                    LACAK PAKET

                </button>

            </div>

        </div>

    </div>



    <?php

        }

    } else { ?>

        <div class="empty-state">

            <i class="fa-solid fa-box-open mb-3" style="font-size: 50px; color: var(--beige-border);"></i>

            <h4 class="fw-bold">Belum ada jejak pesanan</h4>

            <p class="text-muted small mb-4">Mungkin ini saat yang tepat untuk mulai berbelanja sesuatu yang baru.</p>

            <a href="index.php" class="btn btn-dark px-5 py-3 rounded-pill fw-bold" style="font-size: 13px;">JELAJAHI PRODUK</a>

        </div>

    <?php } ?>



</div>



<footer class="py-5 mt-5">

    <div class="container text-center">

        <p class="small text-muted" style="letter-spacing: 1px;">&copy; 2026 GargaeSale STUDIO. SEMUA HAK DILINDUNGI.</p>

    </div>

</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php } ?>