<?php
session_start();
include('includes/config.php');
$oid = intval($_GET['oid']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lacak Pesanan #<?php echo $oid; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; font-family: sans-serif; background: #fff; }
        .timeline { position: relative; padding-left: 20px; }
        .timeline-item { border-left: 2px solid #eee; padding: 0 0 20px 20px; position: relative; }
        .timeline-item::after { content: ''; position: absolute; left: -7px; top: 0; width: 12px; height: 12px; background: #111; border-radius: 50%; }
        .status { font-weight: 800; font-size: 13px; text-transform: uppercase; display: block; }
        .date { font-size: 11px; color: #aaa; }
    </style>
</head>
<body>
    <h5 class="fw-bold mb-4">Riwayat Pengiriman #<?php echo $oid; ?></h5>
    <div class="timeline">
        <?php 
        $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid' ORDER BY postingDate DESC");
        if(mysqli_num_rows($ret) > 0) {
            while($row = mysqli_fetch_array($ret)) {
        ?>
        <div class="timeline-item">
            <span class="status"><?php echo $row['status']; ?></span>
            <p class="small mb-0 text-muted"><?php echo $row['remark']; ?></p>
            <span class="date"><?php echo $row['postingDate']; ?></span>
        </div>
        <?php } } else { echo "Belum ada riwayat."; } ?>
    </div>
    <button class="btn btn-dark btn-sm w-100 mt-4" onclick="window.close()">TUTUP</button>
</body>
</html>