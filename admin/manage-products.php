<?php

session_start();

include('include/config.php');

if(strlen($_SESSION['alogin'])==0) {  

    header('location:index.php');

} else {

    // Logika untuk menghapus produk

    if(isset($_GET['del'])) {

        mysqli_query($con,"delete from products where id = '".$_GET['id']."'");

        $_SESSION['delmsg']="Product deleted !!";

    }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin | Manage Products</title>

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

                                <h3>Manage Products</h3>

                            </div>

                            <div class="module-body table">

                                <?php if(isset($_GET['del'])) { ?>

                                    <div class="alert alert-error">

                                        <button type="button" class="close" data-dismiss="alert">×</button>

                                        <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>

                                    </div>

                                <?php } ?>



                                <br />



                                <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">

                                    <thead>

                                        <tr>

                                            <th>#</th>

                                            <th>Product Name</th>

                                            <th>Category </th>

                                            <th>Company</th>

                                            <th>Creation Date</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>



<?php

$query=mysqli_query($con,"select products.*,category.categoryName from products join category on category.id=products.category");

$cnt=1;

while($row=mysqli_fetch_array($query))

{

?>                                  

                                        <tr>

                                            <td><?php echo htmlentities($cnt);?></td>

                                            <td><?php echo htmlentities($row['productName']);?></td>

                                            <td><?php echo htmlentities($row['categoryName']);?></td>

                                            <td><?php echo htmlentities($row['productCompany']);?></td>

                                            <td><?php echo htmlentities($row['postingDate']);?></td>

                                            <td>

                                                <a href="edit-products.php?id=<?php echo $row['id']?>" ><i class="icon-edit"></i></a>

                                                <a href="manage-products.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a>

                                            </td>

                                        </tr>

<?php $cnt=$cnt+1; } ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>                      

                    </div>

                </div>

            </div>

        </div>

    </div>



<?php include('include/footer.php');?>



    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>

    <script src="scripts/datatables/jquery.dataTables.js"></script>

    <script>

        $(document).ready(function() {

            $('.datatable-1').dataTable();

        });

    </script>

</body>

<?php } ?>buatkan full