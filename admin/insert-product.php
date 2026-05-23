
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{
    
if(isset($_POST['submit']))
{
    $category=$_POST['category'];
    $subcat=$_POST['subcategory'];
    $productname=$_POST['productName'];
    $productcompany=$_POST['productCompany'];
    $productprice=$_POST['productprice'];
    $productpricebd=$_POST['productpricebd'];
    $productdescription=$_POST['productDescription'];
    $productscharge=$_POST['productShippingcharge'];
    $productavailability=$_POST['productAvailability'];
    $productimage1=$_FILES["productimage1"]["name"];
    $productimage2=$_FILES["productimage2"]["name"];
    $productimage3=$_FILES["productimage3"]["name"];

    // Logika Mendapatkan ID Produk Terakhir untuk penamaan folder
    $query=mysqli_query($con,"select max(id) as pid from products");
    $result=mysqli_fetch_array($query);
    $productid=$result['pid']+1;
    $dir="productimages/$productid";

    // Jika folder belum ada, buat foldernya
    if(!is_dir($dir)){
        mkdir("productimages/".$productid, 0777, true);
    }

    // Proses Upload ke folder spesifik
    move_uploaded_file($_FILES["productimage1"]["tmp_name"],"productimages/$productid/".$_FILES["productimage1"]["name"]);
    move_uploaded_file($_FILES["productimage2"]["tmp_name"],"productimages/$productid/".$_FILES["productimage2"]["name"]);
    move_uploaded_file($_FILES["productimage3"]["tmp_name"],"productimages/$productid/".$_FILES["productimage3"]["name"]);

    $sql=mysqli_query($con,"insert into products(category,subCategory,productName,productCompany,productPrice,productDescription,shippingCharge,productAvailability,productImage1,productImage2,productImage3,productPriceBeforeDiscount) values('$category','$subcat','$productname','$productcompany','$productprice','$productdescription','$productscharge','$productavailability','$productimage1','$productimage2','$productimage3','$productpricebd')");
    
    $_SESSION['msg']="Product Inserted Successfully !!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Insert Product</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
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
                                <h3>Insert Product</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_POST['submit'])) { ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Berhasil!</strong> <?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<div class="control-group">
<label class="control-label">Category</label>
<div class="controls">
<select name="category" class="span8 tip" onChange="getSubcat(this.value);" required>
<option value="">Select Category</option> 
<?php $query=mysqli_query($con,"select * from category");
while($row=mysqli_fetch_array($query)) { ?>
<option value="<?php echo $row['id'];?>"><?php echo $row['categoryName'];?></option>
<?php } ?>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label">Sub Category</label>
<div class="controls">
<select name="subcategory" id="subcategory" class="span8 tip" required></select>
</div>
</div>

<div class="control-group">
<label class="control-label">Product Name</label>
<div class="controls">
<input type="text" name="productName" placeholder="Enter Product Name" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Product Company</label>
<div class="controls">
<input type="text" name="productCompany" placeholder="Enter Company Name" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Price Before Discount</label>
<div class="controls">
<input type="text" name="productpricebd" placeholder="Enter Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Selling Price</label>
<div class="controls">
<input type="text" name="productprice" placeholder="Enter Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Description</label>
<div class="controls">
<textarea name="productDescription" rows="6" class="span8 tip"></textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label">Shipping Charge</label>
<div class="controls">
<input type="text" name="productShippingcharge" placeholder="Enter Charge" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Availability</label>
<div class="controls">
<select name="productAvailability" class="span8 tip" required>
<option value="">Select</option>
<option value="In Stock">In Stock</option>
<option value="Out of Stock">Out of Stock</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label">Image 1</label>
<div class="controls">
<input type="file" name="productimage1" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Image 2</label>
<div class="controls">
<input type="file" name="productimage2" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label">Image 3</label>
<div class="controls">
<input type="file" name="productimage3" class="span8 tip">
</div>
</div>

    <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="submit" class="btn btn-primary">Insert Product</button>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('include/footer.php');?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
<?php } ?>