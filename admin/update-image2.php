
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else{
    $pid=intval($_GET['id']); // Product ID

    if(isset($_POST['submit']))
    {
        $productname=$_POST['productName'];
        $productimage2=$_FILES["productimage2"]["name"];

        // Upload ke folder spesifik produk
        move_uploaded_file($_FILES["productimage2"]["tmp_name"],"productimages/$pid/".$_FILES["productimage2"]["name"]);
        
        // Update database untuk kolom Gambar 2
        $sql=mysqli_query($con,"update products set productImage2='$productimage2' where id='$pid'");
        $_SESSION['msg']="Gambar Ke-2 Berhasil Diperbarui !!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Update Product Image 2</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
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
                                <h3>Update Gambar Produk 2</h3>
                            </div>
                            <div class="module-body">

                                <?php if(isset($_POST['submit'])) { ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Berhasil!</strong> <?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
                                    </div>
                                <?php } ?>

                                <br />

                                <form class="form-horizontal row-fluid" name="updateimg2" method="post" enctype="multipart/form-data">
<?php 
$query=mysqli_query($con,"select productName,productImage2 from products where id='$pid'");
while($row=mysqli_fetch_array($query))
{
?>
                                    <div class="control-group">
                                        <label class="control-label">Nama Produk</label>
                                        <div class="controls">
                                            <input type="text" name="productName" readonly value="<?php echo htmlentities($row['productName']);?>" class="span8 tip">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Gambar Saat Ini</label>
                                        <div class="controls">
                                            <img src="productimages/<?php echo htmlentities($pid);?>/<?php echo htmlentities($row['productImage2']);?>" width="200" height="100"> 
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Pilih Gambar Baru (Image 2)</label>
                                        <div class="controls">
                                            <input type="file" name="productimage2" class="span8 tip" required>
                                        </div>
                                    </div>
<?php } ?>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div></div></div>
        </div></div><?php include('include/footer.php');?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
<?php } ?>