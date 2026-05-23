<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');

// Proteksi Login
if(empty($_SESSION['login'])) {   
    header('location:login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesanan Saya | Garage Sale</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .order-card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: #fff; margin-bottom: 20px; overflow: hidden; }
        .order-header { background: #fdfdfd; border-bottom: 1px solid #eee; padding: 15px 20px; }
        .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .product-img { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; }
        .btn-detail { border-radius: 10px; font-weight: 600; padding: 8px 20px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Riwayat Pesanan</h2>
        <a href="index.php" class="btn btn-outline-dark btn-sm"><i class="fa fa-arrow-left"></i> Kembali Belanja</a>
    </div>

    <?php
    $uid = $_SESSION['id'];
    // Query untuk mengambil detail pesanan dan nama produk
    $query = mysqli_query($con, "SELECT orders.id as oid, products.productName as pname, products.productImage1 as pimg, products.productPrice as pprice, orders.quantity as qty, orders.orderStatus as status, orders.orderDate as odate, orders.paymentMethod as paym 
                                 FROM orders 
                                 JOIN products ON orders.productId = products.id 
                                 WHERE orders.userId='$uid' 
                                 ORDER BY orders.orderDate DESC");
    
    $cnt = mysqli_num_rows($query);
    if($cnt > 0) {
        while($row = mysqli_fetch_array($query)) {
    ?>
    <div class="order-card p-0">
        <div class="order-header d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small">Tanggal Pesanan:</span> 
                <span class="fw-bold small"><?= $row['odate']; ?></span>
                <span class="mx-2">|</span>
                <span class="text-muted small">Metode:</span> 
                <span class="badge bg-light text-dark"><?= $row['paym']; ?></span>
            </div>
            <span class="status-badge status-pending"><?= $row['status']; ?></span>
        </div>
        <div class="p-4">
            <div class="row align-items-center">
                <div class="col-md-1 col-3">
                    <img src="admin/productimages/<?= $row['pimg']; ?>" class="product-img" alt="produk">
                </div>
                <div class="col-md-7 col-9">
                    <h5 class="fw-bold mb-1"><?= $row['pname']; ?></h5>
                    <p class="text-muted mb-0 small"><?= $row['qty']; ?> Barang x Rp <?= number_format($row['pprice'],0,',','.'); ?></p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="text-muted small">Total Belanja</div>
                    <h4 class="fw-bold text-primary">Rp <?= number_format($row['pprice'] * $row['qty'],0,',','.'); ?></h4>
                    <a href="order-details.php?id=<?= $row['oid']; ?>" class="btn btn-dark btn-detail btn-sm mt-2">Lihat Detail</a>
                </div>
            </div>
        </div>
    </div>
    <?php 
        } 
    } else {
        echo "<div class='text-center py-5'><i class='fa fa-shopping-basket fa-3x text-muted mb-3'></i><p>Belum ada pesanan.</p></div>";
    }
    ?>
</div>

</body>
</html>