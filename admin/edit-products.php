
<?php
session_start();
include('include/config.php');

// Sembunyikan notifikasi warning agar tampilan bersih
error_reporting(E_ALL ^ E_NOTICE);

if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    $pid = intval($_GET['id']); // Mengambil ID produk dari URL

    // PROSES UPDATE DATA (Hanya jalan saat tombol submit diklik)
    if(isset($_POST['submit'])) {
        $category = $_POST['category'] ?? "";
        $subcat = $_POST['subcategory'] ?? "";
        $productname = $_POST['productName'] ?? "";
        $productcompany = $_POST['productCompany'] ?? "";
        $productprice = $_POST['productprice'] ?? "";
        $productpricebd = $_POST['productpricebd'] ?? "";
        $productdescription = $_POST['productDescription'] ?? "";
        $productscharge = $_POST['productShippingcharge'] ?? "";
        $productavailability = $_POST['productAvailability'] ?? "";

        $query = mysqli_query($con, "UPDATE products SET category='$category', subCategory='$subcat', productName='$productname', productCompany='$productcompany', productPrice='$productprice', productPriceBeforeDiscount='$productpricebd', productDescription='$productdescription', shippingCharge='$productscharge', productAvailability='$productavailability' WHERE id='$pid'");
        
        if($query) {
            $msg = "Produk Berhasil Diperbarui!";
        } else {
            $error = "Gagal Update: " . mysqli_error($con);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Product</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
    <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
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
<?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php');?>
                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Edit Product</h3>
                            </div>
                            <div class="module-body">

                                <?php if(isset($msg)) { ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Berhasil!</strong> <?php echo htmlentities($msg); ?>
                                    </div>
                                <?php } ?>

                                <?php if(isset($error)) { ?>
                                    <div class="alert alert-danger">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Error!</strong> <?php echo htmlentities($error); ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal row-fluid" name="editproduct" method="post" enctype="multipart/form-data">
<?php 
// Menggunakan LEFT JOIN agar jika kategori kosong, data produk tetap muncul
$query = mysqli_query($con, "SELECT products.*, category.categoryName as catname, category.id as cid, subcategory.subcategory as subcatname, subcategory.id as subcatid FROM products LEFT JOIN category ON category.id=products.category LEFT JOIN subcategory ON subcategory.id=products.subCategory WHERE products.id='$pid'");

if(mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_array($query)) {
?>
                                    <div class="control-group">
                                        <label class="control-label">Category</label>
                                        <div class="controls">
                                            <select name="category" class="span8 tip" onChange="getSubcat(this.value);" required>
                                                <option value="<?php echo htmlentities($row['cid']);?>"><?php echo htmlentities($row['catname']);?></option>
                                                <?php 
                                                $ret = mysqli_query($con,"select * from category");
                                                while($rw = mysqli_fetch_array($ret)) {
                                                    if($row['catname'] == $rw['categoryName']) continue;
                                                    echo "<option value='".htmlentities($rw['id'])."'>".htmlentities($rw['categoryName'])."</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Sub Category</label>
                                        <div class="controls">
                                            <select name="subcategory" id="subcategory" class="span8 tip" required>
                                                <option value="<?php echo htmlentities($row['subcatid']);?>"><?php echo htmlentities($row['subcatname']);?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Product Name</label>
                                        <div class="controls">
                                            <input type="text" name="productName" value="<?php echo htmlentities($row['productName']);?>" class="span8 tip" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Product Company</label>
                                        <div class="controls">
                                            <input type="text" name="productCompany" value="<?php echo htmlentities($row['productCompany']);?>" class="span8 tip" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Price Before Discount</label>
                                        <div class="controls">
                                            <input type="text" name="productpricebd" value="<?php echo htmlentities($row['productPriceBeforeDiscount']);?>" class="span8 tip" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Selling Price (Final)</label>
                                        <div class="controls">
                                            <input type="text" name="productprice" value="<?php echo htmlentities($row['productPrice']);?>" class="span8 tip" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Description</label>
                                        <div class="controls">
                                            <textarea name="productDescription" rows="5" class="span8 tip"><?php echo htmlentities($row['productDescription']);?></textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Shipping Charge</label>
                                        <div class="controls">
                                            <input type="text" name="productShippingcharge" value="<?php echo htmlentities($row['shippingCharge']);?>" class="span8 tip" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Availability</label>
                                        <div class="controls">
                                            <select name="productAvailability" class="span8 tip" required>
                                                <option value="<?php echo htmlentities($row['productAvailability']);?>"><?php echo htmlentities($row['productAvailability']);?></option>
                                                <option value="In Stock">In Stock</option>
                                                <option value="Out of Stock">Out of Stock</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Product Image</label>
                                        <div class="controls">
                                            <img src="productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" width="150">
                                            <br><a href="update-image1.php?id=<?php echo $row['id'];?>" class="btn btn-mini">Change Image</a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
                                        </div>
                                    </div>
<?php 
    } 
} else {
    echo "<div class='alert alert-error'>ID Produk tidak ditemukan. Silakan kembali ke Manage Products.</div>";
}
?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('include/footer.php');?>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
<?php } ?>