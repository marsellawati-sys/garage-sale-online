<?php
include('include/config.php');

// Mengecek apakah ada kiriman cat_id dari fungsi AJAX di halaman produk
if(!empty($_POST["cat_id"])) 
{
    $id=intval($_POST['cat_id']); // Mengamankan input ID kategori
    
    // Mengambil semua subkategori yang memiliki ID kategori yang sesuai
    $query=mysqli_query($con,"SELECT * FROM subcategory WHERE categoryid=$id");
?>
    <option value="">Select Subcategory</option>
<?php
    while($row=mysqli_fetch_array($query))
    {
?>
        <option value="<?php echo htmlentities($row['id']); ?>">
            <?php echo htmlentities($row['subcategory']); ?>
        </option>
<?php
    }
}
?>