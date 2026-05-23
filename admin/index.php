<?php
session_start();
error_reporting(0);
include("include/config.php");

// --- LOGIKA LOGIN ---
if(isset($_POST['login']))
{
    $username=$_POST['username'];
    $password=md5($_POST['password']);
    $ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$username' and password='$password'");
    $num=mysqli_fetch_array($ret);
    if($num>0)
    {
        $_SESSION['alogin']=$_POST['username'];
        $_SESSION['id']=$num['id'];
        header("location:manage-products.php");
        exit();
    }
    else
    {
        $_SESSION['errmsg']="Username atau Password salah!";
    }
}

// --- LOGIKA REGISTER ---
if(isset($_POST['register']))
{
    $username=$_POST['username'];
    $password=md5($_POST['password']);
    
    $check=mysqli_query($con,"SELECT * FROM admin WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $_SESSION['errmsg']="Username sudah terdaftar!";
    } else {
        $query=mysqli_query($con,"INSERT INTO admin(username,password) VALUES('$username','$password')");
        if($query) {
            $_SESSION['successmsg']="Registrasi berhasil! Silahkan Login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Sale | Admin Portal</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .module-head { background: #2d2d2d !important; color: white !important; }
        .nav-tabs { margin-bottom: 0; border-bottom: none; }
        .nav-tabs > li > a { border-radius: 4px 4px 0 0; background: #f9f9f9; }
        .active a { background: #fff !important; font-weight: bold; }
        .wrapper { padding-top: 60px; }
    </style>
</head>
<body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="index.php">Garage Sale | Admin Panel</a>
                <ul class="nav pull-right">
                    <li><a href="../">Back to Portal</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="span4 offset4">
                    
                    <ul class="nav nav-tabs" id="adminTab">
                        <li class="active"><a href="#loginTab" data-toggle="tab">Login</a></li>
                        <li><a href="#regTab" data-toggle="tab">Register</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="loginTab">
                            <div class="module module-login">
                                <form class="form-vertical" method="post">
                                    <div class="module-head"><h3>Sign In</h3></div>
                                    <div class="module-body">
                                        <?php if($_SESSION['errmsg']) { ?>
                                            <div class="alert alert-error"><?php echo $_SESSION['errmsg']; unset($_SESSION['errmsg']); ?></div>
                                        <?php } ?>
                                        <?php if($_SESSION['successmsg']) { ?>
                                            <div class="alert alert-success"><?php echo $_SESSION['successmsg']; unset($_SESSION['successmsg']); ?></div>
                                        <?php } ?>
                                        <div class="control-group">
                                            <div class="controls row-fluid">
                                                <input class="span12" type="text" name="username" placeholder="Username" required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="controls row-fluid">
                                                <input class="span12" type="password" name="password" placeholder="Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="module-foot">
                                        <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane" id="regTab">
                            <div class="module module-login">
                                <form class="form-vertical" method="post">
                                    <div class="module-head"><h3>Create Admin Account</h3></div>
                                    <div class="module-body">
                                        <div class="control-group">
                                            <div class="controls row-fluid">
                                                <input class="span12" type="text" name="username" placeholder="New Username" required>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="controls row-fluid">
                                                <input class="span12" type="password" name="password" placeholder="New Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="module-foot">
                                        <button type="submit" name="register" class="btn btn-success btn-block">Register Admin</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> </div>
            </div>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>