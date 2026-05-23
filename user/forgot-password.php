<?php
session_start();
error_reporting(0);
include('includes/config.php');

// --- LOGIKA RESET PASSWORD AMAN ---
if(isset($_POST['reset_pass'])) {
    // Sanitasi input untuk mencegah SQL Injection (Anti-Hack)
    $email = mysqli_real_escape_string($con, $_POST['email_reset']);
    $contact = mysqli_real_escape_string($con, $_POST['contact_reset']);
    $new_pass = mysqli_real_escape_string($con, $_POST['new_password']);
    $hashed_pass = md5($new_pass);

    // Cek kecocokan data
    $check = mysqli_query($con, "SELECT id FROM users WHERE email='$email' AND contactno='$contact'");
    $res = mysqli_fetch_array($check);

    if($res > 0) {
        // Jika cocok, update password
        $update = mysqli_query($con, "UPDATE users SET password='$hashed_pass' WHERE email='$email'");
        if($update) {
            echo "<script>alert('Password berhasil diperbarui! Silakan login kembali.'); window.location.href='login.php';</script>";
        }
    } else {
        $_SESSION['errmsg'] = "Data Verifikasi Salah! Pastikan Email & No. HP sesuai.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GarageSale | Secure Reset</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --beige-bg: #f5f4ef;
            --beige-card: #fdfcfb;
            --beige-border: #e8e4d8;
            --accent: #111111;
            --text-muted: #888888;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--beige-bg);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            margin: 0; padding: 20px;
        }

        .main-container {
            background: #ffffff;
            border: 1px solid var(--beige-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.02);
            width: 100%; max-width: 900px;
            display: flex; overflow: hidden;
        }

        .brand-side {
            flex: 1; background-color: var(--beige-card);
            padding: 60px; display: flex; flex-direction: column; justify-content: center;
            border-right: 1px solid var(--beige-border);
        }

        .brand-side h1 { font-weight: 800; font-size: 2.8rem; letter-spacing: -1.5px; line-height: 1; }

        .form-side { flex: 1.2; padding: 50px; background: white; }

        /* Alert Merah */
        .alert-custom {
            background-color: #fff5f5; border: 1px solid #feb2b2;
            color: #c53030; border-radius: 12px; font-size: 13px;
            font-weight: 600; margin-bottom: 25px; padding: 12px;
        }

        .input-wrapper { position: relative; margin-bottom: 18px; }

        .input-wrapper i.field-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: var(--accent); opacity: 0.3;
        }

        .form-control {
            height: 52px; padding-left: 48px; border-radius: 12px;
            border: 1px solid var(--beige-border); background: var(--beige-card); font-size: 14px;
        }

        .btn-submit {
            background: var(--accent); border: none; color: white;
            height: 52px; border-radius: 12px; font-weight: 800;
            width: 100%; margin-top: 10px; text-transform: uppercase;
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: 0.3s; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="brand-side">
        <div style="font-weight: 900; font-size: 20px; margin-bottom: 50px; letter-spacing: 1px;">Garage Sale.</div>
        <h1>Secure<br>Recovery.</h1>
        <p>Kami menjaga keamanan data Anda. Masukkan informasi yang valid untuk memulihkan akun.</p>
    </div>

    <div class="form-side">
        <h5 class="fw-800 mb-2" style="font-weight: 800;">Lupa Password?</h5>
        <p class="small text-muted mb-4">Lengkapi data verifikasi di bawah ini.</p>

        <?php if($_SESSION['errmsg']) { ?>
            <div class="alert alert-custom">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?php echo $_SESSION['errmsg']; unset($_SESSION['errmsg']); ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope field-icon"></i>
                <input type="email" name="email_reset" class="form-control" placeholder="Email Terdaftar" required>
            </div>
            
            <div class="input-wrapper">
                <i class="fa-solid fa-phone field-icon"></i>
                <input type="text" name="contact_reset" class="form-control" placeholder="Nomor WhatsApp" required>
            </div>

            <div class="input-wrapper">
                <i class="fa-solid fa-key field-icon"></i>
                <input type="password" name="new_password" id="newPass" class="form-control" placeholder="Password Baru" required>
                <i class="fa-solid fa-eye toggle-btn" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); cursor:pointer; color: #888;" onclick="togglePass()"></i>
            </div>

            <button type="submit" name="reset_pass" class="btn btn-submit shadow-sm">Ganti Password Sekarang</button>
            
            <div class="text-center mt-4">
                <a href="login.php" class="text-dark small fw-bold text-decoration-none"><i class="fa fa-arrow-left me-1"></i> Kembali Login</a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePass() {
        const input = document.getElementById('newPass');
        const icon = event.currentTarget;
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
</body>
</html>