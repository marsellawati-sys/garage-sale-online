<?php
session_start();
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | Garage Sale Official</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent: #d4a373; --dark: #111111; --bg: #fdfcfb; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg); color: var(--dark); }
        
        .navbar-custom { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #eee; }
        .hero-section { padding: 120px 0 80px; text-align: center; }
        .hero-title { font-size: 4rem; font-weight: 800; letter-spacing: -2px; line-height: 1; margin-bottom: 25px; }
        .accent-text { color: var(--accent); }

        .about-card { background: #fff; border-radius: 40px; border: 1px solid #eee; padding: 60px; box-shadow: 0 20px 60px rgba(0,0,0,0.02); }
        .vision-box { border-left: 4px solid var(--accent); padding-left: 25px; margin: 40px 0; }
        
        .stat-card { text-align: center; padding: 30px; }
        .stat-number { font-size: 2.5rem; font-weight: 800; display: block; }
        .stat-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: 700; }

        .feature-icon { width: 60px; height: 60px; background: var(--dark); color: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px; }
        .img-rounded { border-radius: 30px; object-fit: cover; width: 100%; height: 450px; }
        
        .btn-shop { background: var(--dark); color: #fff; padding: 15px 40px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: 0.3s; display: inline-block; }
        .btn-shop:hover { transform: translateY(-5px); color: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand fw-800 fs-3" href="index.php" style="font-weight: 800;">Garage Sale.</a>
        <div class="ms-auto">
            <a href="index.php" class="text-dark text-decoration-none fw-bold small">Kembali Belanja</a>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <h1 class="hero-title">Giving Clothes <br>A <span class="accent-text">Second Story.</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">Kami percaya bahwa pakaian terbaik adalah yang sudah ada. Garage Sale hadir untuk mengkurasi koleksi thrift berkualitas demi gaya yang berkelanjutan.</p>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="about-card">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?q=80&w=1000&auto=format&fit=crop" class="img-rounded shadow-lg" alt="Thrifting Store">
                </div>
                <div class="col-lg-6">
                    <h6 class="text-uppercase fw-800 text-muted mb-3 small" style="letter-spacing: 2px;">Cerita Kami</h6>
                    <h2 class="fw-800 mb-4" style="font-size: 2.5rem;">Lebih dari Sekadar Pakaian Bekas.</h2>
                    <p class="text-muted">Dimulai dari sebuah garasi kecil di tahun 2024, Garage Sale lahir dari keresahan akan limbah fashion yang terus meningkat. Kami memilih setiap item secara manual, memastikan kualitas jahitan, bahan, dan orisinalitasnya tetap terjaga.</p>
                    
                    <div class="vision-box">
                        <h5 class="fw-bold mb-2">Visi Kami</h5>
                        <p class="text-muted mb-0">Menjadikan thrifting sebagai gaya hidup utama yang keren, terjangkau, dan ramah lingkungan bagi generasi muda Indonesia.</p>
                    </div>

                    <div class="row mt-5">
                        <div class="col-4 stat-card">
                            <span class="stat-number">5k+</span>
                            <span class="stat-label">Koleksi</span>
                        </div>
                        <div class="col-4 stat-card">
                            <span class="stat-number">2k+</span>
                            <span class="stat-label">Happy User</span>
                        </div>
                        <div class="col-4 stat-card">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Curated</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mx-auto"><i class="fa fa-gem"></i></div>
                    <h5 class="fw-bold">Premium Quality</h5>
                    <p class="small text-muted">Setiap barang melewati proses cuci (laundry) dan sortir ketat sebelum sampai ke tanganmu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mx-auto"><i class="fa fa-leaf"></i></div>
                    <h5 class="fw-bold">Eco-Friendly</h5>
                    <p class="small text-muted">Membeli thrift berarti kamu membantu mengurangi jejak karbon dan limbah tekstil dunia.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mx-auto"><i class="fa fa-tag"></i></div>
                    <h5 class="fw-bold">Fair Price</h5>
                    <p class="small text-muted">Gaya keren tidak harus mahal. Kami memberikan harga terbaik untuk brand-brand ternama.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="index.php" class="btn-shop">Mulai Thrifting Sekarang</a>
        </div>
    </div>
</section>

<footer class="py-5 border-top mt-5">
    <div class="container text-center">
        <p class="small text-muted">© 2026 Garage Sale Official. All Rights Reserved.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#" class="text-dark"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-dark"><i class="fab fa-tiktok"></i></a>
            <a href="#" class="text-dark"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>