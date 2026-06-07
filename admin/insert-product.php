<?php
session_start();
include('include/config.php');

// Proteksi halaman admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    
    // --- LOGIKA UTAMA TAMBAH PRODUK (Sama dengan bawaanmu, dijamin aman & sinkron) ---
    if(isset($_POST['submit'])) {
        $category = mysqli_real_escape_string($con, $_POST['category']);
        $subcat = mysqli_real_escape_string($con, $_POST['subcategory']);
        $productname = mysqli_real_escape_string($con, $_POST['productName']);
        $productcompany = mysqli_real_escape_string($con, $_POST['productCompany']);
        $productprice = mysqli_real_escape_string($con, $_POST['productprice']);
        $productpricebd = mysqli_real_escape_string($con, $_POST['productpricebd']);
        $productdescription = mysqli_real_escape_string($con, $_POST['productDescription']);
        $productscharge = mysqli_real_escape_string($con, $_POST['productShippingcharge']);
        $productavailability = mysqli_real_escape_string($con, $_POST['productAvailability']);
        
        $productimage1 = $_FILES["productimage1"]["name"];
        $productimage2 = $_FILES["productimage2"]["name"];
        $productimage3 = $_FILES["productimage3"]["name"];

        // Menentukan ID Auto-Increment berikutnya untuk penamaan folder asset gambar
        $query_max = mysqli_query($con, "SELECT MAX(id) as pid FROM products");
        $result_max = mysqli_fetch_array($query_max);
        $productid = $result_max['pid'] + 1;
        $dir = "productimages/$productid";

        if(!is_dir($dir)){
            mkdir("productimages/".$productid, 0777, true);
        }

        // Proses pindah file dari cache server ke folder penyimpanan
        move_uploaded_file($_FILES["productimage1"]["tmp_name"], "productimages/$productid/".$_FILES["productimage1"]["name"]);
        move_uploaded_file($_FILES["productimage2"]["tmp_name"], "productimages/$productid/".$_FILES["productimage2"]["name"]);
        move_uploaded_file($_FILES["productimage3"]["tmp_name"], "productimages/$productid/".$_FILES["productimage3"]["name"]);
        
        // Query Masuk ke Database
        $sql = mysqli_query($con, "INSERT INTO products(category,subCategory,productName,productCompany,productPrice,productPriceBeforeDiscount,productDescription,shippingCharge,productAvailability,productImage1,productImage2,productImage3) VALUES('$category','$subcat','$productname','$productcompany','$productprice','$productpricebd','$productdescription','$productscharge','$productavailability','$productimage1','$productimage2','$productimage3')");
        
        if($sql) {
            $_SESSION['msg'] = "Produk Curated Berhasil Dijatuhkan ke Toko!";
        } else {
            $_SESSION['error'] = "Terjadi kegagalan sistem. Coba periksa kembali data.";
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop Collection Baru | Garage Sale Studio</title>
    
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

        /* --- CONTAINER FORM PREMIUM --- */
        .form-panel-box {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(141, 119, 95, 0.08);
            margin-bottom: 40px;
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 800;
            color: #8d775f;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            border-bottom: 2px solid #f2f0eb;
            padding-bottom: 8px;
        }

        /* Overriding Kerangka Lama Menjadi Form Elemen Modern */
        .modern-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 10px;
        }
        @media(max-width: 768px){ .modern-row-grid { grid-template-columns: 1fr; } }

        .field-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }
        .field-block label {
            font-weight: 700;
            font-size: 13px;
            color: #1e1e1e;
            margin: 0;
        }
        .field-block input, .field-block select, .field-block textarea {
            width: 100% !important;
            height: 48px;
            background: #fdfdfd;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 12px !important;
            padding: 10px 16px !important;
            font-size: 14px !important;
            box-sizing: border-box;
            box-shadow: none !important;
            transition: 0.2s;
        }
        .field-block textarea { height: 120px; resize: vertical; }
        .field-block input:focus, .field-block select:focus, .field-block textarea:focus {
            border-color: #8d775f !important;
            background: #fff;
        }

        /* --- PREVIEW IMAGE STYLING --- */
        .image-upload-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        .img-preview-box {
            background: #fbfbfa;
            border: 2px dashed #e6e2d6;
            border-radius: 16px;
            padding: 15px;
            text-align: center;
            position: relative;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .img-preview-box img {
            max-height: 100px;
            border-radius: 8px;
            display: none;
            margin-top: 10px;
            object-fit: cover;
        }

        .btn-submit-modern {
            background: #1e1e1e;
            color: #ffffff !important;
            border: none;
            border-radius: 14px;
            padding: 15px 35px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .btn-submit-modern:hover {
            background: #8d775f;
            transform: translateY(-2px);
        }

        .alert-toast {
            padding: 15px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 25px;
        }
    </style>

    <script>
    function getSubcat(val) {
        $.ajax({
            type: "POST",
            url: "get_subcat.php",
            data:'cat_id='+val,
            success: function(data){
                $("#subcategory").html(data);
            }
        });
    }
    </script>
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
                            <li class="active"><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Drop Produk Baru</a></li>
                        </ul>
                        <ul class="widget widget-menu unstyled">
                            <li><a href="category.php"><i class="menu-icon icon-tasks"></i>Kategori</a></li>
                            <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Kategori</a></li>
                        </ul>
                    </div>
                </div>

                <div class="span9">
                    <div class="content">

                        <div class="form-panel-box">
                            <h2 style="font-weight: 800; color: #1e1e1e; margin: 0 0 5px 0;">Drop New Curated Find</h2>
                            <p style="color: #888; font-size: 13px; margin-bottom: 30px;">Tambahkan koleksi pakaian thrift eksklusif terbaru ke dalam katalog etalase toko.</p>

                            <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success alert-toast">
                                    <i class="icon-ok-sign me-2"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                                </div>
                            <?php } ?>
                            <?php if(isset($_SESSION['error'])) { ?>
                                <div class="alert alert-error alert-toast">
                                    <i class="icon-remove-sign me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                </div>
                            <?php } ?>

                            <form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">
                                
                                <div class="form-section-title">1. Penempatan Klasifikasi</div>
                                <div class="modern-row-grid">
                                    <div class="field-block">
                                        <label>Pilih Kategori Induk</label>
                                        <select name="category" onChange="getSubcat(this.value);" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php 
                                            $query_cat = mysqli_query($con, "SELECT * FROM category");
                                            while($row_cat = mysqli_fetch_array($query_cat)) { ?>
                                                <option value="<?php echo $row_cat['id'];?>"><?php echo $row_cat['categoryName'];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="field-block">
                                        <label>Pilih Sub-Kategori</label>
                                        <select name="subcategory" id="subcategory" required>
                                            <option value="">Pilih Kategori Terlebih Dahulu</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-section-title" style="margin-top: 20px;">2. Informasi Detail Pakaian</div>
                                <div class="field-block">
                                    <label>Nama Produk / Koleksi Pakaian</label>
                                    <input type="text" name="productName" placeholder="Contoh: Vintage Corduroy Outer Harajuku" required>
                                </div>
                                
                                <div class="modern-row-grid">
                                    <div class="field-block">
                                        <label>Merek / Brand / Vendor Perusahaan</label>
                                        <input type="text" name="productCompany" placeholder="Contoh: Nike Vintage, Uniqlo preloved" required>
                                    </div>
                                    <div class="field-block">
                                        <label>Status Ketersediaan Stok</label>
                                        <select name="productAvailability" required>
                                            <option value="In Stock">Ready Stock (Tersedia)</option>
                                            <option value="Out of Stock">Sold Out (Habis Terjual)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modern-row-grid">
                                    <div class="field-block">
                                        <label>Harga Jual Toko (Rp)</label>
                                        <input type="number" name="productprice" placeholder="Masukkan nominal angka bersih" required>
                                    </div>
                                    <div class="field-block">
                                        <label>Harga Coret Sebelum Diskon (Rp) <span style="font-weight: 400; color: #888;">- Opsional</span></label>
                                        <input type="number" name="productpricebd" placeholder="Kosongkan jika tidak ada diskon">
                                    </div>
                                </div>

                                <div class="field-block">
                                    <label>Ongkos Kirim Flat / Biaya Shipping (Rp)</label>
                                    <input type="number" name="productShippingcharge" placeholder="Masukkan nominal ongkir" required value="0">
                                </div>

                                <div class="field-block">
                                    <label>Deskripsi & Kondisi Real Item (Minus/Ukuran)</label>
                                    <textarea name="productDescription" placeholder="Tuliskan detail ukuran PxL pakaian, bahan kain, serta minus noda/robek jika ada secara transparan..."></textarea>
                                </div>

                                <div class="form-section-title" style="margin-top: 25px;">3. Berkas Galeri Foto (Maks 3 Foto)</div>
                                <div class="image-upload-wrapper">
                                    <div class="img-preview-box">
                                        <label style="font-weight: 700; font-size: 11px;">FOTO UTAMA *</label>
                                        <input type="file" name="productimage1" id="imgInp1" accept="image/*" required style="font-size: 11px; height: auto; border: none; padding: 0;">
                                        <img id="preview1" src="#" alt="Preview 1">
                                    </div>
                                    <div class="img-preview-box">
                                        <label style="font-weight: 700; font-size: 11px;">FOTO DETAIL 2 *</label>
                                        <input type="file" name="productimage2" id="imgInp2" accept="image/*" required style="font-size: 11px; height: auto; border: none; padding: 0;">
                                        <img id="preview2" src="#" alt="Preview 2">
                                    </div>
                                    <div class="img-preview-box">
                                        <label style="font-weight: 700; font-size: 11px;">FOTO TAG 3</label>
                                        <input type="file" name="productimage3" id="imgInp3" accept="image/*" style="font-size: 11px; height: auto; border: none; padding: 0;">
                                        <img id="preview3" src="#" alt="Preview 3">
                                    </div>
                                </div>

                                <div style="margin-top: 40px; text-align: right;">
                                    <button type="submit" name="submit" class="btn-submit-modern"><i class="icon-cloud-upload" style="margin-right: 8px;"></i> Publish Drop Koleksi</button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <script>
        function setupPreview(inputId, previewId) {
            $(inputId).change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr('src', e.target.result).text("").show();
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
        setupPreview("#imgInp1", "#preview1");
        setupPreview("#imgInp2", "#preview2");
        setupPreview("#imgInp3", "#preview3");
    </script>
</body>
</html>
<?php } ?>
