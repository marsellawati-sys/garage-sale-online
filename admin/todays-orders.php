
<?php
session_start();
include('include/config.php');

// Proteksi halaman admin
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Jakarta');
    
    $order_id = intval($_GET['oid']);

    // --- LOGIKA UPDATE STATUS & REMARK (Sama dengan bawaan asli, dijamin aman) ---
    if(isset($_POST['submit2'])) {
        $status = mysqli_real_escape_string($con, $_POST['status']);
        $remark = mysqli_real_escape_string($con, $_POST['remark']);
        
        // Amandemen memperbarui status orderan di database
        $query_update = mysqli_query($con, "UPDATE orders SET orderStatus='$status' WHERE id='$order_id'");
        
        // Memasukkan riwayat pelacakan ke tabel ordertrackhistory jika ada di sistem database kamu
        // Jika tabel track tidak dipakai, query ini aman dan tidak akan merusak tabel orders utama
        @mysqli_query($con, "INSERT INTO ordertrackhistory(orderId,status,remark) VALUES('$order_id','$status','$remark')");
        
        echo "<script>alert('Status pesanan baju thrift berhasil diperbarui!'); window.close(); window.opener.location.reload();</script>";
        exit();
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otorisasi Pengiriman #<?php echo $order_id; ?> | GS Studio</title>
    
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f7f5;
            padding: 25px;
            margin: 0;
        }

        .popup-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(141, 119, 95, 0.08);
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e1e1e;
            margin-bottom: 5px;
            letter-spacing: -0.3px;
        }
        .header-subtitle {
            color: #888;
            font-size: 12px;
            display: block;
            margin-bottom: 25px;
        }

        /* Elements Styling Form */
        .field-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }
        .field-block label { 
            font-weight: 700; 
            font-size: 13px; 
            color: #1e1e1e; 
            margin: 0;
        }
        .field-block select, .field-block textarea {
            width: 100% !important;
            background: #fdfdfd;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
            box-sizing: border-box;
            transition: 0.2s;
            box-shadow: none !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .field-block textarea { 
            height: 100px; 
            resize: none; 
        }
        .field-block select:focus, .field-block textarea:focus { 
            border-color: #8d775f !important; 
            background: #fff;
        }

        /* --- ACTION BUTTONS --- */
        .btn-flex-group {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
        .btn-modern-save {
            flex: 2;
            background: #1e1e1e; 
            color: #ffffff !important;
            border: none; 
            border-radius: 12px; 
            padding: 14px;
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 12px;
            cursor: pointer; 
            transition: 0.2s;
            letter-spacing: 0.5px;
        }
        .btn-modern-save:hover { 
            background: #8d775f; 
        }
        .btn-modern-close {
            flex: 1;
            background: #f2f0eb; 
            color: #555 !important;
            border: 1px solid #e6e2d6; 
            border-radius: 12px; 
            padding: 14px;
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 12px;
            text-align: center;
            cursor: pointer; 
            transition: 0.2s;
            text-decoration: none !important;
        }
        .btn-modern-close:hover { 
            background: #e6e2d6;
        }
    </style>
</head>
<body>

    <div class="popup-box">
        <div class="header-title">📋 Otorisasi Status Invoice</div>
        <span class="header-subtitle">ID Transaksi Pesanan: <b>#<?php echo $order_id; ?></b></span>

        <form name="updateticket" id="updateticket" method="post">
            
            <div class="field-block">
                <label>Pilih Tahap Status Transaksi</label>
                <select name="status" required>
                    <option value="">-- Pilih Status Terbaru --</option>
                    <option value="Pending">Pending (Menunggu Pembayaran/Diproses)</option>
                    <option value="Delivered">Delivered (Barang Dikirim & Selesai Lunas)</option>
                </select>
            </div>

            <div class="field-block">
                <label>Catatan Logistik / Nomor Resi Kurir</label>
                <textarea name="remark" placeholder="Contoh: Paket telah diserahkan ke kurir J&T. No Resi: JX123456789 atau Catatan pembatalan jika ada..." required></textarea>
            </div>

            <div class="btn-flex-group">
                <button type="submit" name="submit2" class="btn-modern-save">Perbarui Pesanan</button>
                <a href="javascript:void(0);" onClick="window.close();" class="btn-modern-close">Batal</a>
            </div>

        </form>
    </div>

</body>
</html>
<?php } ?>
