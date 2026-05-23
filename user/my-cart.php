<?php
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include('includes/config.php');

// --- 1. LOGIKA UPDATE & HAPUS ---

// Fitur Hapus via URL (Lebih Instan)
if(isset($_GET['del'])){
    $id_to_del = $_GET['del'];
    unset($_SESSION['cart'][$id_to_del]);
    echo "<script>window.location='my-cart.php';</script>";
}

if(isset($_POST['submit'])){
    if(!empty($_SESSION['cart'])){
        // Logika Update Quantity
        foreach($_POST['quantity'] as $key => $val){
            if((int)$val <= 0){
                unset($_SESSION['cart'][$key]);
            } else {
                if(is_array($_SESSION['cart'][$key])){
                    $_SESSION['cart'][$key]['quantity'] = (int)$val;
                } else {
                    $_SESSION['cart'][$key] = (int)$val;
                }
            }
        }
        echo "<script>alert('Keranjang diperbarui!'); window.location='my-cart.php';</script>";
    }
}

// --- 2. LOGIKA HITUNG TOTAL ---
$total_price = 0; 
$total_qty = 0;

if(!empty($_SESSION['cart']) && is_array($_SESSION['cart'])){
    $p_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $p_ids);
    $query_h = mysqli_query($con, "SELECT id, productPrice FROM products WHERE id IN ($ids_string)");
    
    if($query_h){
        while($row_h = mysqli_fetch_array($query_h)){
            $item_id = $row_h['id'];
            $val = $_SESSION['cart'][$item_id];
            $qty = is_array($val) ? (int)$val['quantity'] : (int)$val;
            $price = (float)$row_h['productPrice'];
            $total_price += ($price * $qty);
            $total_qty += $qty;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>NexStore | Keranjang Belanja</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --beige-bg: #fdfcfb;
            --beige-border: #e8e4d8;
            --accent-dark: #111111;
            --danger-soft: #fff1f0;
            --danger-red: #ff4d4f;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; }
        .logo { font-weight: 900; font-size: 28px; letter-spacing: -1.5px; text-decoration: none; color: var(--accent-dark); }
        .top-brand-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; border-bottom: 1px solid var(--beige-border); }
        
        .cart-section { padding: 50px 5%; }
        .cart-table-card { border: 1px solid var(--beige-border); border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .table thead { background: var(--beige-bg); border-bottom: 1px solid var(--beige-border); }
        .table th { padding: 20px; font-weight: 700; color: #888; text-transform: uppercase; font-size: 12px; }
        
        .cart-img { width: 90px; height: 90px; object-fit: contain; border-radius: 15px; background: #f9f9f9; padding: 5px; }
        .qty-input { width: 70px; padding: 8px; border-radius: 10px; border: 1px solid var(--beige-border); text-align: center; font-weight: 700; }
        
        /* Tombol Hapus Modern */
        .btn-delete { 
            width: 40px; height: 40px; border-radius: 12px; 
            background: var(--danger-soft); color: var(--danger-red); 
            display: flex; align-items: center; justify-content: center; 
            text-decoration: none; transition: 0.2s; border: none;
        }
        .btn-delete:hover { background: var(--danger-red); color: #fff; transform: scale(1.1); }

        .summary-card { background: var(--accent-dark); color: #fff; border-radius: 28px; padding: 35px; position: sticky; top: 30px; }
        .btn-to-payment { 
            background: #fff; color: #000; width: 100%; padding: 20px; 
            border-radius: 18px; font-weight: 800; border: none; margin-top: 20px; 
            display: block; text-align: center; text-decoration: none; transition: 0.3s; 
        }
        .btn-to-payment:hover { background: #eee; transform: translateY(-3px); color: #000; }
    </style>
</head>
<body>

<div class="top-brand-row">
    <a href="index.php" class="logo">GarageSale.</a>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted fw-bold">Total: Rs. <?php echo number_format($total_price, 0, ',', '.'); ?></span>
    </div>
</div>

<div class="cart-section">
    <div class="row g-5">
        <div class="col-lg-8">
            <h2 class="mb-4 fw-800">Keranjang Belanja</h2>
            <form name="cart" method="post">
                <div class="cart-table-card">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($_SESSION['cart'])){
                                $p_ids = array_keys($_SESSION['cart']);
                                $sql = "SELECT * FROM products WHERE id IN (".implode(',', $p_ids).")";
                                $query = mysqli_query($con, $sql);
                                
                                while($row = mysqli_fetch_array($query)){
                                    $val = $_SESSION['cart'][$row['id']];
                                    $qty = is_array($val) ? (int)$val['quantity'] : (int)$val;
                                    $price = (float)$row['productPrice'];
                                    $subtotal = $qty * $price; 
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3 py-3">
                                        <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" class="cart-img">
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo $row['productName']; ?></div>
                                            <div class="small text-muted">Kategori: Gadget</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-600">Rs. <?php echo number_format($price, 0, ',', '.'); ?></td>
                                <td>
                                    <input type="number" class="qty-input" value="<?php echo $qty; ?>" name="quantity[<?php echo $row['id']; ?>]" min="1">
                                </td>
                                <td class="fw-800 text-dark">Rs. <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="my-cart.php?del=<?php echo $row['id']; ?>" 
                                       class="btn-delete mx-auto" 
                                       onclick="return confirm('Hapus produk ini?')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } } else { ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Keranjang masih kosong.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="index.php" class="text-dark fw-bold text-decoration-none"><i class="fa fa-arrow-left me-2"></i> Lanjut Belanja</a>
                    <button type="submit" name="submit" class="btn btn-dark px-5 py-3 rounded-4 fw-bold">Update Jumlah</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h4 class="fw-800 mb-4">Ringkasan</h4>
                <div class="d-flex justify-content-between mb-3 opacity-75">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 opacity-75">
                    <span>Pajak (0%)</span>
                    <span>Rp 0</span>
                </div>
                <hr style="border-color: rgba(255,255,255,0.1)">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="h5 mb-0">Total</span>
                    <span class="h2 fw-800 mb-0">Rs. <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                </div>
                
                <a href="payment-method.php" class="btn-to-payment">PROSES CHECKOUT</a>
                
                <div class="mt-4 pt-2">
                    <div class="d-flex align-items-center gap-2 small opacity-50">
                        <i class="fa fa-shield-halved"></i>
                        <span>Pembayaran Aman & Terenkripsi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php ?>