<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0) {   
    header('location:index.php');
    exit();
} else {
    $uid = $_SESSION['id'];

    // --- 1. LOGIKA UPDATE PROFIL & ALAMAT ---
    if(isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $contact = mysqli_real_escape_string($con, $_POST['contactno']);
        $address = mysqli_real_escape_string($con, $_POST['address_main']);
        $city = mysqli_real_escape_string($con, $_POST['city_main']); 
        $state = mysqli_real_escape_string($con, $_POST['state_main']); 
        $pincode = mysqli_real_escape_string($con, $_POST['pincode_main']);

        $query = mysqli_query($con, "UPDATE users SET name='$name', contactno='$contact', shippingAddress='$address', shippingCity='$city', shippingState='$state', shippingPincode='$pincode' WHERE id='$uid'");
        
        if($query) {
            echo "<script>alert('Profil & Alamat diperbarui!'); location.href='my-account.php';</script>";
        } else {
            echo "<script>alert('Gagal update: " . mysqli_error($con) . "');</script>";
        }
    }

    // --- 2. LOGIKA GANTI PASSWORD ---
    if(isset($_POST['change_pass'])) {
        $current = md5($_POST['current_pass']);
        $new = md5($_POST['new_pass']);
        $confirm = md5($_POST['confirm_pass']);

        if($new != $confirm) {
            echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
        } else {
            $sql = mysqli_query($con, "SELECT password FROM users WHERE id='$uid' AND password='$current'");
            if(mysqli_num_rows($sql) > 0) {
                mysqli_query($con, "UPDATE users SET password='$new' WHERE id='$uid'");
                echo "<script>alert('Password berhasil diperbarui!');</script>";
            } else {
                echo "<script>alert('Password lama salah!');</script>";
            }
        }
    }

    // Ambil data user terbaru
    $query = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
    $row = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Sale | Akun Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root { --accent: #d4a373; --dark: #111111; --border: #e8e4d8; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fdfcfb; padding-top: 80px; color: var(--dark); }
        .card-custom { background: #fff; border-radius: 25px; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
        .sidebar { background: #fff; border-right: 1px solid var(--border); padding: 30px; height: 100%; min-height: 500px; }
        .nav-pills .nav-link { color: #777; font-weight: 600; border-radius: 12px; padding: 12px 20px; margin-bottom: 5px; text-align: left; transition: 0.3s; border: none; width: 100%; }
        .nav-pills .nav-link.active { background: var(--dark); color: #fff; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid var(--border); background: #fafafa; font-size: 14px; }
        .btn-dark-custom { background: var(--dark); color: #fff; border-radius: 12px; padding: 12px 25px; font-weight: 700; border: none; width: 100%; transition: 0.3s; cursor: pointer; }
        .btn-dark-custom:hover { opacity: 0.8; }
    </style>
</head>
<body>

<header class="fixed-top bg-white border-bottom py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="fw-800 fs-4 text-dark text-decoration-none" style="font-weight: 800;">Garage Sale.</a>
        <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Keluar</a>
    </div>
</header>

<div class="container my-5">
    <div class="card-custom shadow-sm">
        <div class="row g-0">
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background:var(--accent); color:#fff; border-radius:50%; font-size:22px; font-weight:800;">
                            <?php echo strtoupper(substr(trim($row['name'] ?? 'U'), 0, 1)); ?>
                        </div>
                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($row['name']); ?></h6>
                    </div>
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-profile"><i class="fa fa-map-marker-alt me-2"></i> Profil & Alamat</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-security"><i class="fa fa-lock me-2"></i> Keamanan</button>
                        <button class="nav-link" onclick="location.href='order-history.php'"><i class="fa fa-shopping-bag me-2"></i> Pesanan</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="tab-content p-4 p-md-5">
                    
                    <div class="tab-pane fade show active" id="tab-profile">
                        <h5 class="fw-800 mb-4" style="font-weight: 800;">Data Diri & Pengiriman</h5>
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">WhatsApp</label>
                                    <input type="text" name="contactno" class="form-control" value="<?php echo htmlspecialchars($row['contactno']); ?>" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="small fw-bold">Provinsi (Sekarang: <?php echo $row['shippingState']; ?>)</label>
                                    <select id="provinsi" class="form-select"></select>
                                    <input type="hidden" name="state_main" id="state_name" value="<?php echo $row['shippingState']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Kota/Kabupaten (Sekarang: <?php echo $row['shippingCity']; ?>)</label>
                                    <select id="kota" class="form-select" disabled><option value="">Pilih Provinsi Dahulu</option></select>
                                    <input type="hidden" name="city_main" id="city_name" value="<?php echo $row['shippingCity']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Kode Pos</label>
                                    <input type="text" name="pincode_main" class="form-control" value="<?php echo $row['shippingPincode']; ?>">
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold">Alamat Lengkap (Jalan/No.Rumah)</label>
                                    <textarea name="address_main" class="form-control" rows="3"><?php echo $row['shippingAddress']; ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update_profile" class="btn-dark-custom mt-4">Simpan Perubahan</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab-security">
                        <h5 class="fw-800 mb-4" style="font-weight: 800;">Ganti Password</h5>
                        <form method="post" style="max-width: 400px;">
                            <div class="mb-3">
                                <label class="small fw-bold">Password Saat Ini</label>
                                <input type="password" name="current_pass" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Password Baru</label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_pass" class="form-control" required>
                            </div>
                            <button type="submit" name="change_pass" class="btn-dark-custom">Update Password</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    const API_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    // 1. Ambil Data Provinsi
    fetch(`${API_URL}/provinces.json`)
        .then(response => response.json())
        .then(provinces => {
            let options = '<option value="">Pilih Provinsi...</option>';
            provinces.sort((a, b) => a.name.localeCompare(b.name));
            provinces.forEach(prov => {
                options += `<option value="${prov.id}">${prov.name}</option>`;
            });
            $('#provinsi').html(options);
        });

    // 2. Ketika Provinsi Dipilih -> Ambil Kota
    $('#provinsi').change(function() {
        let provId = $(this).val();
        let provName = $("#provinsi option:selected").text();
        $('#state_name').val(provName); // Simpan nama provinsi ke hidden input

        if(provId) {
            fetch(`${API_URL}/regencies/${provId}.json`)
                .then(response => response.json())
                .then(regencies => {
                    let options = '<option value="">Pilih Kota/Kabupaten...</option>';
                    regencies.sort((a, b) => a.name.localeCompare(b.name));
                    regencies.forEach(city => {
                        options += `<option value="${city.id}" data-name="${city.name}">${city.name}</option>`;
                    });
                    $('#kota').html(options).prop('disabled', false);
                });
        } else {
            $('#kota').html('<option value="">Pilih Provinsi Dahulu</option>').prop('disabled', true);
        }
    });

    // 3. Ketika Kota Dipilih -> Simpan Nama Kota
    $('#kota').change(function() {
        let cityName = $("#kota option:selected").data('name');
        $('#city_name').val(cityName);
    });
});
</script>
</body>
</html>
<?php } ?>