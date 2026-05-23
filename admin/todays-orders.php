
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
} else {
    date_default_timezone_set('Asia/Jakarta');
    $today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin | Todays Orders</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <script type="text/javascript">
        function popUpWindow(URLStr, width, height) {
            window.open(URLStr, 'updateorder', 'width='+width+',height='+height+',left=100,top=100,scrollbars=yes');
        }
    </script>
</head>
<body>
    <div class="container" style="margin-top:20px;">
        <div class="module">
            <div class="module-head">
                <h3>Pesanan Hari Ini (<?php echo date('d-M-Y'); ?>)</h3>
            </div>
            <div class="module-body table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Metode</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Query Gabungan (Sesuaikan orderDate dengan postingDate jika perlu)
                        $sql = "SELECT 
                                    users.name AS username, 
                                    products.productName AS pname, 
                                    orders.quantity AS qty, 
                                    products.productPrice AS price, 
                                    orders.paymentMethod AS paym, 
                                    orders.orderStatus AS status, 
                                    orders.id AS oid, 
                                    orders.shippingCharge AS ongkir 
                                FROM orders 
                                JOIN users ON orders.userId = users.id 
                                JOIN products ON orders.productId = products.id 
                                WHERE DATE(orders.orderDate) = '$today'";

                        $query = mysqli_query($con, $sql);

                        if(!$query) {
                            echo "<tr><td colspan='7' style='color:red;'>Error: " . mysqli_error($con) . "</td></tr>";
                        } else {
                            $cnt = 1;
                            while($row = mysqli_fetch_array($query)) {
                                $ongkir = $row['ongkir'] ?? 0;
                                $total = ($row['qty'] * $row['price']) + $ongkir;
                        ?>			
                        <tr>
                            <td><?php echo $cnt; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['pname']; ?> (x<?php echo $row['qty']; ?>)</td>
                            <td><?php echo $row['paym']; ?></td>
                            <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                $st = $row['status'];
                                if($st == 'Pending' || $st == '') echo "<span class='label label-warning'>Pending</span>";
                                elseif($st == 'Delivered') echo "<span class='label label-success'>Selesai</span>";
                                else echo "<span class='label label-info'>$st</span>";
                                ?>
                            </td>
                            <td>
                                <a href="javascript:void(0);" onClick="popUpWindow('update-order.php?oid=<?php echo $row['oid']; ?>', 600, 600);" class="btn btn-primary btn-small">Update</a>
                            </td>
                        </tr>
                        <?php $cnt++; } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>