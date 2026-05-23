
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else{
    $pid=intval($_GET['id']); // Mengambil ID produk dari URL
    
    if(isset($_POST['submit']))
    {
        $productname=$_POST['productName'];
        $productimage1=$_FILES["productimage1"]["name"];
        
        // Mengunggah file ke folder spesifik produk
        move_uploaded_file($_FILES["productimage1"]["tmp_name"],"productimages/$pid/".$_FILES["productimage1"]["name"]);
        
        // Update nama file di database
        $sql=mysqli_query($con,"update products set productImage1='$productimage1' where id='$pid'");
        $_SESSION['msg']="Gambar Utama Berhasil Diperbarui !!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Update Product Image</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
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
                                <h3>Update Gambar Produk 1</h3>
                            </div>
                            <div class="module-body">

                                <?php if(isset($_POST['submit'])) { ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Berhasil!</strong> <?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<?php 
$query=mysqli_query($con,"select productName,productImage1 from products where id='$pid'");
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
                                            <img src="productimages/<?php echo htmlentities($pid);?>/<?php echo htmlentities($row['productImage1']);?>" width="200"> 
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Pilih Gambar Baru</label>
                                        <div class="controls">
                                            <input type="file" name="productimage1" class="span8 tip" required>
                                        </div>
                                    </div>
<?php } ?>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn btn-primary">Update Gambar</button>
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