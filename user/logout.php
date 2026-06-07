<?php
session_start();
include("includes/config.php");

// Perbaikan: Menggunakan operator '=' untuk menghapus session login
$_SESSION['login'] = "";

// Perbaikan: Mengubah timezone menjadi Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');
$ldate = date('d-m-Y h:i:s A', time());

// Update data logout ke database
mysqli_query($con, "UPDATE userlog SET logout = '$ldate' WHERE userEmail = '" . $_SESSION['login'] . "' ORDER BY id DESC LIMIT 1");

// Hancurkan semua session
session_unset();
session_destroy();

$_SESSION['errmsg'] = "You have successfully logged out";
?>
<script language="javascript">
document.location = "index.php";
</script>
