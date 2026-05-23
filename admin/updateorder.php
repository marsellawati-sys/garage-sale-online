<?php
session_start();
include('include/config.php');

// 1. Proteksi Login Admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    $oid = intval($_GET['oid']);
    
    // 2. Logika Update Status
    if(isset($_POST['submit'])) {
        $status = $_POST['status'];
        $remark = mysqli_real_escape_string($con, $_POST['remark']);
        
        // Simpan ke riwayat track
        mysqli_query($con, "INSERT INTO ordertrackhistory(orderId, status, remark) VALUES ('$oid', '$status', '$remark')");
        // Update status utama di tabel orders
        $query = mysqli_query($con, "UPDATE orders SET orderStatus='$status' WHERE id='$oid'");
        
        if($query) {
            echo "<script>alert('Status Berhasil Diperbarui!'); window.opener.location.reload(); window.close();</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Status Pesanan</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <style>
        body { padding: 30px; background: #fdfdfd; font-family: 'Segoe UI', sans-serif; }
        .box-container { background: white; padding: 25px; border-radius: 8px; border: 1px solid #e3e3e3; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; margin-bottom: 8px; display: block; }
        .btn-group-custom { margin-top: 25px; display: flex; gap: 10px; }
    </style>
</head>
<body>

<div class="box-container">
    <h3 style="margin-top:0;">Update Pesanan #NEX-<?php echo $oid; ?></h3>
    <hr>

    <form method="post">
        <div class="form-group">
            <label>Pilih Status Baru:</label>
            <select name="status" required class="span4">
                <option value="">-- Pilih Status --</option>
                <option value="In Process">In Process (Sedang Dikemas)</option>
                <option value="Shipped">Shipped (Sudah Dikirim)</option>
                <option value="Delivered">Delivered (Sudah Diterima)</option>
                <option value="Cancelled">Cancelled (Dibatalkan)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Catatan / Nomor Resi:</label>
            <textarea name="remark" class="span4" rows="5" required placeholder="Masukkan keterangan untuk pembeli..."></textarea>
        </div>

        <div class="btn-group-custom">
            <button type="submit" name="submit" class="btn btn-primary">
                <i class="icon-save"></i> Update Sekarang
            </button>
            
            <button type="button" class="btn btn-default" onclick="window.close();">
                <i class="icon-arrow-left"></i> Kembali / Batal
            </button>
        </div>
    </form>
</div>

</body>
</html>
<?php } ?>