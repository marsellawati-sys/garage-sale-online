<?php
session_start();
include('include/config.php');

// Proteksi halaman admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Jakarta');

    // --- LOGIKA HAPUS PRODUK (Sama dengan bawaan asli, dijamin aman) ---
    if(isset($_GET['del'])) {
        $product_id = intval($_GET['id']);
        $query_del = mysqli_query($con, "DELETE FROM products WHERE id = '$product_id'");
        if($query_del) {
            $_SESSION['delmsg'] = "Koleksi pakaian berhasil dihapus dari sistem gudang.";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk. Silakan coba kembali.";
        }
        header('location:manage-products.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang Stok Curated | Garage Sale Studio</title>
    
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

        /* Merombak Struktur Desain DataTables */
        .dataTables_wrapper {
            padding-top: 10px;
        }
        .dataTables_filter input {
            height: 38px !important;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 10px !important;
            padding: 5px 12px !important;
            margin-left: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            padding: 14px 12px !important;
            font-size: 13px;
            vertical-align: middle !important;
            border-bottom: 1px solid #f5f4f0 !important;
            color: #222;
        }

        /* --- THUMBNAIL FOTO BAJU --- */
        .prod-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e6e2d6;
        }

        /* --- BADGE AVAILABILITY --- */
        .badge-stock {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }
        .stock-ready { background: #e8f9ee; color: #27ae60; }
        .stock-sold { background: #fdf2f2; color: #de3b3b; }

        /* --- ACTION BUTTONS --- */
        .btn-action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            color: #fff !important;
            margin-right: 5px;
            transition: 0.2s;
        }
        .btn-edit-item { background: #1e1e1e; }
        .btn-edit-item:hover { background: #8d775f; }
        .btn-delete-item { background: #de3b3b; }
        .btn-delete-item:hover { background: #b82828; }

        .btn-quick {
            background: #1e1e1e; color: #fff !important;
            border-radius: 12px; padding: 10px 18px; font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px; border: none; transition: 0.2s;
        }
        .btn-quick:hover { background: #8d775f; transform: translateY(-2px); }

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
                            <li class="active"><a href="manage-products.php"><i class="menu-icon icon-table"></i>Gudang Produk</a></li>
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
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                                <div>
                                    <h2 style="font-weight: 800; color: #1e1e1e; margin: 0;">Gudang Pakaian Curated</h2>
                                    <span style="color: #888; font-size: 13px;">Kelola inventaris koleksi pakaian, edit harga, dan pantau status barang thrift.</span>
                                </div>
                                <a href="insert-product.php" class="btn-quick"><i class="icon-plus"></i> Drop Produk</a>
                            </div>

                            <?php if(isset($_SESSION['delmsg'])) { ?>
                                <div class="alert alert-error" style="background: #fdf2f2; color: #de3b3b; border-radius: 10px; font-weight:600;">
                                    <i class="icon-trash" style="margin-right: 8px;"></i> <?php echo $_SESSION['delmsg']; unset($_SESSION['delmsg']); ?>
                                </div>
                            <?php } ?>

                            <table class="table table-striped table-bordered table-modern datatable-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Visual</th>
                                        <th>Nama Koleksi</th>
                                        <th>Kategori</th>
                                        <th>Brand</th>
                                        <th>Harga Bersih</th>
                                        <th>Status Stok</th>
                                        <th style="width: 90px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Query relasi menggabungkan produk, kategori, dan subkategori
                                    $query_inventory = mysqli_query($con, "SELECT products.*, category.categoryName, subcategory.subcategory FROM products JOIN category ON category.id=products.category JOIN subcategory ON subcategory.id=products.subCategory ORDER BY products.id DESC");
                                    $cnt = 1;
                                    while($row = mysqli_fetch_array($query_inventory)) {
                                        $availability = $row['productAvailability'];
                                        $badge_class = (strtolower($availability) == 'in stock') ? 'stock-ready' : 'stock-sold';
                                        $status_display = (strtolower($availability) == 'in stock') ? 'Ready Stock' : 'Sold Out';
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td>
                                            <img src="productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" class="prod-thumb" alt="Product Image">
                                        </td>
                                        <td><b><?php echo htmlentities($row['productName']); ?></b></td>
                                        <td>
                                            <span style="font-size: 12px; color: #8d775f; font-weight:600;"><?php echo htmlentities($row['categoryName']); ?></span><br>
                                            <small style="color: #999;"><?php echo htmlentities($row['subcategory']); ?></small>
                                        </td>
                                        <td><?php echo htmlentities($row['productCompany']); ?></td>
                                        <td><b>Rp <?php echo number_format($row['productPrice'], 0, ',', '.'); ?></b></td>
                                        <td>
                                            <span class="badge-stock <?php echo $badge_class; ?>"><?php echo $status_display; ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="edit-products.php?id=<?php echo $row['id'];?>" class="btn-action-icon btn-edit-item" title="Edit Detail Koleksi">
                                                <i class="icon-edit"></i>
                                            </a>
                                            <a href="manage-products.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Apakah kamu yakin ingin melenyapkan baju curated ini dari database gudang?')" class="btn-action-icon btn-delete-item" title="Hapus Produk">
                                                <i class="icon-trash"></i>
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
                    "sSearch": "Cari Baju:",
                    "sLengthMenu": "Tampilkan _MENU_ item",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ koleksi",
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
