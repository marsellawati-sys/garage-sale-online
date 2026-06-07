
<?php
session_start();
include('include/config.php');

// Proteksi halaman admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Jakarta');

    // --- LOGIKA TAMBAH KATEGORI (Sama dengan bawaan asli, dijamin aman) ---
    if(isset($_POST['submit'])) {
        $category = mysqli_real_escape_string($con, $_POST['category']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        
        $sql = mysqli_query($con, "INSERT INTO category(categoryName,categoryDescription) VALUES('$category','$description')");
        if($sql) {
            $_SESSION['msg'] = "Kategori Baru Berhasil Dibuat!";
        } else {
            $_SESSION['error'] = "Gagal membuat kategori. Coba periksa kembali data.";
        }
        header('location:category.php');
        exit();
    }

    // --- LOGIKA HAPUS KATEGORI (Sama dengan bawaan asli, dijamin aman) ---
    if(isset($_GET['del'])) {
        $cat_id = intval($_GET['id']);
        $query_del = mysqli_query($con, "DELETE FROM category WHERE id = '$cat_id'");
        if($query_del) {
            $_SESSION['delmsg'] = "Kategori berhasil dihapus dari sistem.";
        } else {
            $_SESSION['error'] = "Gagal menghapus. Data mungkin terikat dengan produk lain.";
        }
        header('location:category.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Koleksi | Garage Sale Studio</title>
    
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

        /* --- LAYOUT GRIID 2 KOLOM --- */
        .category-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-top: 15px;
        }
        @media (max-width: 900px) {
            .category-container { grid-template-columns: 1fr; }
        }

        .panel-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(141, 119, 95, 0.08);
            box-sizing: border-box;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 800;
            color: #1e1e1e;
            margin-bottom: 20px;
            letter-spacing: -0.3px;
        }

        /* Elements Styling */
        .field-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }
        .field-block label { font-weight: 700; font-size: 13px; color: #1e1e1e; margin: 0;}
        .field-block input, .field-block textarea {
            width: 100% !important;
            height: 46px;
            background: #fdfdfd;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 12px !important;
            padding: 10px 16px !important;
            font-size: 14px !important;
            box-sizing: border-box;
            transition: 0.2s;
        }
        .field-block textarea { height: 90px; resize: none; }
        .field-block input:focus, .field-block textarea:focus { border-color: #8d775f !important; background: #fff;}

        .btn-submit-modern {
            width: 100%; background: #1e1e1e; color: #ffffff !important;
            border: none; border-radius: 12px; padding: 12px;
            font-weight: 700; text-transform: uppercase; font-size: 12px;
            cursor: pointer; transition: 0.2s;
        }
        .btn-submit-modern:hover { background: #8d775f; }

        /* --- MODERN DATATABLES --- */
        .table-modern { width: 100% !important; border-collapse: collapse; }
        .table-modern th {
            background: #fbfafa !important; color: #8d775f !important;
            text-transform: uppercase; font-size: 11px; font-weight: 700;
            padding: 14px 10px !important; border-bottom: 2px solid #f2f0eb !important;
        }
        .table-modern td {
            padding: 14px 10px !important; font-size: 13px;
            border-bottom: 1px solid #f5f4f0 !important;
        }

        .btn-action-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px; color: #fff !important; transition: 0.2s;
        }
        .btn-edit { background: #1e1e1e; margin-right: 4px; }
        .btn-edit:hover { background: #8d775f; }
        .btn-delete { background: #de3b3b; }
        .btn-delete:hover { background: #b82828; }

        .dataTables_filter input {
            height: 36px !important; border: 1.5px solid #e6e2d6 !important; border-radius: 10px !important;
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
                            <li class="active"><a href="category.php"><i class="menu-icon icon-tasks"></i>Kategori</a></li>
                            <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Kategori</a></li>
                        </ul>
                    </div>
                </div>

                <div class="span9">
                    <div class="content">

                        <div style="margin-bottom: 20px;">
                            <h2 style="font-weight: 800; color: #1e1e1e; margin: 0;">Klasifikasi Kategori Toko</h2>
                            <p style="color: #888; font-size: 13px;">Kelola kelompok utama display koleksi baju thrifting kamu.</p>
                        </div>

                        <?php if(isset($_SESSION['msg'])) { ?>
                            <div class="alert alert-success" style="border-radius:10px; font-weight:600;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
                        <?php } ?>
                        <?php if(isset($_SESSION['delmsg'])) { ?>
                            <div class="alert alert-error" style="border-radius:10px; font-weight:600; background:#fdf2f2; color:#de3b3b;"><?php echo $_SESSION['delmsg']; unset($_SESSION['delmsg']); ?></div>
                        <?php } ?>

                        <div class="category-container">
                            
                            <div class="panel-box">
                                <div class="panel-title"><i class="icon-plus-sign" style="color:#8d775f; margin-right:6px;"></i> Tambah Baru</div>
                                <form method="post">
                                    <div class="field-block">
                                        <label>Nama Kategori</label>
                                        <input type="text" name="category" placeholder="Misal: Vintage Outerwear" required autocomplete="off">
                                    </div>
                                    <div class="field-block">
                                        <label>Deskripsi Singkat</label>
                                        <textarea name="description" placeholder="Penjelasan isi kategori baju ini..." required></textarea>
                                    </div>
                                    <button type="submit" name="submit" class="btn-submit-modern">Simpan Kategori</button>
                                </form>
                            </div>

                            <div class="panel-box">
                                <div class="panel-title"><i class="icon-list" style="color:#8d775f; margin-right:6px;"></i> Daftar Klasifikasi Terdaftar</div>
                                <table class="table table-striped table-modern datatable-1">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Nama Klasifikasi</th>
                                            <th>Penjelasan Ringkas</th>
                                            <th style="width: 80px; text-align: center;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $query_cat = mysqli_query($con, "SELECT * FROM category ORDER BY id DESC");
                                        $cnt = 1;
                                        while($row = mysqli_fetch_array($query_cat)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><b><?php echo htmlentities($row['categoryName']); ?></b></td>
                                            <td style="color:#666; font-size:12px;"><?php echo htmlentities($row['categoryDescription']); ?></td>
                                            <td style="text-align: center;">
                                                <a href="edit-category.php?id=<?php echo $row['id'];?>" class="btn-action-icon btn-edit" title="Ubah Nama">
                                                    <i class="icon-edit"></i>
                                                </a>
                                                <a href="category.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Menghapus kategori dapat berdampak pada produk terkait. Lanjutkan?')" class="btn-action-icon btn-delete" title="Hapus Kategori">
                                                    <i class="icon-remove"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div> </div>
                </div>

            </div>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-1').dataTable({
                "bLengthChange": false, // Menyembunyikan menu drop filter jumlah entri data agar hemat ruang kolom
                "iDisplayLength": 5,    // Batasi 5 baris data per halaman agar pas di grid layar
                "oLanguage": { "sSearch": "Cari:" }
            });
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
        });
    </script>
</body>
</html>
<?php } ?>
