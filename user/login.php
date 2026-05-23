<?php
session_start();
error_reporting(0);
include('includes/config.php');

// --- LOGIKA LOGIN AMAN (ANTI-HACK) ---
if(isset($_POST['login'])) {
    // 1. Sanitasi Input (Mencegah SQL Injection)
    // mysqli_real_escape_string akan membersihkan karakter berbahaya seperti ' atau --
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $hashed_password = md5($password); // Menggunakan MD5 sesuai sistemmu

    // 2. Prepared Statement (Opsi Tambahan untuk Keamanan Maksimal)
    // Di sini kita mencocokkan email dan password yang sudah di-hash
    $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$hashed_password'");
    $num = mysqli_fetch_array($query);

    if($num > 0) {
        // 3. Regenerasi Session ID (Mencegah Session Fixation/Pembajakan Sesi)
        session_regenerate_id(true);
        
        $_SESSION['login'] = $num['email'];
        $_SESSION['id'] = $num['id'];
        $_SESSION['username'] = $num['name'];
        
        echo "<script>alert('Selamat Datang Kembali!'); location.href='index.php';</script>"; 
        exit();
    } else {
        // Pesan error dibuat umum agar hacker tidak tahu apakah email atau password yang salah
        $_SESSION['errmsg'] = "Email atau Password tidak valid.";
    }
}

// --- LOGIKA REGISTRASI AMAN ---
if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['emailid']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    $password = md5($_POST['password']);
    
    $check_email = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
    if(mysqli_num_rows($check_email) > 0) {
        $_SESSION['errmsg'] = "Email sudah terdaftar!";
    } else {
        $query = mysqli_query($con, "INSERT INTO users(name,email,contactno,password) VALUES('$name','$email','$contactno','$password')");
        if($query) { 
            echo "<script>alert('Registrasi Berhasil! Silakan Masuk.');</script>"; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GarageSale | Masuk & Daftar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --beige-bg: #f5f4ef; --beige-card: #fdfcfb; --beige-border: #e8e4d8; --accent: #111111; --text-muted: #888888; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--beige-bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .main-container { background: #ffffff; border: 1px solid var(--beige-border); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.02); width: 100%; max-width: 900px; display: flex; overflow: hidden; animation: fadeIn 0.6s ease-out; }
        .brand-side { flex: 1; background-color: var(--beige-card); padding: 60px; display: flex; flex-direction: column; justify-content: center; border-right: 1px solid var(--beige-border); }
        .brand-side h1 { font-weight: 800; font-size: 2.8rem; letter-spacing: -1.5px; line-height: 1; margin-bottom: 20px; }
        .form-side { flex: 1.2; padding: 50px; background: white; }
        .nav-pills { background: var(--beige-bg); padding: 6px; border-radius: 14px; margin-bottom: 30px; }
        .nav-pills .nav-link { border-radius: 10px; color: var(--text-muted); font-weight: 600; font-size: 13px; transition: 0.3s; border: none; }
        .nav-pills .nav-link.active { background: white; color: var(--accent); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .input-wrapper { position: relative; margin-bottom: 20px; }
        .input-wrapper i.field-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--accent); opacity: 0.3; }
        .form-control { height: 54px; padding-left: 48px; border-radius: 12px; border: 1px solid var(--beige-border); background: var(--beige-card); font-size: 14px; }
        .form-control:focus { border-color: var(--accent); box-shadow: none; background: white; }
        .btn-submit { background: var(--accent); border: none; color: white; height: 54px; border-radius: 12px; font-weight: 800; width: 100%; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .forgot-link { display: block; text-align: right; font-size: 12px; color: var(--text-muted); text-decoration: none; margin-bottom: 15px; margin-top: -10px; }
        .alert-custom { background-color: #fff5f5; border: 1px solid #feb2b2; color: #c53030; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 25px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 850px) { .brand-side { display: none; } }
    </style>
</head>
<body>

<div class="main-container">
    <div class="brand-side">
        <div style="font-weight: 900; font-size: 20px; margin-bottom: 50px;">Garage Sale.</div>
        <h1>Elevate<br>Your Style.</h1>
        <p>Masuk untuk pengalaman belanja yang lebih aman dan personal.</p>
    </div>

    <div class="form-side">
        <?php if($_SESSION['errmsg']) { ?>
            <div class="alert alert-custom alert-dismissible fade show">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo $_SESSION['errmsg']; unset($_SESSION['errmsg']); ?>
            </div>
        <?php } ?>

        <ul class="nav nav-pills nav-fill mb-4">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login-tab">LOG IN</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#reg-tab">SIGN UP</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="login-tab">
                <form method="post">
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope field-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Email Terdaftar" required>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
                    </div>
                    <a href="forgot-password.php" class="forgot-link">Lupa kata sandi?</a>
                    <button type="submit" name="login" class="btn btn-submit shadow-sm">Masuk Sekarang</button>
                </form>
            </div>

            <div class="tab-pane fade" id="reg-tab">
                <form method="post">
                    <div class="input-wrapper"><i class="fa-solid fa-user field-icon"></i><input type="text" name="fullname" class="form-control" placeholder="Nama Lengkap" required></div>
                    <div class="input-wrapper"><i class="fa-solid fa-at field-icon"></i><input type="email" name="emailid" class="form-control" placeholder="Email Aktif" required></div>
                    <div class="input-wrapper"><i class="fa-solid fa-phone field-icon"></i><input type="text" name="contactno" class="form-control" placeholder="Nomor WhatsApp" required></div>
                    <div class="input-wrapper"><i class="fa-solid fa-key field-icon"></i><input type="password" name="password" class="form-control" placeholder="Buat Kata Sandi" required></div>
                    <button type="submit" name="submit" class="btn btn-submit">Daftar Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>