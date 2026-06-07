<?php
session_start();
include('include/config.php');

// Proteksi halaman admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Sukses | Garage Sale Studio</title>
    
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f7f5;
        }
        .navbar-inner { background: #1e1e1e !important; padding: 10px 0; }
        .brand { color: #fff !important; font-weight: 800; }

        /* --- CONTAINER TABEL MODERN --- */
        .table-panel-box {
            background: #ffffff;
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(141, 119, 95, 0.08);
            margin-bottom: 40px;
        }

        /* Struktur Desain DataTables */
        .dataTables_filter input {
            height: 38px !important;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 10px !important;
            padding: 5px 12px !important;
            margin-left: 8px;
        }
        .dataTables_length select {
            height: 38px !important;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 10px !important;
            width: 70px;
        }

        .table-modern {
            width: 100% !important;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table-modern th {
            background: #fbfafa !important;
            color: #8d775f !important;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            padding: 16px 12px !important;
            border-bottom: 2px solid #f2f0eb !important;
        }
        .table-modern td {
            padding: 16px 12px !important;
            font-size: 13px;
            vertical-align: middle !important;
            border-bottom: 1px solid #f5f4f0 !important;
            color: #222;
        }

        /* --- BADGES STYLING --- */
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
            background: #e8f9ee;
            color: #27ae60;
            border: 1px solid #c2f0d1;
        }
        
        .badge-method {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #f0ecf4;
            color: #6c5ce7;
            border: 1px solid #e1daf2;
        }

        /* --- ACTION BUTTON --- */
        .btn-view-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #1e1e1e;
            color: #fff !important;
            transition: 0.2s;
            border: none;
        }
        .btn-view-detail:hover {
            background: #8d775f;
            transform: translateY(-2px);
        }

        /* Custom Pagination DataTables */
        .datatable-pagination a {
            background: #fff !important;
            border: 1px solid #e6e2d6 !important;
            color: #1e1e1e !important;
            padding: 6px 12px !important;
            font-size: 12px;
            font-weight: 600;
        }
        .datatable-pagination a:hover {
            background: #8d775f !important;
            color: white !important;
        }
    </style>
</head>
<body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="dashboard.php">⚙️ GS. STUDIO HUB CONTROL</a>
            </div>
        </div>
    </div>

    <div class="wrapper" style="padding-top: 40px;">
        <div class="container">
            <div class="row">
                
                <div class="span3">
                    <div class="sidebar">
                        <ul class="widget widget-menu unstyled">
                            <li><a href="dashboard.php"><i class="menu-icon icon-dashboard"></i>Dashboard Utama</a></li>
                            <li><a href="manage-products.php"><i class="menu-icon icon-table"></i>Gudang Produk</a></li>
                            <li><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Drop Produk Baru</a></li>
                        </ul>
                        <ul class="widget widget-menu unstyled">
                            <li><a href="category.php"><i class="menu-icon icon-tasks"></i>Kategori</a></li>
                            <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Kategori</a></li>
                        </ul>
                    </div>
                </div>

                <div class="span9">
                    <div class="content">

                        <div class="table-panel-box">
                            <div style="margin-bottom: 25px;">
                                <h2 style="font-weight: 800; color: #1e1e1e; margin: 0;">Arsip Penjualan Sukses</h2>
                                <span style="color: #888; font-size: 13px;">Daftar invoice pesanan yang telah dikirim (*Delivered*) dan selesai diproses oleh sistem.</span>
                            </div>

                            <table class="table table-striped table-bordered table-modern datatable-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Detail Pembeli</th>
                                        <th>Koleksi Baju</th>
                                        <th style="width: 50px; text-align:center;">Qty</th>
                                        <th>Total Dana</th>
                                        <th>Waktu Lunas</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th style="text-align: center; width: 40px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Query relasi gabungan pesanan khusus yang berstatus 'Delivered'
                                    $query_orders = mysqli_query($con, "SELECT orders.id, users.name as username, users.email as useremail, users.contactno as usercontact, users.shippingAddress, products.productName, orders.quantity, products.productPrice, products.shippingCharge, orders.orderDate, orders.paymentMethod, orders.orderStatus FROM orders JOIN users ON orders.userId=users.id JOIN products ON orders.productId=products.id WHERE orders.orderStatus='Delivered' ORDER BY orders.id DESC");
                                    $cnt = 1;
                                    while($row = mysqli_fetch_array($query_orders)) {
                                        // Menghitung total nilai transaksi item + ongkir
                                        $total_bayar = ($row['quantity'] * $row['productPrice']) + $row['shippingCharge'];
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td>
                                            <b><?php echo htmlentities($row['username']); ?></b><br>
                                            <small style="color: #777;"><?php echo htmlentities($row['usercontact']); ?></small>
                                        </td>
                                        <td><span style="font-weight:600; color:#8d775f;"><?php echo htmlentities($row['productName']); ?></span></td>
                                        <td style="text-align: center;"><b><?php echo htmlentities($row['quantity']); ?></b></td>
                                        <td><b>Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></b></td>
                                        <td style="font-size:12px; color:#666;"><?php echo date('d M Y, H:i', strtotime($row['orderDate'])); ?> WIB</td>
                                        <td>
                                            <span class="badge-method"><?php echo htmlentities($row['paymentMethod']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge-status">Selesai</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="updateorder.php?oid=<?php echo htmlentities($row['id']);?>" target="_blank" class="btn-view-detail" title="Lihat Berkas Pengiriman">
                                                <i class="icon-external-link"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer" style="background: #1e1e1e; color: #888; padding: 20px 0; border: none; margin-top: 40px;">
        <div class="container" style="text-align: center;">
            <b class="copyright" style="color: #fff;">&copy; Garage Sale Studio Inventaris Dashboard</b>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-1').dataTable({
                "oLanguage": {
                    "sSearch": "Cari Transaksi:",
                    "sLengthMenu": "Tampilkan _MENU_",
                    "sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ riwayat lunas",
                    "oPaginate": {
                        "sPrevious": "Kembali",
                        "sNext": "Lanjut"
                    }
                }
            });
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
        });
    </script>
</body>
</html>
<?php } ?>
