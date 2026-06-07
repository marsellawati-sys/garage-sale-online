<?php
session_start();
include('includes/config.php');

// Ambil keyword dari form pencarian
$search_keyword = isset($_POST['product']) ? mysqli_real_escape_string($con, $_POST['product']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian: <?php echo htmlspecialchars($search_keyword); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
<body>
<div class="container mt-5">
    <h2>Hasil Pencarian untuk: "<?php echo htmlspecialchars($search_keyword); ?>"</h2>
    <hr>
    <div class="row">
        <?php
        if(!empty($search_keyword)) {
            // Query mencari berdasarkan Nama Produk ATAU Deskripsi Produk
            $sql = "SELECT * FROM products WHERE productName LIKE '%$search_keyword%' OR productDescription LIKE '%$search_keyword%'";
            $query = mysqli_query($con, $sql);
            
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_array($query)) {
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['productName']; ?></h5>
                                <p class="card-text">Rp <?php echo number_format($row['productPrice'], 0, ',', '.'); ?></p>
                                <a href="index.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-dark">Tambah ke Keranjang</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12'><p class='alert alert-warning'>Produk tidak ditemukan.</p></div>";
            }
        } else {
            echo "<div class='col-12'><p class='alert alert-danger'>Silakan masukkan kata kunci pencarian.</p></div>";
        }
        ?>
    </div>
</div>
</body>
</html>
