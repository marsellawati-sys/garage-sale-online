
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

// Proteksi Keamanan: Jika belum login, kembalikan ke index
if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

$uid = $_SESSION['id'];

// ==========================================
// 1. LOGIKA PROSES UPDATE INFO PROFIL UTAMA
// ==========================================
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    
    $query = mysqli_query($con, "UPDATE users SET name='$name', contactno='$contactno' WHERE id='$uid'");
    if ($query) {
        echo "<script>alert('Profil Utama Berhasil Diperbarui!'); window.location='my-account.php';</script>";
        exit();
    }
}

// ==========================================
// 2. LOGIKA PROSES UPDATE ALAMAT DENGAN API
// ==========================================
if (isset($_POST['update_address'])) {
    $billingaddress = mysqli_real_escape_string($con, $_POST['billingaddress']);
    $shippingcity   = mysqli_real_escape_string($con, $_POST['shippingcity']); // Hasil gabungan API: "Kecamatan, Kota, Provinsi"
    $billingpincode = mysqli_real_escape_string($con, $_POST['billingpincode']);

    // Update data ke database (Billing & Shipping disamakan agar mempermudah hitung ongkir otomatis)
    $query = mysqli_query($con, "UPDATE users SET 
        billingAddress='$billingaddress', 
        shippingAddress='$billingaddress', 
        billingCity='$shippingcity', 
        shippingCity='$shippingcity', 
        billingPincode='$billingpincode', 
        shippingPincode='$billingpincode' 
        WHERE id='$uid'");
    
    if ($query) {
        echo "<script>alert('Alamat & Wilayah Pengiriman Berhasil Diperbarui!'); window.location='my-account.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui alamat: " . mysqli_error($con) . "');</script>";
    }
}

// ==========================================
// 3. LOGIKA PROSES UPDATE GANTI PASSWORD
// ==========================================
if (isset($_POST['chg_pass'])) {
    $current_pass = md5($_POST['cpass']);
    $new_pass     = md5($_POST['newpass']);
    
    // Cek apakah password lama cocok
    $sql = mysqli_query($con, "SELECT password FROM users WHERE id='$uid' AND password='$current_pass'");
    $num = mysqli_num_rows($sql);
    
    if ($num > 0) {
        $query_pass = mysqli_query($con, "UPDATE users SET password='$new_pass' WHERE id='$uid'");
        if ($query_pass) {
            echo "<script>alert('Password Berhasil Diubah!'); window.location='my-account.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Password Lama Tidak Sesuai!'); window.location='my-account.php';</script>";
    }
}

// Ambil data user terkini untuk dicetak ke dalam value Form
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>My Account | GarageSale</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --accent-dark: #111111;
            --border-color: #e8e4d8;
            --bg-light: #fdfcfb;
            --danger-color: #dc3545;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; color: #333; }
        .logo { font-weight: 900; font-size: 28px; letter-spacing: -1.5px; text-decoration: none; color: var(--accent-dark); }
        .top-brand-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; border-bottom: 1px solid var(--border-color); }
        .account-container { padding: 40px 5%; }
        
        .nav-tabs-custom { border-bottom: 2px solid var(--border-color); margin-bottom: 30px; display: flex; gap: 10px; }
        .nav-tabs-custom .nav-link { border: none; color: #888; font-weight: 700; padding: 12px 18px; font-size: 13px; text-transform: uppercase; border-radius: 10px 10px 0 0; }
        .nav-tabs-custom .nav-link.active { color: var(--accent-dark); border-bottom: 3px solid var(--accent-dark); background: transparent; }
        
        .card-custom { background: #fff; border: 1px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.01); margin-bottom: 30px; }
        .form-label { font-weight: 700; font-size: 12px; color: #555; text-transform: uppercase; margin-top: 15px; margin-bottom: 5px; display: block; }
        .form-control { border-radius: 10px; border: 1px solid #dcdcdc; padding: 10px 15px; font-size: 14px; font-weight: 600; }
        .form-control:focus { border-color: var(--accent-dark); box-shadow: none; }
        
        .btn-save { background: var(--accent-dark); color: #fff; border-radius: 12px; padding: 12px 25px; font-weight: 800; border: none; font-size: 13px; text-transform: uppercase; margin-top: 20px; }
        .btn-save:hover { background: #222; }
        .current-address-badge { background: var(--bg-light); border: 1px solid var(--border-color); padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>

<div class="top-brand-row">
    <a href="index.php" class="logo">GarageSale.</a>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold"><i class="fa fa-home me-1"></i> Beranda</a>
        <a href="my-cart.php" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold"><i class="fa fa-shopping-cart me-1"></i> Keranjang</a>
        <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold" onclick="return confirm('Apakah Anda yakin ingin keluar dari akun?');"><i class="fa fa-sign-out-alt me-1"></i> Keluar</a>
    </div>
</div>

<div class="container account-container">
    <h2 class="fw-800 mb-1">Akun Saya</h2>
    <p class="text-muted small mb-4">Kelola data profil, alamat kirim terhubung API wilayah, dan keamanan password Anda.</p>
    
    <ul class="nav nav-tabs nav-tabs-custom" id="accountTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="p1-tab" data-bs-toggle="tab" data-bs-target="#panel-profil" type="button">Profil Utama</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="p2-tab" data-bs-toggle="tab" data-bs-target="#panel-alamat" type="button">Alamat Pengiriman (API)</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="p3-tab" data-bs-toggle="tab" data-bs-target="#panel-password" type="button">Keamanan Password</button>
        </li>
    </ul>

    <div class="tab-content" id="accountTabsContent">
        
        <div class="tab-pane fade show active" id="panel-profil" role="tabpanel">
            <div class="card-custom col-lg-7">
                <h5 class="fw-800 mb-3 text-uppercase" style="font-size:14px;"><i class="fa fa-user me-2"></i>Informasi Profil</h5>
                <form method="post" action="my-account.php">
                    <div class="mb-2">
                        <label class="form-label">Alamat Email (Login)</label>
                        <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly disabled>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nama Penerima Paket</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. Handphone / WhatsApp</label>
                        <input type="text" class="form-control" name="contactno" value="<?php echo htmlspecialchars($user['contactno'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn-save">Update Profil</button>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="panel-alamat" role="tabpanel">
            <div class="card-custom col-lg-8">
                <h5 class="fw-800 mb-2 text-uppercase" style="font-size:14px;"><i class="fa fa-map-location-dot me-2"></i>Alamat & Destinasi Pengiriman</h5>
                <p class="text-muted small mb-4">Sistem mendeteksi wilayah otomatis guna menghitung ongkir murah Pulau Jawa vs Luar Pulau Jawa di Kasir.</p>
                
                <?php if(!empty($user['shippingCity'])): ?>
                <div class="current-address-badge">
                    <strong class="d-block text-dark mb-1"><i class="fa fa-circle-check text-success me-1"></i> Alamat Terdaftar Saat Ini:</strong>
                    <div class="text-secondary"><?php echo htmlspecialchars($user['shippingAddress'] ?? '-'); ?></div>
                    <div class="fw-bold text-dark mt-1"><?php echo htmlspecialchars($user['shippingCity'] ?? '-'); ?> (Kodepos: <?php echo htmlspecialchars($user['shippingPincode'] ?? '-'); ?>)</div>
                </div>
                <?php endif; ?>

                <form method="post" action="my-account.php">
                    <div class="mb-3">
                        <label class="form-label">Nama Jalan / Blok / No. Rumah</label>
                        <textarea class="form-control" name="billingaddress" rows="3" placeholder="Contoh: Jl. Diponegoro No. 45, RT 01/RW 04" required><?php echo htmlspecialchars($user['shippingAddress'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <select class="form-control form-select" id="api_provinsi" required>
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kota / Kabupaten</label>
                        <select class="form-control form-select" id="api_kota" required disabled>
                            <option value="">-- Pilih Kota / Kabupaten --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kecamatan</label>
                        <select class="form-control form-select" id="api_kecamatan" required disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" class="form-control" name="billingpincode" value="<?php echo htmlspecialchars($user['shippingPincode'] ?? ''); ?>" required placeholder="5 Digit Angka">
                    </div>

                    <input type="hidden" id="api_shippingcity" name="shippingcity" value="<?php echo htmlspecialchars($user['shippingCity'] ?? ''); ?>">

                    <button type="submit" name="update_address" class="btn-save">Simpan Perubahan Alamat</button>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="panel-password" role="tabpanel">
            <div class="card-custom col-lg-6">
                <h5 class="fw-800 mb-3 text-uppercase" style="font-size:14px;"><i class="fa fa-lock me-2"></i>Ubah Password Akun</h5>
                <form method="post" action="my-account.php" onSubmit="return valid();">
                    <div class="mb-2">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" name="cpass" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="newpass" name="newpass" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="cnfpass" name="cnfpass" required>
                    </div>
                    <button type="submit" name="chg_pass" class="btn-save">Simpan Password Baru</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectProv = document.getElementById("api_provinsi");
    const selectKota = document.getElementById("api_kota");
    const selectKec  = document.getElementById("api_kecamatan");
    const inputHiddenCity = document.getElementById("api_shippingcity");

    // Endpoint Server API Resmi Wilayah Admin Indonesia (EMSifa Open-Source CDN)
    const baseUrl = "https://www.emsifa.com/api-wilayah-indonesia/api";

    // 1. Mengambil Seluruh Provinsi
    fetch(`${baseUrl}/provinces.json`)
        .then(res => res.json())
        .then(data => {
            data.forEach(prov => {
                let option = document.createElement("option");
                option.value = prov.id;
                option.textContent = prov.name;
                selectProv.appendChild(option);
            });
        })
        .catch(err => {
            console.error("Gagal terhubung ke server API Wilayah:", err);
            selectProv.innerHTML = '<option value="">Gagal memuat API otomatis, silakan segarkan halaman</option>';
        });

    // 2. Event Mengambil Opsi Kota dari Provinsi Terpilih
    selectProv.addEventListener("change", function () {
        selectKota.innerHTML = '<option value="">-- Pilih Kota / Kabupaten --</option>';
        selectKec.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>';
        selectKota.disabled  = true;
        selectKec.disabled   = true;

        if (this.value) {
            fetch(`${baseUrl}/regencies/${this.value}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kota => {
                        let option = document.createElement("option");
                        option.value = kota.id;
                        option.textContent = kota.name;
                        selectKota.appendChild(option);
                    });
                    selectKota.disabled = false;
                });
        }
        gabungkanTeksOtomatis();
    });

    // 3. Event Mengambil Opsi Kecamatan dari Kota Terpilih
    selectKota.addEventListener("change", function () {
        selectKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selectKec.disabled  = true;

        if (this.value) {
            fetch(`${baseUrl}/districts/${this.value}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kec => {
                        let option = document.createElement("option");
                        option.value = kec.id;
                        option.textContent = kec.name;
                        selectKec.appendChild(option);
                    });
                    selectKec.disabled = false;
                });
        }
        gabungkanTeksOtomatis();
    });

    // 4. Update data saat kecamatan diubah
    selectKec.addEventListener("change", gabungkanTeksOtomatis);

    // Fungsi Penggabung String Teks Pilihan Menjadi Teks SQL Baku
    function gabungkanTeksOtomatis() {
        const textProv = selectProv.selectedIndex > 0 ? selectProv.options[selectProv.selectedIndex].text : "";
        const textKota = selectKota.selectedIndex > 0 ? selectKota.options[selectKota.selectedIndex].text : "";
        const textKec  = selectKec.selectedIndex > 0 ? selectKec.options[selectKec.selectedIndex].text : "";

        if (textKec && textKota && textProv) {
            inputHiddenCity.value = `${textKec}, ${textKota}, ${textProv}`;
        } else {
            inputHiddenCity.value = "";
        }
    }
});

function valid() {
    if(document.getElementById("newpass").value != document.getElementById("cnfpass").value) {
        alert("Konfirmasi Password Baru Anda Tidak Cocok!");
        document.getElementById("cnfpass").focus();
        return false;
    }
    return true;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
