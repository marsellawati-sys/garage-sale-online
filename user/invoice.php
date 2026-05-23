<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if(strlen($_SESSION['login']) == 0) {   
    header('location:login.php');
    exit();
}

if(!isset($_POST['submit'])) {
    header('location:index.php');
    exit();
}

$uid = $_SESSION['id'];
$pid = intval($_POST['product_id']);
$p_method = mysqli_real_escape_string($con, $_POST['payment_method']);
$shipping = intval($_POST['shipping_cost']);
$total = intval($_POST['grand_total']);
$quantity = 1; 

$query_order = mysqli_query($con, "INSERT INTO orders(userId, productId, quantity) VALUES('$uid', '$pid', '$quantity')");
$order_id = mysqli_insert_id($con); 

$info_q = mysqli_query($con, "SELECT products.productName, products.productPrice, users.name, users.shippingAddress, users.contactno 
                             FROM products, users 
                             WHERE products.id='$pid' AND users.id='$uid'");
$data = mysqli_fetch_array($info_q);
$discount = ($data['productPrice'] >= 100000 ? $data['productPrice'] * 0.05 : 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Premium #GS-<?php echo $order_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #111111;
            --accent: #d4a373; /* Gold/Bronze accent */
            --bg: #f4f2ee;
            --border: #e8e4d8;
        }

        body { 
            background-color: var(--bg); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding: 60px 0;
            color: var(--primary);
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 0; /* Flat minimalist look */
            box-shadow: 0 40px 100px rgba(0,0,0,0.08);
            max-width: 800px;
            margin: auto;
            position: relative;
            overflow: hidden;
        }

        /* Accent bar at top */
        .invoice-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: var(--primary);
        }

        .header-section {
            padding: 60px 50px 40px;
            border-bottom: 1px solid #f0f0f0;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -1.5px;
            text-transform: uppercase;
        }

        .invoice-label {
            font-weight: 300;
            font-size: 45px;
            letter-spacing: -2px;
            line-height: 1;
        }

        .info-grid {
            padding: 40px 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .info-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #aaa;
            margin-bottom: 15px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-table th {
            background: #fafafa;
            padding: 15px 50px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #eee;
        }

        .item-table td {
            padding: 30px 50px;
            border-bottom: 1px solid #f9f9f9;
        }

        .total-section {
            padding: 40px 50px 60px;
            background: #fafafa;
        }

        .summary-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .grand-total-box {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--primary);
        }

        .btn-premium {
            background: var(--primary);
            color: white;
            padding: 14px 30px;
            border-radius: 0;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.4s;
            text-decoration: none;
            border: none;
        }

        .btn-premium:hover {
            background: #333;
            color: white;
            letter-spacing: 3px;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .invoice-card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="invoice-card">
        <div class="header-section d-flex justify-content-between align-items-end">
            <div>
                <div class="brand-logo mb-4">Garage Sale.</div>
                <div class="invoice-label">Invoice</div>
            </div>
            <div class="text-end">
                <p class="mb-1 fw-bold">No. #GS-<?php echo $order_id; ?></p>
                <p class="text-muted small"><?php echo date('D, d M Y'); ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-title">Ship To</div>
                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($data['name']); ?></h6>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars($data['shippingAddress']); ?></p>
                <p class="text-muted small"><?php echo htmlspecialchars($data['contactno']); ?></p>
            </div>
            <div class="text-md-end">
                <div class="info-title">Payment</div>
                <h6 class="fw-bold mb-1"><?php echo $p_method; ?></h6>
                <p class="text-muted small">Status: <span class="text-dark fw-bold">PENDING</span></p>
            </div>
        </div>

        <table class="item-table">
            <thead>
                <tr>
                    <th class="text-start">Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold text-uppercase small mb-1">Essential Collection</div>
                        <div class="text-muted small"><?php echo htmlspecialchars($data['productName']); ?></div>
                    </td>
                    <td class="text-center">01</td>
                    <td class="text-end fw-bold">Rp <?php echo number_format($data['productPrice'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="summary-row">
                <span class="text-muted me-5">Subtotal</span>
                <span class="fw-bold" style="min-width: 120px; text-align: right;">Rp <?php echo number_format($data['productPrice'], 0, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
                <span class="text-muted me-5">Shipping</span>
                <span class="fw-bold" style="min-width: 120px; text-align: right;">Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
            </div>
            <div class="summary-row text-success">
                <span class="me-5">Seasonal Discount</span>
                <span class="fw-bold" style="min-width: 120px; text-align: right;">- Rp <?php echo number_format($discount, 0, ',', '.'); ?></span>
            </div>
            
            <div class="grand-total-box">
                <span class="info-title mb-0 me-4">Total Amount</span>
                <span class="fs-3 fw-800">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>

            <div class="mt-5 pt-4 text-center no-print">
                <a href="index.php" class="text-muted small text-decoration-none me-4 fw-bold">BACK TO SHOP</a>
                <button onclick="window.print()" class="btn-premium">
                    <i class="fa fa-print me-2"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
    <p class="text-center mt-5 text-muted small opacity-50 no-print">© 2026 Garage Sale Store. All Rights Reserved.</p>
</div>

</body>
</html>