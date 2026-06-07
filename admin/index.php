<?php
session_start();
error_reporting(0);
include("include/config.php");

// --- LOGIKA LOGIN (Terhubung langsung & aman ke database) ---
if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = md5($_POST['password']);
    
    // Memeriksa kecocokan data ke tabel admin di database MySQL
    $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password'");
    $num = mysqli_fetch_array($ret);
    if($num > 0)
    {
        $_SESSION['alogin'] = $_POST['username'];
        $_SESSION['id'] = $num['id'];
        // Dialihkan langsung ke Dashboard Analytics utama
        header("location:dashboard.php");
        exit();
    }
    else
    {
        $_SESSION['errmsg'] = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Gerbang Admin | Garage Sale Studio</title>
    
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Fraunces:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f2f0eb; /* Warna canvas hangat krem toko depan */
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            box-sizing: border-box;
        }

        .login-box {
            background: #ffffff;
            border-radius: 28px;
            padding: 45px 40px;
            box-shadow: 0 20px 50px rgba(141, 119, 95, 0.1);
            border: 1px solid rgba(141, 119, 95, 0.08);
            position: relative;
            overflow: hidden;
        }

        .brand-title {
            font-family: 'Fraunces', serif;
            font-size: 32px;
            font-weight: 700;
            color: #1e1e1e;
            text-align: center;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #8d775f;
            text-align: center;
            display: block;
            margin-bottom: 35px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- STYLING FORM INPUT MODERN --- */
        .input-group-custom {
            position: relative;
            margin-bottom: 22px;
        }

        .input-group-custom input {
            width: 100%;
            height: 54px;
            background: #fdfdfd;
            border: 1.5px solid #e6e2d6 !important;
            border-radius: 14px !important;
            padding: 10px 20px !important;
            font-size: 14px !important;
            color: #1e1e1e !important;
            font-weight: 600;
            box-sizing: border-box;
            box-shadow: none !important;
            transition: all 0.3s ease;
        }

        .input-group-custom input:focus {
            border-color: #8d775f !important;
            background: #ffffff;
        }

        /* --- BUTTONS --- */
        .btn-action-submit {
            width: 100%;
            height: 54px;
            background: #1e1e1e;
            color: #ffffff !important;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(30, 30, 30, 0.15);
        }

        .btn-action-submit:hover {
            background: #8d775f;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(141, 119, 95, 0.25);
        }

        /* --- NOTIFIKASI ALERTS --- */
        .alert-custom {
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }
        .alert-custom-danger { background: #fdf2f2; color: #de3b3b; border: 1px solid #fbe3e3; }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-box">
            <div class="brand-title">GS. Studio</div>
            <div class="brand-subtitle">Control Panel Gate</div>

            <?php if(isset($_SESSION['errmsg'])) { ?>
                <div class="alert-custom alert-custom-danger">
                    <?php echo $_SESSION['errmsg']; unset($_SESSION['errmsg']); ?>
                </div>
            <?php } ?>

            <form id="loginForm" method="post">
                <div class="input-group-custom">
                    <input type="text" name="username" placeholder="Username Administrator" required autocomplete="off">
                </div>
                <div class="input-group-custom">
                    <input type="password" name="password" placeholder="Password Sistem" required>
                </div>
                <button type="submit" name="login" class="btn-action-submit">Masuk Dashboard</button>
            </form>

        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
